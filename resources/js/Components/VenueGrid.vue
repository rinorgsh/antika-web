<script setup>
import Eyebrow from '@/Components/Eyebrow.vue';

// Les salles (photo, capacité, équipements) : ce qu'un organisateur veut voir avant tout.
defineProps({
    venues: { type: Array, default: () => [] },
});
</script>

<template>
    <section v-if="venues.length" class="bg-antika-panel py-20">
        <div class="mx-auto max-w-7xl px-6">
            <div v-reveal class="text-center">
                <Eyebrow center>{{ $t('venues.eyebrow') }}</Eyebrow>
                <h2 class="mt-5 font-serif text-3xl text-antika-cream sm:text-4xl">{{ $t('venues.title') }}</h2>
                <p class="mx-auto mt-4 max-w-2xl text-stone-400">{{ $t('venues.text') }}</p>
            </div>
            <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <article v-for="(venue, i) in venues" :key="venue.id" v-reveal="(i % 3) * 100" class="overflow-hidden border border-white/10 bg-antika-ink/40">
                    <div class="relative aspect-[16/10] bg-black/40">
                        <img v-if="venue.image" :src="venue.image" :alt="venue.name" loading="lazy" class="h-full w-full object-cover" />
                        <span v-if="venue.capacity" class="absolute right-3 top-3 rounded-full bg-antika-ink/85 px-3 py-1 text-xs text-antika-cream">{{ $t('venues.seats').replace(':count', venue.capacity) }}</span>
                    </div>
                    <div class="p-5">
                        <h3 class="font-serif text-xl text-antika-cream">{{ venue.name }}</h3>
                        <p v-if="venue.description" class="mt-2 text-sm leading-relaxed text-stone-400">{{ venue.description }}</p>
                        <p class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-[11px] uppercase tracking-wider text-stone-500">
                            <span v-if="venue.has_parking">{{ $t('venues.parking') }}</span>
                            <span v-if="venue.has_vestiaire">{{ $t('venues.cloakroom') }}</span>
                            <span v-if="venue.has_private_toilets">{{ $t('venues.toilets') }}</span>
                        </p>
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>
