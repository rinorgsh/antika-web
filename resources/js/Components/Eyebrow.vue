<script setup>
// Petit intitulé orange en majuscules, encadré de traits — motif récurrent du site.
defineProps({
    center: { type: Boolean, default: false },
});
</script>

<template>
    <div class="flex items-center gap-4" :class="center ? 'justify-center' : ''">
        <span class="h-px w-10 bg-antika-copper"></span>
        <span class="text-xs font-semibold uppercase tracking-[0.25em] text-antika-copper">
            <slot />
        </span>
        <span v-if="center" class="h-px w-10 bg-antika-copper"></span>
    </div>
</template>
