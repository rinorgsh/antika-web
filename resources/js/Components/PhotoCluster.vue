<script setup>
// Trio de photos qui se chevauchent : une en haut à gauche, une en bas à gauche,
// une au milieu à droite. Effet "collage" en profondeur.
defineProps({
    images: { type: Array, required: true }, // 3 chemins d'images
});
</script>

<template>
    <div class="relative mx-auto h-[340px] w-full max-w-[440px] sm:h-[460px] sm:max-w-[500px]">
        <!-- Haut gauche -->
        <img
            :src="images[0]"
            alt=""
            class="absolute left-0 top-0 z-10 aspect-[4/5] w-[55%] rounded-sm object-cover shadow-2xl ring-1 ring-white/10"
        />
        <!-- Milieu droite (au-dessus) -->
        <img
            :src="images[2]"
            alt=""
            class="absolute right-0 top-1/2 z-30 aspect-[4/5] w-[58%] -translate-y-1/2 rounded-sm object-cover shadow-2xl ring-4 ring-antika-ink"
        />
        <!-- Bas gauche -->
        <img
            :src="images[1]"
            alt=""
            class="absolute bottom-0 left-[5%] z-20 aspect-square w-[50%] rounded-sm object-cover shadow-2xl ring-4 ring-antika-ink"
        />
        <!-- Liseré cuivre décoratif -->
        <div class="absolute -bottom-3 left-2 z-0 h-20 w-20 border border-antika-copper/60"></div>
    </div>
</template>
