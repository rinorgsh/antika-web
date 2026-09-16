<script setup>
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, nextTick, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Eyebrow from '@/Components/Eyebrow.vue';
import { attribution, event as trackEvent } from '@/tracking';

const props = defineProps({
    strings: { type: Object, required: true },
    preselectedType: { type: String, default: null },
    eventTypes: { type: Array, default: () => [] },
    venues: { type: Array, default: () => [] },
    menuFormulas: { type: Array, default: () => [] },
    drinkCategories: { type: Array, default: () => [] },
    extraCategories: { type: Array, default: () => [] },
    legal: { type: Object, default: () => ({}) },
});

const page = usePage();
const site = computed(() => page.props.site);
const locale = computed(() => page.props.locale || 'nl');

/* ------------------------------------------------------------------ */
/* Textes                                                               */
/* ------------------------------------------------------------------ */

// s('step3.too_small', { capacity: 40, guests: 60 })
const s = (key, params = {}) => {
    let text = key.split('.').reduce((v, k) => (v && typeof v === 'object' ? v[k] : undefined), props.strings);
    if (typeof text !== 'string') return key;
    Object.entries(params).forEach(([k, v]) => { text = text.replaceAll(`:${k}`, v); });
    return text;
};

// Pluriels au format Laravel : "{0} Aucune|{1} 1 boisson|[2,*] :count boissons"
const choice = (key, count) => {
    const parts = s(key).split('|');
    for (const part of parts) {
        const m = part.match(/^\s*(\{(\d+)\}|\[(\d+),(\d+|\*)\])\s*(.*)$/);
        if (!m) continue;
        const exact = m[2] !== undefined && Number(m[2]) === count;
        const inRange = m[3] !== undefined && count >= Number(m[3]) && (m[4] === '*' || count <= Number(m[4]));
        if (exact || inRange) return m[5].replaceAll(':count', count);
    }
    return parts[parts.length - 1].replace(/^\s*(\{\d+\}|\[\d+,(\d+|\*)\])\s*/, '').replaceAll(':count', count);
};

const steps = computed(() => props.strings.steps);

onMounted(() => document.body.classList.add('hide-zenchef'));
onBeforeUnmount(() => document.body.classList.remove('hide-zenchef'));

const TOTAL = 6;

/* ------------------------------------------------------------------ */
/* État                                                                 */
/* ------------------------------------------------------------------ */

const step = ref(1);
const furthest = ref(1);
const errors = ref({});
const submitting = ref(false);
const availability = ref({});   // { venueId: true|false } pour la date choisie

const form = reactive({
    event_type_id: null,
    event_date: '',
    event_time_slot: '',
    guest_count: 50,
    venue_ids: [],
    menu_formula_id: null,
    menu_choices: {},       // servi à table : { categoryId: itemId }
    supplements: [],        // buffet : plats à supplément ajoutés
    child_menu: false,
    dietary_requirements: '',
    all_in: [],             // boissons à volonté (ids)
    bottles: {},            // { optionId: quantité }
    aperitif: null,         // option d'apéritif choisie
    extras: {},             // { itemId: quantité }
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    company: '',
    special_requests: '',
    consent: false,
    website: '',            // pot de miel anti-robots (champ caché)
});

// Arrivée depuis une page d'atterrissage (?type=mariage) : type déjà choisi.
const preselected = props.eventTypes.find((t) => t.slug === props.preselectedType);
if (preselected) {
    form.event_type_id = preselected.id;
    step.value = 2;
    furthest.value = 2;
}

/* ------------------------------------------------------------------ */
/* Dérivés                                                              */
/* ------------------------------------------------------------------ */

const minDate = (() => {
    const d = new Date();
    d.setDate(d.getDate() + 1);
    return d.toISOString().slice(0, 10);
})();

const selectedType = computed(() => props.eventTypes.find((t) => t.id === form.event_type_id));
const selectedVenues = computed(() => props.venues.filter((v) => form.venue_ids.includes(v.id)));
const selectedFormula = computed(() => props.menuFormulas.find((f) => f.id === form.menu_formula_id));
const isBuffet = computed(() => selectedFormula.value?.type === 'buffet');

const mainDrinks = computed(() => props.drinkCategories.filter((c) => !c.role));
const bubbles = computed(() => props.drinkCategories.filter((c) => c.role === 'bubbles'));
const aperitifs = computed(() => props.drinkCategories.filter((c) => c.role === 'aperitif').flatMap((c) => c.options));

const drinksCount = computed(() =>
    form.all_in.length + Object.values(form.bottles).filter((q) => q > 0).length + (form.aperitif ? 1 : 0),
);

const dateLabel = computed(() => {
    if (!form.event_date) return '';
    const d = new Date(`${form.event_date}T12:00:00`);
    const tag = { nl: 'nl-BE', fr: 'fr-BE', en: 'en-GB' }[locale.value] || 'nl-BE';
    return d.toLocaleDateString(tag, { day: 'numeric', month: 'long', year: 'numeric' });
});

const summary = computed(() => [
    { label: s('sidebar.type'), value: selectedType.value?.name, done: !!form.event_type_id },
    {
        label: s('sidebar.date'),
        value: form.event_date ? `${dateLabel.value} · ${s('sidebar.guests', { count: form.guest_count })}` : '',
        done: !!(form.event_date && form.event_time_slot),
    },
    { label: s('sidebar.venue'), value: selectedVenues.value.map((v) => v.name).join(', '), done: form.venue_ids.length > 0 },
    { label: s('sidebar.menu'), value: selectedFormula.value?.name, done: !!form.menu_formula_id },
    { label: s('sidebar.drinks'), value: drinksCount.value ? choice('sidebar.drinks_count', drinksCount.value) : '', done: drinksCount.value > 0 },
]);

