<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Eyebrow from '@/Components/Eyebrow.vue';

// Page d'atterrissage d'une annonce : un message, un bouton, zéro distraction.
const props = defineProps({
    occasion: { type: String, required: true },
    eventType: { type: String, default: null },
    strings: { type: Object, required: true },
});

const page = usePage();
const site = computed(() => page.props.site);
const o = computed(() => props.strings[props.occasion]);
const c = computed(() => props.strings.common);

const simulatorUrl = computed(() => `/events/simulator${props.eventType ? `?type=${props.eventType}` : ''}`);
</script>

<template>
    <Head>
        <title>{{ o.meta_title }}</title>
        <meta head-key="description" name="description" :content="o.meta_description" />
    </Head>

    <AppLayout>
        <section class="relative flex min-h-[88vh] items-center overflow-hidden">
            <img :src="o.image" alt="" class="absolute inset-0 h-full w-full object-cover" />
            <div class="absolute inset-0 bg-gradient-to-t from-antika-ink via-antika-ink/75 to-antika-ink/55"></div>
            <div class="relative mx-auto w-full max-w-4xl px-6 pb-16 pt-32 text-center">
                <Eyebrow center>{{ o.eyebrow }}</Eyebrow>
                <h1 class="mx-auto mt-6 max-w-3xl font-serif text-4xl leading-tight text-antika-cream sm:text-6xl">{{ o.title }}</h1>
                <p class="mx-auto mt-6 max-w-2xl leading-relaxed text-stone-200">{{ o.text }}</p>
                <Link :href="simulatorUrl" class="mt-10 inline-block rounded-full bg-antika-coral px-10 py-4 text-sm font-semibold uppercase tracking-wide text-white transition-colors hover:bg-antika-copper">{{ c.cta }}</Link>
                <p class="mt-4 text-xs uppercase tracking-widest text-stone-400">{{ c.cta_note }}</p>
            </div>
        </section>

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
                    <p class="mt-8 text-sm text-stone-400">{{ c.location }} · {{ c.parking }}</p>
                </div>
                <div v-reveal="120" class="grid grid-cols-3 gap-6 text-center">
                    <div v-for="(st, i) in c.steps" :key="i">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-antika-copper/60 font-serif text-xl text-antika-copper">{{ i + 1 }}</div>
                        <p class="mt-4 font-serif text-lg text-antika-cream">{{ st.title }}</p>
                        <p class="mt-2 text-xs leading-relaxed text-stone-400">{{ st.text }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="border-t border-white/10 bg-antika-panel py-16 text-center">
            <Link :href="simulatorUrl" class="inline-block rounded-full bg-antika-coral px-10 py-4 text-sm font-semibold uppercase tracking-wide text-white transition-colors hover:bg-antika-copper">{{ c.cta }}</Link>
            <p class="mt-5 text-sm text-stone-400">
                {{ c.or_call }}
                <a :href="`tel:${site.contact.phone_link}`" class="text-antika-cream hover:text-antika-copper">{{ site.contact.phone }}</a>
            </p>
        </section>
    </AppLayout>
</template>
