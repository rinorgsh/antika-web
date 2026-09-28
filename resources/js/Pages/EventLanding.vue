<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import CallbackForm from '@/Components/CallbackForm.vue';
import Eyebrow from '@/Components/Eyebrow.vue';
import FaqList from '@/Components/FaqList.vue';
import ReviewsBlock from '@/Components/ReviewsBlock.vue';
import VenueGrid from '@/Components/VenueGrid.vue';

// Page d'atterrissage d'une annonce : un message, une action principale
// (devis, ou rappel pour une koffietafel), et de quoi rassurer.
const props = defineProps({
    occasion: { type: String, required: true },
    eventType: { type: String, default: null },
    eventTypeId: { type: Number, default: null },
    strings: { type: Object, required: true },
    venues: { type: Array, default: () => [] },
});

const page = usePage();
const site = computed(() => page.props.site);
const o = computed(() => props.strings[props.occasion]);
const c = computed(() => props.strings.common);
const steps = computed(() => o.value.steps || c.value.steps);
const callbackFirst = computed(() => o.value.primary === 'callback');

const simulatorUrl = computed(() => `/events/simulator${props.eventType ? `?type=${props.eventType}` : ''}`);
const small = (img) => img.replace(/\.webp$/, '-sm.webp');
</script>

<template>
    <Head>
        <title>{{ o.meta_title }}</title>
        <meta head-key="description" name="description" :content="o.meta_description" />
    </Head>

    <AppLayout sticky-bar :quote-url="simulatorUrl">
        <!-- HERO -->
        <section class="relative flex min-h-[88vh] items-center overflow-hidden">
            <img :src="o.image" :srcset="`${small(o.image)} 900w, ${o.image} 1920w`" sizes="100vw" alt="" fetchpriority="high" class="absolute inset-0 h-full w-full object-cover" />
            <div class="absolute inset-0 bg-gradient-to-t from-antika-ink via-antika-ink/75 to-antika-ink/55"></div>
            <div class="relative mx-auto w-full max-w-4xl px-6 pb-16 pt-32 text-center">
                <Eyebrow center>{{ o.eyebrow }}</Eyebrow>
                <h1 class="mx-auto mt-6 max-w-3xl font-serif text-4xl leading-tight text-antika-cream sm:text-6xl">{{ o.title }}</h1>
                <p class="mx-auto mt-6 max-w-2xl leading-relaxed text-stone-200">{{ o.text }}</p>
                <div class="mt-10 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <a v-if="callbackFirst" href="#callback" class="inline-block rounded-full bg-antika-coral px-10 py-4 text-sm font-semibold uppercase tracking-wide text-white transition-colors hover:bg-antika-copper">{{ o.cta || c.cta }}</a>
                    <Link v-else :href="simulatorUrl" class="inline-block rounded-full bg-antika-coral px-10 py-4 text-sm font-semibold uppercase tracking-wide text-white transition-colors hover:bg-antika-copper">{{ o.cta || c.cta }}</Link>
                    <a :href="`tel:${site.contact.phone_link}`" class="inline-block rounded-full border border-antika-cream/50 px-8 py-4 text-sm font-semibold tracking-wide text-antika-cream transition-colors hover:bg-antika-cream hover:text-antika-ink">{{ site.contact.phone }}</a>
                </div>
                <p class="mt-4 text-xs uppercase tracking-widest text-stone-400">{{ c.cta_note }}</p>
            </div>
        </section>

        <!-- Bandeau de confiance -->
        <section class="border-y border-white/10 bg-antika-panel">
            <ul class="mx-auto grid max-w-6xl grid-cols-2 gap-px bg-white/10 md:grid-cols-4">
                <li v-for="(item, i) in c.trust" :key="i" class="flex items-center justify-center gap-2 bg-antika-panel px-4 py-5 text-center text-sm text-stone-200">
                    <svg class="h-4 w-4 flex-none text-antika-copper" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    {{ item }}
                </li>
            </ul>
        </section>

        <!-- Arguments + étapes -->
        <section class="py-20">
            <div class="mx-auto grid max-w-6xl gap-12 px-6 lg:grid-cols-2 lg:items-center">
                <div v-reveal>
                    <Eyebrow>{{ c.why }}</Eyebrow>
                    <ul class="mt-8 space-y-4">
                        <li v-for="(point, i) in o.points" :key="i" class="flex items-start gap-3 text-stone-200">
                            <svg class="mt-0.5 h-5 w-5 flex-none text-antika-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" /></svg>
                            <span>{{ point }}</span>
                        </li>
                    </ul>
                    <p class="mt-8 text-sm text-stone-400">{{ c.location }}</p>
                </div>
                <div v-reveal="120" class="grid grid-cols-3 gap-6 text-center">
                    <div v-for="(st, i) in steps" :key="i">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-antika-copper/60 font-serif text-xl text-antika-copper">{{ i + 1 }}</div>
                        <p class="mt-4 font-serif text-lg text-antika-cream">{{ st.title }}</p>
                        <p class="mt-2 text-xs leading-relaxed text-stone-400">{{ st.text }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Formules (pages entreprises) -->
        <section v-if="o.formats" class="border-t border-white/10 py-20">
            <div class="mx-auto max-w-6xl px-6">
                <div v-reveal class="text-center">
                    <Eyebrow center>{{ o.formats_title }}</Eyebrow>
                </div>
                <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div v-for="(f, i) in o.formats" :key="i" v-reveal="i * 100" class="border border-white/10 bg-antika-panel p-7">
                        <span class="font-serif text-3xl text-antika-copper">0{{ i + 1 }}</span>
                        <h3 class="mt-3 font-serif text-xl text-antika-cream">{{ f.title }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-stone-400">{{ f.text }}</p>
                    </div>
                </div>
            </div>
        </section>

        <VenueGrid :venues="venues" />

        <ReviewsBlock />

        <!-- Rappel + devis -->
        <section id="callback" class="scroll-mt-24 border-y border-white/10 bg-antika-panel/40 py-20">
            <div class="mx-auto grid max-w-6xl gap-10 px-6 lg:grid-cols-2 lg:items-center">
                <div v-reveal class="text-center lg:text-left">
                    <h2 class="font-serif text-3xl text-antika-cream sm:text-4xl">{{ o.title }}</h2>
                    <p class="mt-4 leading-relaxed text-stone-400">{{ c.cta_note }}</p>
                    <Link v-if="!callbackFirst" :href="simulatorUrl" class="mt-8 inline-block rounded-full bg-antika-coral px-10 py-4 text-sm font-semibold uppercase tracking-wide text-white transition-colors hover:bg-antika-copper">{{ c.cta }}</Link>
                    <p class="mt-6 text-sm text-stone-400">
                        {{ c.or_call }}
                        <a :href="`tel:${site.contact.phone_link}`" class="text-antika-cream hover:text-antika-copper">{{ site.contact.phone }}</a>
                    </p>
                </div>
                <div v-reveal="120">
                    <CallbackForm :event-type-id="eventTypeId" :source="`/events/${occasion}`" />
                </div>
            </div>
        </section>

        <FaqList />
    </AppLayout>
</template>