/* ------------------------------------------------------------------ */
/* Navigation                                                           */
/* ------------------------------------------------------------------ */

const validate = (n) => {
    const e = {};
    if (n === 1 && !form.event_type_id) e.event_type_id = s('errors.type');
    if (n === 2) {
        if (!form.event_date || form.event_date < minDate) e.event_date = s('errors.date');
        if (!form.event_time_slot) e.event_time_slot = s('errors.slot');
        if (!form.guest_count || form.guest_count < 1) e.guest_count = s('errors.guests');
    }
    if (n === 3 && !form.venue_ids.length) e.venue_ids = s('errors.venue');
    if (n === 4 && !form.menu_formula_id) e.menu_formula_id = s('errors.formula');
    if (n === 6) {
        if (![form.first_name, form.last_name, form.email, form.phone].every((v) => v.trim())) e.contact = s('errors.contact');
        else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email.trim())) e.contact = s('errors.email');
        if (!form.consent) e.consent = s('errors.consent');
    }
    errors.value = e;
    return Object.keys(e).length === 0;
};

const scrollTop = () => nextTick(() => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
    // Liste des étapes défilante (mobile) : on amène l'étape active en vue.
    const li = document.querySelector('[data-step-active]')?.parentElement;
    li?.parentElement?.scrollTo({ left: li.offsetLeft - 16, behavior: 'smooth' });
});

const goTo = (n) => {
    if (n > furthest.value || n === step.value) return;
    errors.value = {};
    step.value = n;
    scrollTop();
};

const next = () => {
    if (!validate(step.value)) return;
    if (step.value >= TOTAL) return;
    step.value += 1;
    furthest.value = Math.max(furthest.value, step.value);
    if (step.value === TOTAL) applyDefaultExtras();
    trackEvent('quote_step', { step: step.value });
    scrollTop();
};

const previous = () => {
    if (step.value > 1) {
        errors.value = {};
        step.value -= 1;
        scrollTop();
    }
};

const chooseType = (id) => {
    form.event_type_id = id;
    next();
};

/* ------------------------------------------------------------------ */
/* Étape 2-3 : date, disponibilité des salles                            */
/* ------------------------------------------------------------------ */

const changeGuests = (delta) => {
    form.guest_count = Math.max(1, (Number(form.guest_count) || 0) + delta);
};

watch(() => form.event_date, async (date) => {
    availability.value = {};
    if (!date || date < minDate) return;
    try {
        const { data } = await window.axios.post(route('simulator.availability'), { date });
        if (form.event_date !== date) return;   // une autre date a été choisie entre-temps
        availability.value = data;
        // Une salle déjà prise ce jour-là est désélectionnée.
        form.venue_ids = form.venue_ids.filter((id) => data[id] !== false);
    } catch (e) {
        /* sans réponse, on laisse choisir : le serveur revérifie à l'envoi */
    }
});

const isUnavailable = (venue) => availability.value[venue.id] === false;

const toggleVenue = (venue) => {
    if (isUnavailable(venue)) return;
    const i = form.venue_ids.indexOf(venue.id);
    i >= 0 ? form.venue_ids.splice(i, 1) : form.venue_ids.push(venue.id);
};

/* ------------------------------------------------------------------ */
/* Étape 4 : menu                                                       */
/* ------------------------------------------------------------------ */

const chooseFormula = (id) => {
    if (form.menu_formula_id === id) return;
    form.menu_formula_id = id;
    form.menu_choices = {};
    form.supplements = [];
};

const toggleSupplement = (id) => {
    const i = form.supplements.indexOf(id);
    i >= 0 ? form.supplements.splice(i, 1) : form.supplements.push(id);
};

/* ------------------------------------------------------------------ */
/* Étape 5 : boissons                                                   */
/* ------------------------------------------------------------------ */

const toggleAllIn = (id) => {
    const i = form.all_in.indexOf(id);
    i >= 0 ? form.all_in.splice(i, 1) : form.all_in.push(id);
};

const bottleQty = (id) => form.bottles[id] || 0;
const changeBottles = (id, delta) => {
    form.bottles[id] = Math.max(0, bottleQty(id) + delta);
};

/* ------------------------------------------------------------------ */
/* Étape 6 : extras                                                     */
/* ------------------------------------------------------------------ */

const extraQty = (id) => form.extras[id] || 0;
const isExtraOn = (id) => extraQty(id) > 0;

const allExtras = computed(() => props.extraCategories.flatMap((c) => c.items));

const toggleExtra = (item) => {
    if (item.exclusive_group) {
        // Groupe exclusif (packs) : un seul choix possible, toujours un choisi.
        allExtras.value
            .filter((x) => x.exclusive_group === item.exclusive_group)
            .forEach((x) => { delete form.extras[x.id]; });
        form.extras[item.id] = 1;
        return;
    }
    isExtraOn(item.id) ? delete form.extras[item.id] : (form.extras[item.id] = 1);
};

const changeExtra = (id, delta) => {
    const q = Math.max(0, extraQty(id) + delta);
    q ? (form.extras[id] = q) : delete form.extras[id];
};

let defaultsApplied = false;
const applyDefaultExtras = () => {
    if (defaultsApplied) return;
    defaultsApplied = true;
    allExtras.value.filter((x) => x.is_default).forEach((x) => { form.extras[x.id] = 1; });
};

