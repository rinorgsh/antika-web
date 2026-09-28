<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';
import { t } from '@/i18n';
import { adsConversion, attribution, event as trackEvent, setUserData } from '@/tracking';

// Formulaire court « Bel mij terug » : 2 champs obligatoires, pour ceux que
// les 6 étapes du simulateur découragent (et le mobile).
const props = defineProps({
    eventTypeId: { type: Number, default: null },
    source: { type: String, default: '' },
});

const page = usePage();
const site = computed(() => page.props.site);
const privacyUrl = computed(() => page.props.marketing?.privacy_url || '/privacy');

const tomorrow = (() => {
    const d = new Date();
    d.setDate(d.getDate() + 1);
    return d.toISOString().slice(0, 10);
})();

const form = reactive({ name: '', phone: '', email: '', event_date: '', guest_count: '', message: '', website: '' });
const errors = ref({});
const sending = ref(false);
const sent = ref(false);

const consentHtml = computed(() =>
    t('callback.consent').replace(':privacy', `<a href="${privacyUrl.value}" class="underline hover:text-antika-cream">${t('callback.privacy')}</a>`),
);

const submit = () => {
    if (sending.value) return;
    errors.value = {};
    sending.value = true;

    router.post('/events/callback', {
        ...form,
        guest_count: form.guest_count || null,
        event_date: form.event_date || null,
        event_type_id: props.eventTypeId,
        source_page: props.source || window.location.pathname,
        tracking: attribution(),
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            sent.value = true;
            trackEvent('generate_lead', { lead_type: 'callback', guests: Number(form.guest_count) || undefined });
            setUserData({ email: form.email, phone: form.phone });
            adsConversion('ads_lead_label');
        },
        onError: (e) => { errors.value = e; },
        onFinish: () => { sending.value = false; },
    });
};
</script>

<template>
    <div class="border border-antika-copper/40 bg-antika-panel p-6 sm:p-8">
        <div v-if="sent" class="py-6 text-center" role="status">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-antika-copper text-antika-copper">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
            </span>
            <p class="mt-5 font-serif text-2xl text-antika-cream">{{ $t('callback.success_title') }}</p>
            <p class="mt-2 text-sm text-stone-400">{{ $t('callback.success_text').replace(':phone', site.contact.phone) }}</p>
        </div>

        <form v-else novalidate @submit.prevent="submit">
            <p class="font-serif text-2xl text-antika-cream">{{ $t('callback.title') }}</p>
            <p class="mt-2 text-sm leading-relaxed text-stone-400">{{ $t('callback.text') }}</p>

            <div class="mt-6 grid grid-cols-2 gap-3">
                <input v-model="form.name" type="text" required class="cb-field col-span-2 sm:col-span-1" :placeholder="`${$t('callback.name')} *`" :aria-label="$t('callback.name')" autocomplete="name" />
                <input v-model="form.phone" type="tel" required class="cb-field col-span-2 sm:col-span-1" :placeholder="`${$t('callback.phone')} *`" :aria-label="$t('callback.phone')" autocomplete="tel" inputmode="tel" />
                <label class="col-span-1">
                    <span class="mb-1 block text-[11px] uppercase tracking-wider text-stone-500">{{ $t('callback.date') }}</span>
                    <input v-model="form.event_date" type="date" :min="tomorrow" class="cb-field" />
                </label>
                <label class="col-span-1">
                    <span class="mb-1 block text-[11px] uppercase tracking-wider text-stone-500">{{ $t('callback.guests') }}</span>
                    <input v-model="form.guest_count" type="number" min="1" inputmode="numeric" class="cb-field" />
                </label>
                <input v-model="form.email" type="email" class="cb-field col-span-2" :placeholder="$t('callback.email')" :aria-label="$t('callback.email')" autocomplete="email" />
                <textarea v-model="form.message" rows="2" class="cb-field col-span-2 resize-none" :placeholder="$t('callback.message')" :aria-label="$t('callback.message')"></textarea>
                <!-- Pot de miel : invisible pour les humains -->
                <input v-model="form.website" type="text" name="website" tabindex="-1" autocomplete="off" class="absolute -left-[9999px] h-0 w-0 opacity-0" aria-hidden="true" />
            </div>

            <p v-if="Object.keys(errors).length" class="mt-3 text-sm text-antika-coral">{{ Object.values(errors)[0] || $t('callback.errors.generic') }}</p>

            <button type="submit" class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-full bg-antika-coral px-8 py-3.5 text-sm font-semibold uppercase tracking-wide text-white transition-colors hover:bg-antika-copper disabled:opacity-60" :disabled="sending">
                <span v-if="sending" class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                {{ $t('callback.submit') }}
            </button>
            <p class="mt-3 text-center text-xs text-stone-500" v-html="consentHtml"></p>
        </form>
    </div>
</template>

<style scoped>
.cb-field { @apply w-full border border-white/15 bg-antika-ink px-4 py-3 text-base text-stone-100 placeholder:text-stone-500 focus:border-antika-copper focus:ring-0; color-scheme: dark; }
</style>
