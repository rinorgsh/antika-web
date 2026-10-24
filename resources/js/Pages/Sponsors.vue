<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, nextTick, reactive, ref, watchEffect } from 'vue';

// Festival 1912 (temporaire) : page autonome, sans la navigation Antika,
// avec ses propres langues (fr, nl, sq) — les textes arrivent en props.
const props = defineProps({
    lang: { type: String, required: true },
    langs: { type: Array, required: true },
    strings: { type: Object, required: true },
});

const s = computed(() => props.strings);
const f = computed(() => props.strings.form);

// Logos dans public/images/festival ; sans fichier, le nom s'affiche en texte.
const partners = [
    { name: 'BABA', logo: '/images/festival/baba.webp', h: 'h-7 sm:h-8' },
    { name: 'ANTIKA', logo: '/images/festival/antika.png', h: 'h-11 sm:h-12' },
    { name: 'AURAE', logo: '/images/festival/aurae.svg', h: 'h-5 sm:h-6' },
    { name: 'VATRA' },
    { name: 'GRUPI RINIA' },
    { name: 'FEMX' },
];
const types = ['sponsor', 'exhibitor', 'sponsor_exhibitor', 'donor'];

const form = reactive({
    participation_type: '',
    name: '',
    organization: '',
    phone: '',
    email: '',
    activity: '',
    promote: '',
    sell: '',
    message: '',
    website: '',
});
const errors = ref({});
const sending = ref(false);
const sent = ref(false);
const formTop = ref(null);

const orgOptional = computed(() => form.participation_type === 'donor');
// La politique de confidentialité du site existe en nl / fr / en.
const privacyHref = computed(() => `/privacy?lang=${props.lang === 'nl' ? 'nl' : 'fr'}`);
const consentHtml = computed(() =>
    f.value.consent.replace(':privacy', `<a href="${privacyHref.value}" class="underline hover:text-white" target="_blank" rel="noopener">${f.value.privacy}</a>`),
);

watchEffect(() => {
    if (typeof document !== 'undefined') document.documentElement.lang = props.lang;
});

const submit = () => {
    if (sending.value) return;
    errors.value = {};
    sending.value = true;

    router.post('/sponsors', { ...form, locale: props.lang }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: async () => {
            sent.value = true;
            await nextTick();
            formTop.value?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        },
        onError: (e) => {
            errors.value = e;
            nextTick(() => document.querySelector('[data-error]')?.scrollIntoView({ behavior: 'smooth', block: 'center' }));
        },
        onFinish: () => { sending.value = false; },
    });
};
</script>

