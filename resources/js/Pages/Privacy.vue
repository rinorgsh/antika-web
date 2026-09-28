<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage();
const site = computed(() => page.props.site);
const p = computed(() => page.props.translations.privacy);
const fill = (text) => text.replaceAll(':email', site.value.contact.email).replaceAll(':phone', site.value.contact.phone);
</script>

<template>
    <Head :title="p.meta_title" />

    <AppLayout>
        <article class="mx-auto max-w-3xl px-6 pb-24 pt-36">
            <h1 class="font-serif text-4xl text-antika-cream sm:text-5xl">{{ p.title }}</h1>
            <p class="mt-3 text-xs uppercase tracking-widest text-stone-500">{{ p.updated }}</p>
            <section v-for="(section, i) in p.sections" :key="i" class="mt-10">
                <h2 class="font-serif text-2xl text-antika-cream">{{ section.title }}</h2>
                <p class="mt-3 leading-relaxed text-stone-300">{{ fill(section.text) }}</p>
            </section>
        </article>
    </AppLayout>
</template>
