<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Eyebrow from '@/Components/Eyebrow.vue';
import HeroCarousel from '@/Components/HeroCarousel.vue';
import { t } from '@/i18n';

const page = usePage();
const site = computed(() => page.props.site);
const tr = computed(() => page.props.translations);
const locale = computed(() => page.props.locale || 'fr');

// Carte interactive multilingue (la même qu'au restaurant) — on ouvre le hub dans la langue du site.
const menuUrl = computed(() => `${site.value.links.menu}/carte.html?lang=${locale.value}`);
</script>

<template>
    <Head :title="t('nav.home')" />

    <AppLayout>
        <!-- HERO carrousel -->
        <HeroCarousel :images="site.hero_images">
            <div class="mx-auto w-full max-w-7xl px-6">
                <div class="max-w-2xl">
                    <Eyebrow>{{ $t('hero.eyebrow') }}</Eyebrow>
                    <h1 class="mt-6 font-serif text-4xl leading-[1.05] text-antika-cream sm:text-6xl lg:text-7xl">{{ $t('hero.title') }}</h1>
                    <p class="mt-6 max-w-xl text-base leading-relaxed text-stone-200 sm:text-lg">{{ $t('hero.text') }}</p>
                    <div class="mt-9 flex flex-wrap gap-4">
                        <Link href="/menu" class="rounded-full bg-antika-coral px-7 py-3 text-sm font-medium text-white transition-colors hover:bg-antika-copper">{{ $t('hero.cta_menu') }}</Link>
                        <Link href="/events" class="rounded-full bg-antika-coral px-7 py-3 text-sm font-medium text-white transition-colors hover:bg-antika-copper">{{ $t('hero.cta_events') }}</Link>
                        <button type="button" data-zc-action="open" class="rounded-full border border-antika-cream/50 px-7 py-3 text-sm font-medium text-antika-cream transition-colors hover:bg-antika-cream hover:text-antika-ink">{{ $t('hero.cta_reserve') }}</button>
                    </div>
                </div>
            </div>
        </HeroCarousel>

        <!-- À PROPOS + chiffres -->
        <section class="py-24">
            <div class="mx-auto grid max-w-7xl items-center gap-14 px-6 lg:grid-cols-2">
                <div v-reveal class="relative">
                    <img src="/images/about.jpg" alt="" class="aspect-[4/5] w-full rounded-sm object-cover shadow-2xl" />
                    <div class="absolute -bottom-5 -left-5 hidden h-28 w-28 border border-antika-copper/60 lg:block"></div>
                </div>
                <div v-reveal="120">
                    <Eyebrow>{{ $t('about.eyebrow') }}</Eyebrow>
                    <h2 class="mt-5 font-serif text-4xl leading-tight text-antika-cream sm:text-5xl">{{ $t('about.title') }}</h2>
                    <p class="mt-6 leading-relaxed text-stone-400">{{ $t('about.text') }}</p>

                    <div class="mt-10 grid grid-cols-3 gap-4 border-t border-white/10 pt-8">
                        <div>
                            <p class="font-serif text-3xl text-antika-copper sm:text-4xl">{{ site.stats.seats }}</p>
                            <p class="mt-1 text-xs uppercase tracking-wide text-stone-400">{{ $t('stats.seats') }}</p>
                        </div>
                        <div>
                            <p class="font-serif text-3xl text-antika-copper sm:text-4xl">{{ site.stats.event_capacity }}</p>
                            <p class="mt-1 text-xs uppercase tracking-wide text-stone-400">{{ $t('stats.capacity') }}</p>
                        </div>
                        <div>
                            <p class="font-serif text-3xl text-antika-copper sm:text-4xl">{{ site.stats.years }}</p>
                            <p class="mt-1 text-xs uppercase tracking-wide text-stone-400">{{ $t('stats.years') }}</p>
                        </div>
                    </div>

                    <Link href="/menu" class="mt-10 inline-block rounded-full bg-antika-coral px-7 py-3 text-sm font-medium text-white transition-colors hover:bg-antika-copper">{{ $t('about.cta') }}</Link>
                </div>
            </div>
        </section>

        <!-- ATOUTS -->
        <section class="bg-antika-panel py-20">
            <div class="mx-auto max-w-7xl px-6">
                <div class="grid gap-px overflow-hidden rounded-sm bg-white/10 sm:grid-cols-3">
                    <div v-for="(f, i) in tr.features.items" :key="i" v-reveal="i * 120" class="bg-antika-panel p-10 text-center">
                        <span class="font-serif text-4xl text-antika-copper">0{{ i + 1 }}</span>
                        <h3 class="mt-4 font-serif text-xl text-antika-cream">{{ f.title }}</h3>
                        <p class="mt-3 text-sm leading-relaxed text-stone-400">{{ f.text }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- SUGGESTIONS DU CHEF -->
        <section class="py-24">
            <div class="mx-auto max-w-7xl px-6">
                <div v-reveal class="flex flex-col items-center text-center">
                    <Eyebrow center>{{ $t('chef.eyebrow') }}</Eyebrow>
                    <h2 class="mt-6 font-serif text-4xl text-antika-cream sm:text-5xl">{{ $t('chef.title') }}</h2>
                </div>

                <div class="mt-16 grid items-center gap-12 lg:grid-cols-2">
                    <div v-reveal class="grid grid-cols-2 gap-4">
                        <img src="/images/dishes/d1.jpg" alt="" class="mt-8 aspect-[4/5] w-full rounded-sm object-cover shadow-xl" />
                        <img src="/images/dishes/d2.jpg" alt="" class="aspect-[4/5] w-full rounded-sm object-cover shadow-xl" />
                    </div>
                    <div v-reveal="120">
                        <Eyebrow>{{ $t('menu.title') }}</Eyebrow>
                        <p class="mt-5 max-w-md leading-relaxed text-stone-400">{{ $t('menu.intro') }}</p>
                        <a
                            :href="menuUrl"
                            target="_blank"
                            rel="noopener"
                            class="mt-8 inline-flex items-center gap-2.5 rounded-full bg-antika-coral px-9 py-4 text-sm font-semibold uppercase tracking-wide text-white transition-colors hover:bg-antika-copper"
                        >
                            {{ $t('menu.cta') }}
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                        <p class="mt-4 text-xs uppercase tracking-widest text-stone-500">{{ $t('menu.cta_note') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- TEASER ÉVÉNEMENTS / LOCATION -->
        <section class="relative overflow-hidden">
            <img src="/images/events/banquet.jpg" alt="" class="absolute inset-0 h-full w-full object-cover" />
            <div class="absolute inset-0 bg-antika-ink/85"></div>
            <div class="relative mx-auto max-w-3xl px-6 py-24 text-center">
                <div v-reveal>
                    <Eyebrow center>{{ $t('events.hero_eyebrow') }}</Eyebrow>
                    <h2 class="mt-6 font-serif text-4xl text-antika-cream sm:text-5xl">{{ $t('events.hero_title') }}</h2>
                    <p class="mx-auto mt-6 max-w-2xl leading-relaxed text-stone-300">{{ $t('events.hero_text') }}</p>
                    <div class="mt-10 flex flex-wrap justify-center gap-4">
                        <a :href="site.links.simulator" target="_blank" rel="noopener" class="rounded-full bg-antika-coral px-8 py-3 text-sm font-medium text-white transition-colors hover:bg-antika-copper">{{ $t('events.cta_button') }}</a>
                        <Link href="/events" class="rounded-full border border-antika-cream/50 px-8 py-3 text-sm font-medium text-antika-cream transition-colors hover:bg-antika-cream hover:text-antika-ink">{{ $t('nav.events_short') }}</Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- TÉMOIGNAGE -->
        <section class="py-24">
            <div v-reveal class="mx-auto max-w-3xl px-6 text-center">
                <Eyebrow center>{{ $t('testimonial.eyebrow') }}</Eyebrow>
                <p class="mt-8 font-serif text-2xl italic leading-relaxed text-antika-cream sm:text-3xl">&bdquo;{{ $t('testimonial.quote') }}&rdquo;</p>
                <p class="mt-6 text-sm uppercase tracking-widest text-stone-400">— {{ $t('testimonial.author') }}</p>
            </div>
        </section>
    </AppLayout>
</template>