const openExtraCategories = ref(props.extraCategories.length ? [props.extraCategories[0].id] : []);
const toggleExtraCategory = (id) => {
    const i = openExtraCategories.value.indexOf(id);
    i >= 0 ? openExtraCategories.value.splice(i, 1) : openExtraCategories.value.push(id);
};

/* ------------------------------------------------------------------ */
/* Envoi                                                                */
/* ------------------------------------------------------------------ */

const consentHtml = computed(() => {
    if (!props.legal?.terms && !props.legal?.privacy) return null;
    const link = (url, label) => (url ? `<a href="${url}" target="_blank" rel="noopener" class="underline hover:text-antika-cream">${label}</a>` : label);
    return s('step6.consent_legal', {
        terms: link(props.legal.terms, s('step6.terms')),
        privacy: link(props.legal.privacy, s('step6.privacy')),
    });
});

const submit = () => {
    if (!validate(6) || submitting.value) return;
    submitting.value = true;

    const drinks = [
        ...form.all_in.map((id) => ({ id, quantity: 1 })),
        ...Object.entries(form.bottles).filter(([, q]) => q > 0).map(([id, q]) => ({ id: Number(id), quantity: q })),
        ...(form.aperitif ? [{ id: form.aperitif, quantity: 1 }] : []),
    ];

    router.post(route('simulator.submit'), {
        first_name: form.first_name.trim(),
        last_name: form.last_name.trim(),
        email: form.email.trim(),
        phone: form.phone.trim(),
        company: form.company.trim(),
        event_type_id: form.event_type_id,
        event_date: form.event_date,
        event_time_slot: form.event_time_slot,
        guest_count: form.guest_count,
        child_menu: form.child_menu,
        dietary_requirements: form.dietary_requirements,
        special_requests: form.special_requests,
        venue_ids: form.venue_ids,
        menu_formula_id: form.menu_formula_id,
        menu_choices: isBuffet.value ? form.supplements : Object.values(form.menu_choices),
        drinks,
        extras: Object.entries(form.extras).map(([id, quantity]) => ({ id: Number(id), quantity })),
        tracking: attribution(),
        website: form.website,
    }, {
        preserveScroll: true,
        onError: (serverErrors) => {
            if (serverErrors.venue_ids) {
                errors.value = { venue_ids: serverErrors.venue_ids };
                step.value = 3;
                scrollTop();
            } else {
                errors.value = { contact: Object.values(serverErrors)[0] || s('errors.generic') };
            }
        },
        onFinish: () => { submitting.value = false; },
    });
};

/* ------------------------------------------------------------------ */
/* Icônes des types d'événement (pas d'emoji : SVG au trait)            */
/* ------------------------------------------------------------------ */

const ICONS = {
    heart: 'M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z',
    ring: 'M12 21a6.75 6.75 0 1 0 0-13.5A6.75 6.75 0 0 0 12 21Zm0-13.5L9.75 3h4.5L12 7.5Z',
    cake: 'M12 8.25v-1.5m0 0V4.5m0 2.25h.008M7.5 12.75V18a2.25 2.25 0 0 0 2.25 2.25h4.5A2.25 2.25 0 0 0 16.5 18v-5.25m-9 0a3 3 0 0 1 3-3h3a3 3 0 0 1 3 3m-9 0h9',
    cross: 'M12 3v18m-6-12h12',
    droplet: 'M12 21a6.75 6.75 0 0 0 6.75-6.75C18.75 9 12 3 12 3S5.25 9 5.25 14.25A6.75 6.75 0 0 0 12 21Z',
    briefcase: 'M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0',
    party: 'M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z',
    baby: 'M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75h.008v.008H9.75V9.75Zm4.5 0h.008v.008h-.008V9.75Z',
    flower: 'M12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6Zm0 0V3m0 12v6m-3-9H3m12 0h6',
    award: 'M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0',
    star: 'M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z',
};
const iconPath = (icon) => ICONS[icon] || ICONS.star;

const CHECK = 'm4.5 12.75 6 6 9-13.5';
</script>

