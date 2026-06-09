<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Eyebrow from '@/Components/Eyebrow.vue';
import { t } from '@/i18n';

const page = usePage();
const site = computed(() => page.props.site);
const ev = computed(() => page.props.translations.events);
</script>

<template>
    <Head :title="t('nav.events')" />

    <AppLayout>
        <!-- HERO -->
        <section class="relative flex min-h-[80vh] items-center overflow-hidden">
            <img src="/images/events/banquet.jpg" alt="" class="absolute inset-0 h-full w-full object-cover" />
            <div class="absolute inset-0 bg-gradient-to-t from-antika-ink via-antika-ink/70 to-antika-ink/60"></div>
            <div class="relative mx-auto w-full max-w-7xl px-6 pt-24 text-center">
                <Eyebrow center>{{ ev.hero_eyebrow }}</Eyebrow>
                <h1 class="mx-auto mt-6 max-w-3xl font-serif text-4xl leading-tight text-antika-cream sm:text-6xl">{{ ev.hero_title }}</h1>
                <p class="mx-auto mt-6 max-w-2xl leading-relaxed text-stone-200">{{ ev.hero_text }}</p>
                <a :href="site.links.simulator" target="_blank" rel="noopener" class="mt-9 inline-block rounded-full bg-antika-coral px-8 py-3.5 text-sm font-medium text-white transition-colors hover:bg-antika-copper">{{ ev.cta_button }}</a>
            </div>
        </section>

        <!-- INTRO split -->
        <section class="py-24">
            <div class="mx-auto grid max-w-7xl items-center gap-14 px-6 lg:grid-cols-2">
                <div v-reveal>
                    <Eyebrow>{{ ev.hero_eyebrow }}</Eyebrow>
                    <h2 class="mt-5 font-serif text-3xl leading-tight text-antika-cream sm:text-4xl">{{ ev.intro_title }}</h2>
                    <p class="mt-6 leading-relaxed text-stone-400">{{ ev.intro_text }}</p>

                    <ul class="mt-8 space-y-3">
                        <li v-for="(p, i) in ev.formula_points" :key="i" class="flex items-start gap-3 text-stone-200">
                            <svg class="mt-0.5 h-5 w-5 flex-none text-antika-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" /></svg>
                            <span>{{ p }}</span>
                        </li>
                    </ul>
                </div>
                <div v-reveal="120" class="relative">
                    <img src="/images/events/garden.jpg" alt="" class="aspect-[4/5] w-full rounded-sm object-cover shadow-2xl" />
                    <div class="absolute -bottom-5 -right-5 hidden h-28 w-28 border border-antika-copper/60 lg:block"></div>
                </div>
            </div>
        </section>

        <!-- TYPES D'ÉVÉNEMENTS -->
        <section class="bg-antika-panel py-24">
            <div class="mx-auto max-w-7xl px-6">
                <div v-reveal class="text-center">
                    <Eyebrow center>{{ ev.types_title }}</Eyebrow>
                </div>
                <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div v-for="(type, i) in ev.types" :key="i" v-reveal="i * 100" class="border border-white/10 bg-antika-ink/40 p-8 transition-colors hover:border-antika-copper/50">
                        <span class="font-serif text-3xl text-antika-copper">0{{ i + 1 }}</span>
                        <h3 class="mt-4 font-serif text-xl text-antika-cream">{{ type.title }}</h3>
                        <p class="mt-3 text-sm leading-relaxed text-stone-400">{{ type.text }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ÉTAPES -->
        <section class="py-24">
            <div class="mx-auto max-w-7xl px-6">
                <div v-reveal class="text-center">
                    <Eyebrow center>{{ ev.steps_title }}</Eyebrow>
                </div>
                <div class="mt-14 grid gap-10 md:grid-cols-3">
                    <div v-for="(step, i) in ev.steps" :key="i" v-reveal="i * 120" class="relative text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border border-antika-copper/60 font-serif text-2xl text-antika-copper">{{ i + 1 }}</div>
                        <h3 class="mt-5 font-serif text-xl text-antika-cream">{{ step.title }}</h3>
                        <p class="mx-auto mt-3 max-w-xs text-sm leading-relaxed text-stone-400">{{ step.text }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA SIMULATEUR -->
        <section class="relative overflow-hidden">
            <img src="/images/events/hall.jpg" alt="" class="absolute inset-0 h-full w-full object-cover" />
            <div class="absolute inset-0 bg-antika-ink/88"></div>
            <div v-reveal class="relative mx-auto max-w-3xl px-6 py-24 text-center">
                <h2 class="font-serif text-4xl text-antika-cream sm:text-5xl">{{ ev.cta_title }}</h2>
                <p class="mx-auto mt-5 max-w-xl leading-relaxed text-stone-300">{{ ev.cta_text }}</p>
                <a :href="site.links.simulator" target="_blank" rel="noopener" class="mt-9 inline-block rounded-full bg-antika-coral px-9 py-4 text-sm font-semibold uppercase tracking-wide text-white transition-colors hover:bg-antika-copper">{{ ev.cta_button }}</a>
                <p class="mt-6 text-sm text-stone-400">
                    {{ ev.cta_or }}
                    <a :href="`tel:${site.contact.phone_link}`" class="text-antika-cream hover:text-antika-copper">{{ site.contact.phone }}</a>
                </p>
            </div>
        </section>
    </AppLayout>
</template>
