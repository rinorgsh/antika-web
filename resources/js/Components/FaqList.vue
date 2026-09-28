<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Eyebrow from '@/Components/Eyebrow.vue';

// Questions fréquentes (lang/*/site.php -> faq) : lève les freins avant la demande de devis.
const page = usePage();
const items = computed(() => page.props.translations.faq?.items || []);
</script>

<template>
    <section v-if="items.length" class="py-20">
        <div class="mx-auto max-w-3xl px-6">
            <div v-reveal class="text-center">
                <Eyebrow center>{{ $t('faq.eyebrow') }}</Eyebrow>
            </div>
            <div class="mt-10 divide-y divide-white/10 border-y border-white/10">
                <details v-for="(item, i) in items" :key="i" class="group py-5">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-left font-serif text-lg text-antika-cream [&::-webkit-details-marker]:hidden">
                        {{ item.q }}
                        <svg class="h-5 w-5 shrink-0 text-antika-copper transition-transform group-open:rotate-45" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    </summary>
                    <p class="mt-3 leading-relaxed text-stone-400">{{ item.a }}</p>
                </details>
            </div>
        </div>
    </section>
</template>