<template>
    <Head>
        <title>{{ s('meta.title') }}</title>
        <meta head-key="description" name="description" :content="s('meta.description')" />
    </Head>

    <AppLayout>
        <div class="min-h-screen pb-32 pt-28 sm:pt-32 lg:pb-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6">
                <!-- En-tête -->
                <div class="mb-8 text-center">
                    <Eyebrow center>{{ s('intro.eyebrow') }}</Eyebrow>
                    <h1 class="mt-4 font-serif text-3xl text-antika-cream sm:text-4xl">{{ s('intro.title') }}</h1>
                    <p class="mx-auto mt-3 max-w-xl text-sm text-stone-400 sm:text-base">{{ s('intro.subtitle') }}</p>
                </div>

                <!-- Étapes -->
                <nav class="mb-10" :aria-label="s('nav.step_of', { current: step, total: TOTAL })">
                    <ol class="flex gap-2 overflow-x-auto pb-2 sm:justify-center [scrollbar-width:none]">
                        <li v-for="(label, i) in steps" :key="i" class="shrink-0">
                            <button
                                type="button"
                                class="flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs uppercase tracking-wider transition-colors"
                                :class="[
                                    step === i + 1 ? 'border-antika-copper bg-antika-copper/10 text-antika-cream' : 'border-white/10 text-stone-400',
                                    i + 1 <= furthest && step !== i + 1 ? 'hover:border-white/30 hover:text-stone-200' : '',
                                    i + 1 > furthest ? 'cursor-default opacity-40' : '',
                                ]"
                                :disabled="i + 1 > furthest"
                                :data-step-active="step === i + 1 ? '' : null"
                                @click="goTo(i + 1)"
                            >
                                <span class="flex h-5 w-5 items-center justify-center rounded-full text-[10px]" :class="i + 1 < step || (i + 1 <= furthest && step !== i + 1) ? 'bg-antika-copper text-white' : 'border border-current'">
                                    <svg v-if="i + 1 < step" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" :d="CHECK" /></svg>
                                    <template v-else>{{ i + 1 }}</template>
                                </span>
                                {{ label }}
                            </button>
                        </li>
                    </ol>
                    <div class="mx-auto mt-3 h-px max-w-2xl bg-white/10">
                        <div class="h-px bg-antika-copper transition-all duration-500" :style="{ width: `${((step - 1) / (TOTAL - 1)) * 100}%` }"></div>
                    </div>
                </nav>

                <div class="flex gap-10">
                    <div class="min-w-0 flex-1">
                        <transition name="step" mode="out-in">
                            <!-- ============ 1. TYPE ============ -->
                            <section v-if="step === 1" key="1">
                                <h2 class="step-title">{{ s('step1.title') }}</h2>
                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4">
                                    <button
                                        v-for="type in eventTypes"
                                        :key="type.id"
                                        type="button"
                                        class="card group p-4 text-left sm:p-6"
                                        :class="form.event_type_id === type.id ? 'card-on' : ''"
                                        @click="chooseType(type.id)"
                                    >
                                        <span class="flex h-11 w-11 items-center justify-center rounded-full border border-antika-copper/40 text-antika-copper transition-colors group-hover:border-antika-copper">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" :d="iconPath(type.icon)" /></svg>
                                        </span>
                                        <span class="mt-4 block font-serif text-base text-antika-cream sm:text-lg">{{ type.name }}</span>
                                        <span v-if="type.description" class="mt-1 hidden text-sm leading-relaxed text-stone-400 sm:block">{{ type.description }}</span>
                                    </button>
                                </div>
                            </section>

                            <!-- ============ 2. DATE & INVITÉS ============ -->
                            <section v-else-if="step === 2" key="2" class="max-w-2xl">
                                <h2 class="step-title">{{ s('step2.title') }}</h2>

                                <label class="field-label" for="event-date">{{ s('step2.date') }}</label>
                                <input id="event-date" v-model="form.event_date" type="date" :min="minDate" class="field" />
                                <p v-if="errors.event_date" class="field-error">{{ errors.event_date }}</p>

                                <p class="field-label mt-7">{{ s('step2.slot') }}</p>
                                <div class="grid grid-cols-3 gap-2 sm:gap-3">
                                    <button
                                        v-for="slot in ['day', 'evening', 'full_day']"
                                        :key="slot"
                                        type="button"
                                        class="card px-2 py-3.5 text-center text-sm text-stone-200"
                                        :class="form.event_time_slot === slot ? 'card-on text-antika-cream' : ''"
                                        @click="form.event_time_slot = slot"
                                    >{{ s(`slots.${slot}`) }}</button>
                                </div>
                                <p v-if="errors.event_time_slot" class="field-error">{{ errors.event_time_slot }}</p>

                                <label class="field-label mt-7" for="guests">{{ s('step2.guests') }}</label>
                                <div class="flex items-center justify-center gap-2 sm:justify-start sm:gap-3">
                                    <button type="button" class="stepper" :aria-label="`${s('step2.less')} 10`" @click="changeGuests(-10)">−10</button>
                                    <button type="button" class="stepper" :aria-label="s('step2.less')" @click="changeGuests(-1)">−</button>
                                    <input
                                        id="guests"
                                        v-model.number="form.guest_count"
                                        type="number"
                                        min="1"
                                        inputmode="numeric"
                                        class="w-24 border-0 border-b border-white/20 bg-transparent text-center font-serif text-4xl text-antika-cream focus:border-antika-copper focus:ring-0 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none"
                                        @blur="form.guest_count = Math.max(1, Number(form.guest_count) || 1)"
                                    />
                                    <button type="button" class="stepper" :aria-label="s('step2.more')" @click="changeGuests(1)">+</button>
                                    <button type="button" class="stepper" :aria-label="`${s('step2.more')} 10`" @click="changeGuests(10)">+10</button>
                                </div>
                                <p v-if="errors.guest_count" class="field-error">{{ errors.guest_count }}</p>
                            </section>

                            <!-- ============ 3. ESPACE ============ -->
                            <section v-else-if="step === 3" key="3">
                                <h2 class="step-title mb-1">{{ s('step3.title') }}</h2>
                                <p class="mb-6 text-sm text-stone-400">{{ s('step3.subtitle') }}</p>
                                <p v-if="errors.venue_ids" class="field-error mb-4">{{ errors.venue_ids }}</p>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <button
                                        v-for="venue in venues"
                                        :key="venue.id"
                                        type="button"
                                        class="card group overflow-hidden text-left"
                                        :class="[form.venue_ids.includes(venue.id) ? 'card-on' : '', isUnavailable(venue) ? 'cursor-not-allowed opacity-50' : '']"
                                        :disabled="isUnavailable(venue)"
                                        @click="toggleVenue(venue)"
                                    >
                                        <div class="relative aspect-[16/10] overflow-hidden bg-black/40">
                                            <img v-if="venue.image" :src="venue.image" :alt="venue.name" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105" />
                                            <span v-if="form.venue_ids.includes(venue.id)" class="tick absolute left-3 top-3"><svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" :d="CHECK" /></svg></span>
                                            <span v-if="venue.capacity" class="absolute right-3 top-3 rounded-full bg-antika-ink/80 px-2.5 py-1 text-xs text-stone-200">{{ s('step3.capacity', { count: venue.capacity }) }}</span>
                                            <span v-if="isUnavailable(venue)" class="absolute inset-x-3 bottom-3 bg-antika-ink/90 px-3 py-2 text-center text-xs text-stone-200">{{ s('step3.unavailable') }}</span>
                                            <span v-else-if="venue.capacity && form.guest_count > venue.capacity" class="absolute inset-x-3 bottom-3 bg-amber-500/90 px-3 py-2 text-center text-xs font-medium text-antika-ink">{{ s('step3.too_small', { capacity: venue.capacity, guests: form.guest_count }) }}</span>
                                        </div>
                                        <div class="p-5">
                                            <h3 class="font-serif text-lg text-antika-cream">{{ venue.name }}</h3>
                                            <p v-if="venue.description" class="mt-1.5 line-clamp-2 text-sm leading-relaxed text-stone-400">{{ venue.description }}</p>
                                            <p class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs uppercase tracking-wider text-stone-500">
                                                <span v-if="venue.has_parking">{{ s('step3.parking') }}</span>
                                                <span v-if="venue.has_vestiaire">{{ s('step3.cloakroom') }}</span>
                                                <span v-if="venue.has_private_toilets">{{ s('step3.toilets') }}</span>
                                            </p>
                                        </div>
                                    </button>
                                </div>
                            </section>

                            <!-- ============ 4. MENU ============ -->
                            <section v-else-if="step === 4" key="4">
                                <h2 class="step-title mb-1">{{ s('step4.title') }}</h2>
                                <p class="mb-6 text-sm text-stone-400">{{ s('step4.subtitle') }}</p>
                                <p v-if="errors.menu_formula_id" class="field-error mb-4">{{ errors.menu_formula_id }}</p>

                                <div class="-mx-4 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-2 [scrollbar-width:none] sm:mx-0 sm:grid sm:grid-cols-2 sm:overflow-visible sm:px-0 lg:grid-cols-3">
                                    <button
                                        v-for="formula in menuFormulas"
                                        :key="formula.id"
                                        type="button"
                                        class="card w-[78vw] shrink-0 snap-center overflow-hidden text-left sm:w-auto"
                                        :class="form.menu_formula_id === formula.id ? 'card-on' : ''"
                                        @click="chooseFormula(formula.id)"
                                    >
                                        <div class="relative h-44 bg-black/40">
                                            <img v-if="formula.image" :src="formula.image" :alt="formula.name" loading="lazy" class="h-full w-full object-cover" />
                                            <div class="absolute inset-0 bg-gradient-to-t from-antika-ink via-antika-ink/30 to-transparent"></div>
                                            <span v-if="form.menu_formula_id === formula.id" class="tick absolute left-3 top-3"><svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" :d="CHECK" /></svg></span>
                                            <span class="absolute right-3 top-3 rounded-full bg-antika-ink/80 px-2.5 py-1 text-[11px] uppercase tracking-wider text-stone-200">{{ formula.type === 'buffet' ? s('step4.buffet') : s('step4.seated') }}</span>
                                            <h3 class="absolute inset-x-4 bottom-3 font-serif text-xl text-antika-cream">{{ formula.name }}</h3>
                                        </div>
                                        <p v-if="formula.description" class="line-clamp-2 p-4 text-sm leading-relaxed text-stone-400">{{ formula.description }}</p>
                                    </button>
                                </div>

                                <!-- Détail de la formule choisie -->
                                <div v-if="selectedFormula" class="mt-8 border border-white/10 bg-antika-panel p-5 sm:p-8">
                                    <div class="text-center">
                                        <h3 class="font-serif text-2xl text-antika-cream sm:text-3xl">{{ selectedFormula.name }}</h3>
                                        <div class="mx-auto mt-3 h-px w-16 bg-antika-copper"></div>
                                    </div>

                                    <div v-for="category in selectedFormula.categories" :key="category.id" class="mt-8">
                                        <div class="mb-4 text-center">
                                            <h4 class="text-xs uppercase tracking-[0.25em] text-antika-copper">{{ category.name }}</h4>
                                            <p class="mt-1 text-xs text-stone-500">{{ isBuffet ? choice('step4.dishes', category.items.length) : s('step4.pick_one') }}</p>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                                            <component
                                                :is="isBuffet ? 'div' : 'button'"
                                                v-for="item in category.items"
                                                :key="item.id"
                                                :type="isBuffet ? undefined : 'button'"
                                                class="overflow-hidden border text-left transition-colors"
                                                :class="!isBuffet && form.menu_choices[category.id] === item.id ? 'border-antika-copper' : 'border-white/10 hover:border-white/25'"
                                                @click="!isBuffet && (form.menu_choices[category.id] = item.id)"
                                            >
                                                <div class="relative aspect-square bg-black/40">
                                                    <img v-if="item.image" :src="item.image" :alt="item.name" loading="lazy" class="h-full w-full object-cover" />
                                                    <span v-if="!isBuffet && form.menu_choices[category.id] === item.id" class="tick absolute right-2 top-2"><svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" :d="CHECK" /></svg></span>
                                                    <template v-if="item.has_supplement">
                                                        <button
                                                            v-if="isBuffet"
                                                            type="button"
                                                            class="absolute left-2 top-2 rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider"
                                                            :class="form.supplements.includes(item.id) ? 'bg-antika-copper text-white' : 'bg-antika-ink/85 text-antika-cream'"
                                                            @click="toggleSupplement(item.id)"
                                                        >{{ s('step4.supplement') }}{{ form.supplements.includes(item.id) ? '' : ` · ${s('step4.add_supplement')}` }}</button>
                                                        <span v-else class="absolute left-2 top-2 rounded-full bg-antika-ink/85 px-2.5 py-1 text-[10px] uppercase tracking-wider text-antika-cream">{{ s('step4.supplement') }}</span>
                                                    </template>
                                                </div>
                                                <div class="p-2.5 text-center">
                                                    <p class="text-sm leading-tight text-stone-200">{{ item.name }}</p>
                                                    <p v-if="item.description" class="mt-0.5 text-xs leading-tight text-stone-500">{{ item.description }}</p>
                                                </div>
                                            </component>
                                        </div>
                                    </div>

                                    <label class="mt-8 flex cursor-pointer items-center justify-center gap-3 border-t border-white/10 pt-6 text-sm text-stone-200">
                                        <input v-model="form.child_menu" type="checkbox" class="h-5 w-5 rounded border-white/30 bg-transparent text-antika-copper focus:ring-antika-copper/40" />
                                        {{ s('step4.child_menu') }}
                                    </label>
                                </div>

                                <div v-if="selectedFormula" class="mx-auto mt-6 max-w-xl">
                                    <label class="field-label" for="dietary">{{ s('step4.dietary') }}</label>
                                    <textarea id="dietary" v-model="form.dietary_requirements" rows="2" class="field resize-none" :placeholder="s('step4.dietary_placeholder')"></textarea>
                                </div>
                            </section>

                            <!-- ============ 5. BOISSONS ============ -->
                            <section v-else-if="step === 5" key="5">
                                <h2 class="step-title mb-1">{{ s('step5.title') }}</h2>
                                <p class="mb-6 text-sm text-stone-400">{{ s('step5.subtitle') }}</p>

                                <div class="mb-6 border-l-2 border-antika-copper bg-antika-copper/5 px-5 py-4">
                                    <p class="font-serif text-lg text-antika-cream">{{ s('step5.all_in_title') }}</p>
                                    <p class="mt-1 text-sm text-stone-400">{{ s('step5.all_in_text') }}</p>
                                </div>

                                <div class="space-y-4">
                                    <div v-for="category in mainDrinks" :key="category.id" class="border border-white/10 bg-antika-panel p-4 sm:p-5">
                                        <h3 class="mb-2 text-xs uppercase tracking-[0.2em] text-antika-copper">{{ category.name }}</h3>
                                        <div v-for="option in category.options" :key="option.id" class="flex items-center gap-3 border-b border-white/5 py-3 last:border-0">
                                            <img v-if="option.image" :src="option.image" :alt="option.name" loading="lazy" class="h-12 w-12 shrink-0 object-cover" />
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm text-stone-100">{{ option.name }}</p>
                                                <p class="text-[11px] uppercase tracking-wider text-stone-500">{{ option.unit_type === 'bottle' ? s('step5.per_bottle') : s('step5.all_in') }}</p>
                                            </div>
                                            <div v-if="option.unit_type === 'bottle'" class="flex items-center gap-2">
                                                <button type="button" class="stepper-sm" @click="changeBottles(option.id, -1)">−</button>
                                                <span class="w-6 text-center text-sm tabular-nums text-antika-cream">{{ bottleQty(option.id) }}</span>
                                                <button type="button" class="stepper-sm" @click="changeBottles(option.id, 1)">+</button>
                                            </div>
                                            <button v-else type="button" role="switch" :aria-checked="form.all_in.includes(option.id)" class="switch" :class="form.all_in.includes(option.id) ? 'switch-on' : ''" @click="toggleAllIn(option.id)"><span></span></button>
                                        </div>
                                    </div>

                                    <div v-for="category in bubbles" :key="category.id" class="border border-white/10 bg-antika-panel p-4 sm:p-5">
                                        <h3 class="mb-2 text-xs uppercase tracking-[0.2em] text-antika-copper">{{ category.name || s('step5.bubbles') }}</h3>
                                        <div v-for="option in category.options" :key="option.id" class="flex items-center gap-3 border-b border-white/5 py-3 last:border-0">
                                            <img v-if="option.image" :src="option.image" :alt="option.name" loading="lazy" class="h-12 w-12 shrink-0 object-cover" />
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm text-stone-100">{{ option.name }}</p>
                                                <p class="text-[11px] uppercase tracking-wider text-stone-500">{{ s('step5.per_bottle') }}</p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button type="button" class="stepper-sm" @click="changeBottles(option.id, -1)">−</button>
                                                <span class="w-6 text-center text-sm tabular-nums text-antika-cream">{{ bottleQty(option.id) }}</span>
                                                <button type="button" class="stepper-sm" @click="changeBottles(option.id, 1)">+</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="aperitifs.length" class="border border-white/10 bg-antika-panel p-4 sm:p-5">
                                        <h3 class="mb-3 text-xs uppercase tracking-[0.2em] text-antika-copper">{{ s('step5.aperitif') }}</h3>
                                        <div class="space-y-2">
                                            <button
                                                v-for="option in aperitifs"
                                                :key="option.id"
                                                type="button"
                                                class="flex w-full items-center gap-3 border p-3 text-left transition-colors"
                                                :class="form.aperitif === option.id ? 'border-antika-copper bg-antika-copper/5' : 'border-white/10 hover:border-white/25'"
                                                @click="form.aperitif = form.aperitif === option.id ? null : option.id"
                                            >
                                                <img v-if="option.image" :src="option.image" :alt="option.name" loading="lazy" class="h-12 w-12 shrink-0 object-cover" />
                                                <span class="min-w-0 flex-1">
                                                    <span class="block text-sm text-stone-100">{{ option.name }}</span>
                                                    <span v-if="option.description" class="block text-xs text-stone-500">{{ option.description }}</span>
                                                </span>
                                                <span class="radio" :class="form.aperitif === option.id ? 'radio-on' : ''"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <!-- ============ 6. COORDONNÉES + EXTRAS ============ -->
                            <section v-else key="6">
                                <div class="border border-antika-copper/40 bg-antika-panel p-5 sm:p-10">
                                    <div class="text-center">
                                        <Eyebrow center>{{ s('step6.badge') }}</Eyebrow>
                                        <h2 class="mt-4 font-serif text-3xl text-antika-cream sm:text-4xl">{{ s('step6.title') }}</h2>
                                        <p class="mx-auto mt-3 max-w-lg text-sm text-stone-400">{{ s('step6.text') }}</p>
                                    </div>

                                    <div class="mx-auto mt-8 grid max-w-xl grid-cols-2 gap-3">
                                        <input v-model="form.first_name" type="text" class="field" :placeholder="`${s('step6.first_name')} *`" autocomplete="given-name" />
                                        <input v-model="form.last_name" type="text" class="field" :placeholder="`${s('step6.last_name')} *`" autocomplete="family-name" />
                                        <input v-model="form.email" type="email" class="field col-span-2" :placeholder="`${s('step6.email')} *`" autocomplete="email" />
                                        <input v-model="form.phone" type="tel" class="field col-span-2" :placeholder="`${s('step6.phone')} *`" autocomplete="tel" />
                                        <input v-model="form.company" type="text" class="field col-span-2" :placeholder="s('step6.company')" autocomplete="organization" />
                                        <textarea v-model="form.special_requests" rows="3" class="field col-span-2 resize-none" :placeholder="`${s('step6.requests')} ${s('step6.requests_placeholder')}`"></textarea>
                                        <!-- Pot de miel : invisible pour les humains -->
                                        <input v-model="form.website" type="text" name="website" tabindex="-1" autocomplete="off" class="absolute -left-[9999px] h-0 w-0 opacity-0" aria-hidden="true" />

                                        <label class="col-span-2 mt-1 flex cursor-pointer items-start gap-3 text-xs leading-relaxed text-stone-400">
                                            <input v-model="form.consent" type="checkbox" class="mt-0.5 h-4 w-4 shrink-0 rounded border-white/30 bg-transparent text-antika-copper focus:ring-antika-copper/40" />
                                            <span v-if="consentHtml" v-html="consentHtml"></span>
                                            <span v-else>{{ s('step6.consent') }}</span>
                                        </label>
                                        <p v-if="errors.contact" class="field-error col-span-2">{{ errors.contact }}</p>
                                        <p v-if="errors.consent" class="field-error col-span-2">{{ errors.consent }}</p>
                                    </div>
                                </div>

                                <div v-if="extraCategories.length" class="mt-10">
                                    <div class="mb-5 text-center">
                                        <h3 class="font-serif text-2xl text-antika-cream">{{ s('step6.extras_title') }}</h3>
                                        <p class="mt-1 text-sm text-stone-400">{{ s('step6.extras_subtitle') }}</p>
                                    </div>
                                    <div class="space-y-3">
                                        <div v-for="category in extraCategories" :key="category.id" class="border border-white/10 bg-antika-panel">
                                            <button type="button" class="flex w-full items-center justify-between p-4 text-left" @click="toggleExtraCategory(category.id)">
                                                <span class="text-xs uppercase tracking-[0.2em] text-antika-copper">{{ category.name }}</span>
                                                <svg class="h-4 w-4 text-stone-400 transition-transform" :class="openExtraCategories.includes(category.id) ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                                            </button>
                                            <div v-if="openExtraCategories.includes(category.id)" class="px-4 pb-2">
                                                <div v-for="item in category.items" :key="item.id" class="flex items-center gap-3 border-t border-white/5 py-3">
                                                    <img v-if="item.image" :src="item.image" :alt="item.name" loading="lazy" class="h-12 w-12 shrink-0 object-cover" />
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-sm text-stone-100">{{ item.name }}</p>
                                                        <p class="text-xs text-stone-500">
                                                            <span v-if="!item.exclusive_group" class="uppercase tracking-wider">{{ s(`step6.${item.price_type}`) }}</span>
                                                            <span v-if="item.description"><template v-if="!item.exclusive_group"> · </template>{{ item.description }}</span>
                                                        </p>
                                                    </div>
                                                    <button v-if="item.exclusive_group" type="button" class="radio" :class="isExtraOn(item.id) ? 'radio-on' : ''" :aria-label="item.name" @click="toggleExtra(item)"></button>
                                                    <div v-else-if="item.price_type === 'per_unit'" class="flex items-center gap-2">
                                                        <button type="button" class="stepper-sm" @click="changeExtra(item.id, -1)">−</button>
                                                        <span class="w-6 text-center text-sm tabular-nums text-antika-cream">{{ extraQty(item.id) }}</span>
                                                        <button type="button" class="stepper-sm" @click="changeExtra(item.id, 1)">+</button>
                                                    </div>
                                                    <button v-else type="button" role="switch" :aria-checked="isExtraOn(item.id)" class="switch" :class="isExtraOn(item.id) ? 'switch-on' : ''" @click="toggleExtra(item)"><span></span></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </transition>

                        <!-- Navigation (ordinateur) -->
                        <div class="mt-10 hidden items-center justify-between border-t border-white/10 pt-6 lg:flex">
                            <button v-if="step > 1" type="button" class="text-xs uppercase tracking-widest text-stone-400 hover:text-antika-cream" @click="previous">← {{ s('nav.previous') }}</button>
                            <span v-else></span>
                            <button v-if="step > 1 && step < TOTAL" type="button" class="btn-primary" @click="next">{{ s('nav.next') }} →</button>
                            <button v-else-if="step === TOTAL" type="button" class="btn-primary" :disabled="submitting" @click="submit">
                                <span v-if="submitting" class="spinner"></span>
                                {{ s('nav.submit') }}
                            </button>
                        </div>
                    </div>

                    <!-- Récapitulatif (ordinateur) -->
                    <aside class="hidden w-72 shrink-0 lg:block">
                        <div class="sticky top-28 border border-white/10 bg-antika-panel p-6">
                            <p class="text-xs uppercase tracking-[0.2em] text-antika-copper">{{ s('sidebar.title') }}</p>
                            <ul class="mt-5 space-y-4">
                                <li v-for="(line, i) in summary" :key="i" class="flex gap-3">
                                    <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[10px]" :class="line.done ? 'bg-antika-copper text-white' : 'border border-white/20 text-stone-500'">
                                        <svg v-if="line.done" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" :d="CHECK" /></svg>
                                        <template v-else>{{ i + 1 }}</template>
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block text-xs text-stone-500">{{ line.label }}</span>
                                        <span v-if="line.value" class="block text-sm text-stone-100">{{ line.value }}</span>
                                    </span>
                                </li>
                            </ul>
                            <a :href="`tel:${site.contact.phone_link}`" class="mt-6 block border-t border-white/10 pt-5 text-center text-sm text-stone-300 hover:text-antika-cream">{{ site.contact.phone }}</a>
                        </div>
                    </aside>
                </div>
            </div>
        </div>

        <!-- Barre du bas (mobile / tablette) -->
        <div class="fixed inset-x-0 bottom-0 z-40 border-t border-white/10 bg-antika-ink/95 backdrop-blur lg:hidden" style="padding-bottom: env(safe-area-inset-bottom, 0px)">
            <div class="mx-auto flex max-w-3xl items-center justify-between gap-3 px-4 py-3">
                <button v-if="step > 1" type="button" class="flex h-11 w-11 items-center justify-center border border-white/15 text-stone-300" :aria-label="s('nav.previous')" @click="previous">←</button>
                <span v-else class="w-11"></span>
                <span class="text-xs uppercase tracking-widest text-stone-500">{{ s('nav.step_of', { current: step, total: TOTAL }) }}</span>
                <button v-if="step === 1" type="button" class="w-11" disabled></button>
                <button v-else-if="step < TOTAL" type="button" class="btn-primary px-6" @click="next">{{ s('nav.next') }}</button>
                <button v-else type="button" class="btn-primary px-6" :disabled="submitting" @click="submit">
                    <span v-if="submitting" class="spinner"></span>
                    {{ s('nav.submit_short') }}
                </button>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.step-title { @apply mb-6 font-serif text-2xl text-antika-cream sm:text-3xl; }