<template>
    <Head :title="s.meta.title" />

    <div class="min-h-screen bg-antika-ink text-stone-200">
        <!-- Barre du haut : nom + langues -->
        <header class="mx-auto flex max-w-5xl items-center justify-between px-4 py-5 sm:px-6">
            <span class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-400">{{ s.hero.title }}</span>
            <nav class="flex items-center gap-1 text-xs font-semibold uppercase tracking-wider">
                <Link
                    v-for="l in langs" :key="l" :href="`/sponsors?lang=${l}`" preserve-scroll
                    class="rounded-full px-3 py-1.5 transition-colors"
                    :class="l === lang ? 'bg-festival text-white' : 'text-stone-400 hover:text-white'"
                    :aria-current="l === lang ? 'true' : undefined" :title="s.lang_names[l]"
                >{{ l === 'sq' ? 'AL' : l.toUpperCase() }}</Link>
            </nav>
        </header>

        <!-- En-tête -->
        <section class="relative overflow-hidden">
            <div class="pointer-events-none absolute inset-x-0 -top-10 select-none text-center font-serif text-[38vw] leading-none text-white/[0.03] sm:text-[22rem]" aria-hidden="true">1912</div>
            <div class="relative mx-auto max-w-3xl px-4 pb-16 pt-10 text-center sm:px-6 sm:pt-16">
                <span class="mx-auto block h-1 w-16 bg-festival"></span>
                <h1 class="mt-8 font-serif text-5xl text-antika-cream sm:text-7xl">{{ s.hero.title }}</h1>
                <p class="mt-4 text-sm font-semibold uppercase tracking-[0.25em] text-festival-light">{{ s.hero.subtitle }}</p>
                <p class="mx-auto mt-6 max-w-xl leading-relaxed text-stone-300">{{ s.hero.text }}</p>
                <a href="#formulaire" class="mt-9 inline-flex items-center gap-2 rounded-full bg-festival px-8 py-3.5 text-sm font-semibold uppercase tracking-wide text-white transition-colors hover:bg-festival-dark">
                    {{ s.hero.cta }}
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                </a>
            </div>
        </section>

        <!-- Partenaires -->
        <section class="border-y border-white/10 bg-antika-panel">
            <div class="mx-auto flex max-w-5xl flex-wrap items-center justify-center gap-x-8 gap-y-3 px-4 py-6 sm:px-6">
                <span class="w-full text-center text-[11px] uppercase tracking-[0.3em] text-stone-500 sm:w-auto">{{ s.partners_title }}</span>
                <template v-for="p in partners" :key="p.name">
                    <img v-if="p.logo" :src="p.logo" :alt="p.name" class="w-auto object-contain" :class="p.h" loading="lazy" />
                    <span v-else class="font-serif text-lg tracking-widest text-antika-cream/80">{{ p.name }}</span>
                </template>
            </div>
        </section>

        <!-- Formulaire -->
        <main id="formulaire" ref="formTop" class="mx-auto max-w-3xl scroll-mt-6 px-4 py-16 sm:px-6">
            <div v-if="sent" class="border border-festival/50 bg-antika-panel px-6 py-14 text-center" role="status">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-festival text-festival-light">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                </span>
                <p class="mt-6 font-serif text-2xl text-antika-cream sm:text-3xl">{{ s.success.title }}</p>
                <p class="mx-auto mt-3 max-w-md text-stone-400">{{ s.success.text }}</p>
            </div>

            <form v-else novalidate class="space-y-12" @submit.prevent="submit">
                <!-- 1. Type de participation -->
                <fieldset>
                    <legend class="sp-title"><span class="sp-num">1</span>{{ f.type_title }} *</legend>
                    <p class="mt-2 text-sm text-stone-400">{{ f.type_hint }}</p>
                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <label
                            v-for="type in types" :key="type"
                            class="flex cursor-pointer gap-3 border p-4 transition-colors"
                            :class="form.participation_type === type ? 'border-festival bg-festival/10' : 'border-white/15 hover:border-white/30'"
                        >
                            <input v-model="form.participation_type" type="radio" name="participation_type" :value="type" class="mt-1 border-white/30 bg-antika-ink text-festival focus:ring-festival focus:ring-offset-antika-ink" />
                            <span>
                                <span class="block font-semibold text-antika-cream">{{ f.types[type].label }}</span>
                                <span class="mt-1 block text-sm leading-snug text-stone-400">{{ f.types[type].text }}</span>
                            </span>
                        </label>
                    </div>
                    <p v-if="errors.participation_type" data-error class="sp-error">{{ errors.participation_type }}</p>
                </fieldset>

                <!-- 2. Coordonnées -->
                <fieldset>
                    <legend class="sp-title"><span class="sp-num">2</span>{{ f.contact_title }}</legend>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <label class="sm:col-span-2">
                            <span class="sp-label">{{ f.name }} *</span>
                            <input v-model="form.name" type="text" class="sp-field" autocomplete="name" />
                            <p v-if="errors.name" data-error class="sp-error">{{ errors.name }}</p>
                        </label>
                        <label class="sm:col-span-2">
                            <span class="sp-label">{{ f.organization }} <template v-if="orgOptional">({{ f.optional }})</template><template v-else>*</template></span>
                            <input v-model="form.organization" type="text" class="sp-field" autocomplete="organization" />
                            <p v-if="errors.organization" data-error class="sp-error">{{ errors.organization }}</p>
                        </label>
                        <label>
                            <span class="sp-label">{{ f.phone }} *</span>
                            <input v-model="form.phone" type="tel" class="sp-field" autocomplete="tel" inputmode="tel" />
                            <p v-if="errors.phone" data-error class="sp-error">{{ errors.phone }}</p>
                        </label>
                        <label>
                            <span class="sp-label">{{ f.email }} *</span>
                            <input v-model="form.email" type="email" class="sp-field" autocomplete="email" />
                            <p v-if="errors.email" data-error class="sp-error">{{ errors.email }}</p>
                        </label>
                    </div>
                </fieldset>

                <!-- 3. Activité -->
                <fieldset>
                    <legend class="sp-title"><span class="sp-num">3</span>{{ f.activity_title }}</legend>
                    <div class="mt-5 space-y-4">
                        <label v-for="key in ['activity', 'promote', 'sell']" :key="key" class="block">
                            <span class="sp-label normal-case tracking-normal text-sm text-stone-300">{{ f[key] }}</span>
                            <textarea v-model="form[key]" rows="3" class="sp-field resize-y"></textarea>
                            <p v-if="errors[key]" data-error class="sp-error">{{ errors[key] }}</p>
                        </label>
                    </div>
                </fieldset>

                <!-- 4. Informations complémentaires -->
                <fieldset>
                    <legend class="sp-title"><span class="sp-num">4</span>{{ f.extra_title }}</legend>
                    <label class="mt-5 block">
                        <span class="sp-label normal-case tracking-normal text-sm text-stone-300">{{ f.message }}</span>
                        <textarea v-model="form.message" rows="4" class="sp-field resize-y"></textarea>
                        <p v-if="errors.message" data-error class="sp-error">{{ errors.message }}</p>
                    </label>
                </fieldset>

                <!-- Pot de miel : invisible pour les humains -->
                <input v-model="form.website" type="text" name="website" tabindex="-1" autocomplete="off" class="absolute -left-[9999px] h-0 w-0 opacity-0" aria-hidden="true" />

                <!-- Information importante -->
                <aside class="border-l-2 border-festival bg-antika-panel px-5 py-5 text-sm leading-relaxed text-stone-300">
                    <p class="font-semibold uppercase tracking-wider text-antika-cream">{{ s.notice.title }}</p>
                    <p v-for="(line, i) in s.notice.lines" :key="i" class="mt-2">{{ line }}</p>
                </aside>

                <div>
                    <p class="text-xs text-stone-500">{{ f.required_note }}</p>
                    <button type="submit" class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-full bg-festival px-8 py-4 text-sm font-semibold uppercase tracking-wide text-white transition-colors hover:bg-festival-dark disabled:opacity-60" :disabled="sending">
                        <span v-if="sending" class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                        {{ f.submit }}
                    </button>
                    <p class="mt-3 text-center text-xs text-stone-500" v-html="consentHtml"></p>
                </div>
            </form>
        </main>

        <footer class="border-t border-white/10 px-4 py-10 text-center">
            <p class="font-serif text-xl text-antika-cream">{{ s.hero.title }}</p>
            <p class="mx-auto mt-2 max-w-md text-sm italic text-stone-400">{{ s.tagline }}</p>
        </footer>
    </div>
</template>

<style scoped>
.sp-title { @apply flex items-center gap-3 font-serif text-2xl text-antika-cream; }
.sp-num { @apply flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-festival font-sans text-sm font-semibold text-white; }
.sp-label { @apply mb-1.5 block text-[11px] uppercase tracking-wider text-stone-400; }
.sp-field { @apply w-full border border-white/15 bg-antika-panel px-4 py-3 text-base text-stone-100 placeholder:text-stone-500 focus:border-festival focus:ring-0; color-scheme: dark; }
.sp-error { @apply mt-1.5 text-sm text-festival-light; }
</style>
