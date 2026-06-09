<script setup>
// Trio de photos.
// - Desktop : composition superposée en profondeur (photo focale + 2 accents).
// - Mobile : mosaïque nette (1 grande + 2 petites), sans superposition.
defineProps({
    images: { type: Array, required: true }, // 3 chemins d'images
});
</script>

<template>
    <!-- DESKTOP : superposition -->
    <div class="relative mx-auto hidden h-[520px] w-full max-w-[540px] lg:block">
        <!-- Cadre cuivre décoratif (profondeur) -->
        <div class="absolute left-[6%] top-[10%] z-0 h-[58%] w-[42%] border border-antika-copper/50"></div>

        <!-- Accent haut-gauche -->
        <img
            :src="images[0]"
            alt=""
            class="absolute left-0 top-0 z-10 aspect-[4/5] w-[46%] rounded-sm object-cover shadow-xl shadow-black/40 ring-1 ring-white/10"
        />
        <!-- Accent bas-gauche -->
        <img
            :src="images[1]"
            alt=""
            class="absolute bottom-0 left-[10%] z-20 aspect-square w-[42%] rounded-sm object-cover shadow-2xl shadow-black/50 ring-[6px] ring-antika-ink"
        />
        <!-- Photo focale milieu-droite (plus grande, au-dessus) -->
        <img
            :src="images[2]"
            alt=""
            class="absolute right-0 top-1/2 z-30 aspect-[4/5] w-[58%] -translate-y-1/2 rounded-sm object-cover shadow-2xl shadow-black/60 ring-[6px] ring-antika-ink"
        />
    </div>

    <!-- MOBILE / TABLETTE : mosaïque -->
    <div class="grid grid-cols-2 gap-3 lg:hidden">
        <img :src="images[2]" alt="" class="col-span-2 aspect-[16/10] w-full rounded-sm object-cover shadow-lg" />
        <img :src="images[0]" alt="" class="aspect-square w-full rounded-sm object-cover shadow-lg" />
        <img :src="images[1]" alt="" class="aspect-square w-full rounded-sm object-cover shadow-lg" />
    </div>
</template>