.card { @apply border border-white/10 bg-antika-panel transition-colors hover:border-white/25; }
.card-on { @apply border-antika-copper ring-1 ring-antika-copper/40 hover:border-antika-copper; }
.tick { @apply flex h-7 w-7 items-center justify-center rounded-full bg-antika-copper text-white shadow-lg; }
.field-label { @apply mb-2 block text-xs uppercase tracking-[0.2em] text-stone-400; }
.field { @apply w-full border border-white/15 bg-antika-ink px-4 py-3 text-base text-stone-100 placeholder:text-stone-500 focus:border-antika-copper focus:ring-0; color-scheme: dark; }
.field-error { @apply mt-2 text-sm text-antika-coral; }
.stepper { @apply flex h-12 min-w-[3rem] items-center justify-center border border-white/15 px-2 text-sm text-stone-200 transition-colors hover:border-antika-copper hover:text-antika-cream; }
.stepper-sm { @apply flex h-9 w-9 items-center justify-center border border-white/15 text-stone-200 transition-colors hover:border-antika-copper; }
.switch { @apply relative h-7 w-12 shrink-0 rounded-full bg-white/15 transition-colors; }
.switch span { @apply absolute left-1 top-1 h-5 w-5 rounded-full bg-stone-400 transition-all; }
.switch-on { @apply bg-antika-copper; }
.switch-on span { @apply translate-x-5 bg-white; }
.radio { @apply h-5 w-5 shrink-0 rounded-full border-2 border-white/30 transition-colors; }
.radio-on { @apply border-antika-copper bg-antika-copper shadow-[inset_0_0_0_3px_#16110e]; }
.btn-primary { @apply inline-flex items-center justify-center gap-2 rounded-full bg-antika-coral px-8 py-3 text-sm font-semibold uppercase tracking-wide text-white transition-colors hover:bg-antika-copper disabled:cursor-not-allowed disabled:opacity-60; }
.spinner { @apply h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white; }
.step-enter-active, .step-leave-active { transition: opacity 0.25s ease, transform 0.25s ease; }
.step-enter-from { opacity: 0; transform: translateX(12px); }
.step-leave-to { opacity: 0; transform: translateX(-12px); }
</style>
