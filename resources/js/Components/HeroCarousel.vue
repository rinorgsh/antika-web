<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    images: { type: Array, required: true },
    interval: { type: Number, default: 6000 },
});

const active = ref(0);
let timer = null;

const go = (i) => {
    active.value = (i + props.images.length) % props.images.length;
};
const next = () => go(active.value + 1);
const prev = () => go(active.value - 1);

const start = () => {
    stop();
    if (props.images.length > 1) timer = setInterval(next, props.interval);
};
const stop = () => {
    if (timer) clearInterval(timer);
    timer = null;
};

// Swipe tactile (mobile)
let touchX = 0;
const onTouchStart = (e) => { touchX = e.changedTouches[0].clientX; };
const onTouchEnd = (e) => {
    const dx = e.changedTouches[0].clientX - touchX;
    if (Math.abs(dx) > 50) (dx < 0 ? next : prev)();
};

onMounted(start);
onBeforeUnmount(stop);
</script>

<template>
    <section
        class="relative flex min-h-[100svh] items-center overflow-hidden bg-antika-ink"
        @mouseenter="stop"
        @mouseleave="start"
        @touchstart.passive="onTouchStart"
        @touchend.passive="onTouchEnd"
    >
        <!-- Slides -->
        <div class="absolute inset-0">
            <div
                v-for="(img, i) in images"
                :key="img"
                class="absolute inset-0 transition-opacity duration-[1200ms] ease-in-out"
                :class="i === active ? 'opacity-100' : 'opacity-0'"
            >
                <img
                    :src="img"
                    alt=""
                    class="h-full w-full object-cover"
                    :class="i === active ? 'animate-kenburns' : ''"
                />
            </div>
            <!-- Voiles sombres (allégés) : lisibilité du texte à gauche tout en gardant l'image lumineuse -->
            <div class="absolute inset-0 bg-gradient-to-r from-antika-ink/85 via-antika-ink/40 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-antika-ink/70 via-transparent to-antika-ink/15"></div>
        </div>

        <!-- Contenu -->
        <div class="absolute inset-0 z-10 flex items-center">
            <div class="w-full min-w-0">
                <slot />
            </div>
        </div>

        <!-- Flèches -->
        <button
            v-if="images.length > 1"
            class="absolute left-4 top-1/2 z-20 hidden -translate-y-1/2 text-antika-cream/60 transition-colors hover:text-antika-cream md:block"
            aria-label="Précédent"
            @click="prev"
        >
            <svg class="h-9 w-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M15 19l-7-7 7-7" /></svg>
        </button>
        <button
            v-if="images.length > 1"
            class="absolute right-4 top-1/2 z-20 hidden -translate-y-1/2 text-antika-cream/60 transition-colors hover:text-antika-cream md:block"
            aria-label="Suivant"
            @click="next"
        >
            <svg class="h-9 w-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M9 5l7 7-7 7" /></svg>
        </button>

        <!-- Points -->
        <div v-if="images.length > 1" class="absolute bottom-8 left-1/2 z-20 flex -translate-x-1/2 gap-3">
            <button
                v-for="(img, i) in images"
                :key="'dot-' + i"
                class="h-2 rounded-full transition-all duration-300"
                :class="i === active ? 'w-8 bg-antika-copper' : 'w-2 bg-antika-cream/40 hover:bg-antika-cream/70'"
                :aria-label="`Slide ${i + 1}`"
                @click="go(i)"
            ></button>
        </div>
    </section>
</template>

<style scoped>
@keyframes kenburns {
    0% { transform: scale(1); }
    100% { transform: scale(1.12); }
}
.animate-kenburns {
    animation: kenburns 7s ease-out forwards;
}
</style>
