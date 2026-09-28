<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Eyebrow from '@/Components/Eyebrow.vue';

// Note Google + avis mis en avant (réglés dans l'admin). Rien d'affiché tant que c'est vide.
const page = usePage();
const m = computed(() => page.props.marketing || {});
const reviews = computed(() => (m.value.reviews || []).slice(0, 3));
const hasRating = computed(() => Boolean(m.value.rating));
const stars = computed(() => Math.round(parseFloat(String(m.value.rating).replace(',', '.')) || 5));
</script>

<template>
    <section v-if="hasRating || reviews.length" class="py-20">
        <div class="mx-auto max-w-6xl px-6">
            <div v-reveal class="flex flex-col items-center text-center">
                <Eyebrow center>{{ $t('reviews.eyebrow') }}</Eyebrow>
                <a
                    v-if="hasRating"
                    :href="m.reviews_url || undefined"
                    target="_blank"
                    rel="noopener"
                    class="mt-6 inline-flex flex-wrap items-center justify-center gap-x-3 gap-y-1 text-antika-cream"
                    :class="m.reviews_url ? 'hover:text-antika-copper' : 'pointer-events-none'"
                >
                    <span class="flex gap-0.5 text-antika-copper" aria-hidden="true">
                        <svg v-for="i in 5" :key="i" class="h-5 w-5" :class="i <= stars ? 'opacity-100' : 'opacity-30'" fill="currentColor" viewBox="0 0 20 20"><path d="M9.05 2.93c.3-.92 1.6-.92 1.9 0l1.07 3.29a1 1 0 0 0 .95.69h3.46c.97 0 1.37 1.24.59 1.81l-2.8 2.03a1 1 0 0 0-.36 1.12l1.07 3.29c.3.92-.76 1.69-1.54 1.12l-2.8-2.03a1 1 0 0 0-1.18 0l-2.8 2.03c-.78.57-1.83-.2-1.54-1.12l1.07-3.29a1 1 0 0 0-.36-1.12L2.98 8.72c-.78-.57-.38-1.81.59-1.81h3.46a1 1 0 0 0 .95-.69l1.07-3.29Z" /></svg>
                    </span>
                    <span class="font-serif text-2xl">{{ $t('reviews.rating').replace(':rating', m.rating) }}</span>
                    <span v-if="m.reviews_count" class="text-sm text-stone-400">· {{ $t('reviews.count').replace(':count', m.reviews_count) }}</span>
                </a>
            </div>

            <div v-if="reviews.length" class="mt-12 grid gap-6" :class="{ 1: 'mx-auto max-w-2xl', 2: 'md:grid-cols-2', 3: 'md:grid-cols-3' }[reviews.length]">
                <figure v-for="(r, i) in reviews" :key="i" v-reveal="i * 100" class="flex flex-col border border-white/10 bg-antika-panel p-7">
                    <blockquote class="flex-1 font-serif text-lg italic leading-relaxed text-antika-cream">&bdquo;{{ r.text }}&rdquo;</blockquote>
                    <figcaption class="mt-5 text-xs uppercase tracking-widest text-stone-400">
                        {{ r.author }}<span v-if="r.occasion" class="text-stone-500"> · {{ r.occasion }}</span>
                    </figcaption>
                </figure>
            </div>

            <p v-if="m.reviews_url && reviews.length" class="mt-8 text-center">
                <a :href="m.reviews_url" target="_blank" rel="noopener" class="border-b border-antika-copper pb-1 text-xs font-semibold uppercase tracking-widest text-antika-cream hover:text-antika-copper">{{ $t('reviews.link') }}</a>
            </p>
        </div>
    </section>
</template>
