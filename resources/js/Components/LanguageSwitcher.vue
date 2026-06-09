<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const current = computed(() => page.props.locale);
const locales = computed(() => page.props.locales);
</script>

<template>
    <div class="flex items-center gap-2 text-sm">
        <Link
            v-for="loc in locales"
            :key="loc"
            :href="`/locale/${loc}`"
            class="uppercase tracking-wide transition-colors"
            :class="current === loc ? 'font-semibold text-antika-copper' : 'text-stone-400 hover:text-antika-cream'"
        >
            {{ loc }}
        </Link>
    </div>
</template>
