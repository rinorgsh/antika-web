<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Eyebrow from '@/Components/Eyebrow.vue';
import { t } from '@/i18n';

const page = usePage();
const site = computed(() => page.props.site);
const locale = computed(() => page.props.locale || 'fr');

// Carte interactive multilingue (photos, boissons, desserts) — la même qu'au restaurant (QR).
// On ouvre directement le hub de catégories dans la langue courante du site.
const menuUrl = computed(() => `${site.value.links.menu}/carte.html?lang=${locale.value}`);

// Photos d'ambiance (aperçu appétissant).
const gallery = [
    '/images/menu/cluster/29.jpg', '/images/menu/cluster/24.jpg', '/images/menu/cluster/37.jpg',
    '/images/menu/cluster/38.jpg', '/images/menu/cluster/19.jpg', '/images/menu/cluster/44.jpg',
];
</script>

<template>
    <Head :title="t('menu.title')" />

    <AppLayout>
        <!-- En-tête + accès à la carte interactive -->
        <section class="px-6 pb-16 pt-36 text-center sm:pt-44">
            <Eyebrow center>{{ $t('chef.eyebrow') }}</Eyebrow>
            <h1 class="mt-6 font-serif text-4xl text-antika-cream sm:text-5xl">{{ $t('menu.title') }}</h1>
            <p class="mx-auto mt-4 max-w-xl text-stone-400">{{ $t('menu.intro') }}</p>

            <a
                :href="menuUrl"
                target="_blank"
                rel="noopener"
                class="mt-10 inline-flex items-center gap-2.5 rounded-full bg-antika-coral px-9 py-4 text-sm font-semibold uppercase tracking-wide text-white transition-colors hover:bg-antika-copper"
            >
                {{ $t('menu.cta') }}
                <span aria-hidden="true">&rarr;</span>
            </a>
            <p class="mt-4 text-xs uppercase tracking-widest text-stone-500">{{ $t('menu.cta_note') }}</p>
        </section>

        <!-- Aperçu photos -->
        <section class="mx-auto max-w-6xl px-6 pb-28">
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4">
                <img
                    v-for="(src, i) in gallery"
                    :key="i"
                    v-reveal="i * 60"
                    :src="src"
                    alt=""
                    loading="lazy"
                    class="aspect-square w-full rounded-2xl object-cover"
                />
            </div>
        </section>
    </AppLayout>
</template>
