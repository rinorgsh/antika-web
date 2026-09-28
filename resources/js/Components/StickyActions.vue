<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// Barre d'actions fixe en bas de l'écran (mobile) : appeler, WhatsApp, devis.
const props = defineProps({
    quoteUrl: { type: String, default: '/events/simulator' },
});

const page = usePage();
const site = computed(() => page.props.site);
const whatsapp = computed(() => page.props.marketing?.whatsapp);
</script>

<template>
    <div class="fixed inset-x-0 bottom-0 z-40 border-t border-white/10 bg-antika-ink/95 backdrop-blur lg:hidden" style="padding-bottom: env(safe-area-inset-bottom, 0px)">
        <div class="grid gap-2 px-3 py-2.5" :class="whatsapp ? 'grid-cols-3' : 'grid-cols-2'">
            <a :href="`tel:${site.contact.phone_link}`" class="flex items-center justify-center gap-2 border border-white/15 py-3 text-xs font-semibold uppercase tracking-wider text-antika-cream">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                {{ $t('sticky.call') }}
            </a>
            <a v-if="whatsapp" :href="`https://wa.me/${whatsapp}`" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 border border-white/15 py-3 text-xs font-semibold uppercase tracking-wider text-antika-cream">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.64.07-.3-.15-1.26-.46-2.39-1.47-.88-.79-1.48-1.76-1.65-2.06-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.49 0 1.47 1.07 2.89 1.22 3.09.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.09 1.76-.72 2.01-1.41.25-.69.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35ZM12.04 21.5h-.01a9.45 9.45 0 0 1-4.82-1.32l-.35-.2-3.58.94.96-3.49-.23-.36a9.43 9.43 0 0 1-1.45-5.03C2.56 6.83 6.8 2.6 12.04 2.6a9.4 9.4 0 0 1 6.7 2.78 9.4 9.4 0 0 1 2.77 6.7c0 5.23-4.25 9.42-9.47 9.42Zm8.06-17.48A11.3 11.3 0 0 0 12.04.7C5.76.7.66 5.8.66 12.07c0 2 .52 3.96 1.52 5.69L.57 23.3l5.67-1.49a11.36 11.36 0 0 0 5.8 1.48h.01c6.27 0 11.37-5.1 11.38-11.38 0-3.04-1.18-5.9-3.33-8.05Z" /></svg>
                {{ $t('sticky.whatsapp') }}
            </a>
            <Link :href="props.quoteUrl" class="flex items-center justify-center rounded-full bg-antika-coral py-3 text-xs font-semibold uppercase tracking-wider text-white">
                {{ $t('sticky.quote') }}
            </Link>
        </div>
    </div>
</template>
