<script setup>
// Affiche une catégorie de la carte : un libellé souligné + la liste des plats.
defineProps({
    label: { type: String, required: true },
    items: { type: Array, required: true },
    note: { type: String, default: '' },
});
</script>

<template>
    <div>
        <h3 class="inline-block border-b-2 border-antika-copper pb-1 text-sm font-semibold uppercase tracking-[0.2em] text-antika-copper">
            {{ label }}
        </h3>

        <ul class="mt-8 space-y-7">
            <li v-for="item in items" :key="item.name">
                <div class="flex items-baseline gap-3">
                    <span class="font-serif text-lg uppercase tracking-wide text-antika-cream">{{ item.name }}</span>
                    <span class="h-px flex-1 translate-y-[-2px] bg-white/15"></span>
                    <span class="font-serif text-lg text-stone-200">{{ item.price }}</span>
                </div>
                <p class="mt-1.5 max-w-md text-sm leading-relaxed text-stone-400">{{ item.desc }}</p>
            </li>
        </ul>

        <p v-if="note" class="mt-6 border-t border-white/10 pt-4 text-xs italic leading-relaxed text-stone-500">{{ note }}</p>
    </div>
</template>
