<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Eyebrow from '@/Components/Eyebrow.vue';
import MenuCategory from '@/Components/MenuCategory.vue';
import { t } from '@/i18n';

const page = usePage();
const categories = computed(() => page.props.translations.menu.categories);

// Photo de mise en avant par grande catégorie (les autres restent compactes).
const featuredImg = {
    starters: '/images/menu/starters.jpg',
    pasta: '/images/menu/pasta.jpg',
    grill: '/images/menu/grill.jpg',
    sea: '/images/menu/sea.jpg',
    specials: '/images/menu/specials.jpg',
    sides: '/images/menu/sides.jpg',
};

const featured = computed(() => categories.value.filter((c) => featuredImg[c.key]));
const compact = computed(() => categories.value.filter((c) => !featuredImg[c.key]));
</script>

<template>
    <Head :title="t('menu.title')" />

    <AppLayout>
        <!-- En-tête -->
        <section class="px-6 pb-12 pt-36 text-center sm:pt-44">
            <Eyebrow center>{{ $t('chef.eyebrow') }}</Eyebrow>
            <h1 class="mt-6 font-serif text-4xl text-antika-cream sm:text-5xl">{{ $t('menu.title') }}</h1>
            <p class="mx-auto mt-4 max-w-xl text-stone-400">{{ $t('menu.intro') }}</p>
        </section>

        <!-- Navigation par catégorie -->
        <nav class="sticky top-[68px] z-30 border-y border-white/10 bg-antika-ink/95 backdrop-blur">
            <div class="mx-auto flex max-w-6xl gap-2 overflow-x-auto px-6 py-3 sm:justify-center">
                <a
                    v-for="c in categories"
                    :key="c.key"
                    :href="`#cat-${c.key}`"
                    class="whitespace-nowrap rounded-full border border-white/10 px-4 py-1.5 text-xs font-medium uppercase tracking-wide text-stone-300 transition-colors hover:border-antika-copper hover:text-antika-cream"
                >{{ c.label }}</a>
            </div>
        </nav>

        <!-- Grandes catégories : image + liste alternées -->
        <div class="space-y-20 py-20 sm:space-y-28">
            <section
                v-for="(c, i) in featured"
                :id="`cat-${c.key}`"
                :key="c.key"
                class="mx-auto max-w-6xl scroll-mt-32 px-6"
            >
                <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
                    <div v-reveal class="relative" :class="i % 2 === 1 ? 'lg:order-2' : ''">
                        <img :src="featuredImg[c.key]" alt="" class="aspect-[4/3] w-full rounded-sm object-cover shadow-2xl" />
                        <div class="absolute -bottom-4 h-24 w-24 border border-antika-copper/60" :class="i % 2 === 1 ? '-left-4' : '-right-4'"></div>
                    </div>
                    <div v-reveal="120" :class="i % 2 === 1 ? 'lg:order-1' : ''">
                        <MenuCategory :label="c.label" :items="c.items" :note="c.note" />
                    </div>
                </div>
            </section>
        </div>

        <!-- Catégories compactes (burgers, enfants, sofra) -->
        <section class="border-t border-white/10 bg-antika-panel py-20">
            <div class="mx-auto grid max-w-6xl gap-14 px-6 md:grid-cols-3">
                <div v-for="c in compact" :id="`cat-${c.key}`" :key="c.key" v-reveal class="scroll-mt-32">
                    <MenuCategory :label="c.label" :items="c.items" :note="c.note" />
                </div>
            </div>
        </section>
    </AppLayout>
</template>
