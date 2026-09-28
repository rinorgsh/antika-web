<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { adsConversion, event as trackEvent, setUserData } from '@/tracking';

const props = defineProps({
    strings: { type: Object, required: true },
    quote: { type: Object, required: true },
    trackConversion: { type: Boolean, default: false },
    whatsapp: { type: String, default: '' },
    userData: { type: Object, default: null },
});

const page = usePage();
const site = computed(() => page.props.site);

const s = (key, params = {}) => {
    let text = key.split('.').reduce((v, k) => (v && typeof v === 'object' ? v[k] : undefined), props.strings) ?? key;
    Object.entries(params).forEach(([k, v]) => { text = text.replaceAll(`:${k}`, v); });
    return text;
};

const whatsappUrl = computed(() =>
    `https://wa.me/${props.whatsapp}?text=${encodeURIComponent(s('confirmation.whatsapp_message', { number: props.quote.number }))}`,
);

onMounted(() => {
    // Une seule fois, juste après l'envoi (pas au rechargement de la page).
    if (!props.trackConversion) return;
    const value = Math.round(props.quote.value || 0);
    setUserData(props.userData || {});
    trackEvent('generate_lead', { currency: 'EUR', value, guests: props.quote.guests });
    adsConversion('ads_quote_label', { value, currency: 'EUR', transaction_id: props.quote.number });
});
</script>

<template>
    <Head :title="s('meta.title')" />

    <AppLayout>
        <section class="flex min-h-screen items-center px-4 pb-20 pt-32">
            <div class="mx-auto w-full max-w-xl text-center">
                <span class="mx-auto flex h-20 w-20 items-center justify-center rounded-full border border-antika-copper text-antika-copper">
                    <svg class="h-9 w-9" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                </span>
                <h1 class="mt-8 font-serif text-3xl leading-tight text-antika-cream sm:text-4xl">{{ s('confirmation.title', { name: quote.first_name }) }}</h1>
                <p class="mt-5 leading-relaxed text-stone-300">{{ s('confirmation.text') }}</p>
                <p class="mt-4 text-xs uppercase tracking-[0.2em] text-stone-500">{{ s('confirmation.reference') }} · <span class="text-antika-cream">{{ quote.number }}</span></p>

                <div class="mt-8 border-l-2 border-antika-copper bg-antika-panel px-5 py-4 text-left">
                    <p class="text-sm font-semibold text-antika-cream">{{ s('confirmation.spam_title') }}</p>
                    <p class="mt-1 text-sm text-stone-400">{{ s('confirmation.spam_text') }}</p>
                </div>

                <div class="mt-8 border border-white/10 bg-antika-panel p-6">
                    <p class="text-stone-300">{{ s('confirmation.question') }}</p>
                    <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                        <a v-if="whatsapp" :href="whatsappUrl" target="_blank" rel="noopener" class="flex-1 rounded-full bg-[#25D366] px-6 py-3 text-sm font-semibold text-white hover:bg-[#1fb857]">{{ s('confirmation.whatsapp') }}</a>
                        <a :href="`tel:${site.contact.phone_link}`" class="flex-1 rounded-full border border-antika-cream/50 px-6 py-3 text-sm font-semibold text-antika-cream hover:bg-antika-cream hover:text-antika-ink">{{ s('confirmation.call') }} · {{ site.contact.phone }}</a>
                    </div>
                </div>

                <div class="mt-8 flex flex-col items-center gap-3 text-sm">
                    <a :href="route('simulator.pdf', quote.number)" class="text-antika-copper hover:text-antika-cream">{{ s('confirmation.download') }}</a>
                    <Link href="/" class="text-stone-500 hover:text-antika-cream">← {{ s('confirmation.back') }}</Link>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
