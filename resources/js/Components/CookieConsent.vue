<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { setConsent, storedConsent, trackingEnabled } from '@/tracking';

// Bandeau affiché uniquement si un outil de suivi est configuré et
// qu'aucun choix n'a encore été fait sur ce navigateur.
const visible = ref(false);

const refresh = () => {
    visible.value = trackingEnabled() && !storedConsent();
};

onMounted(() => {
    refresh();
    window.addEventListener('antika:consent', refresh);
});
onBeforeUnmount(() => window.removeEventListener('antika:consent', refresh));

const choose = (granted) => {
    setConsent(granted);
    visible.value = false;
};
</script>

<template>
    <transition name="consent">
        <div v-if="visible" class="fixed inset-x-3 bottom-3 z-[60] mx-auto max-w-2xl border border-white/10 bg-antika-panel/95 p-5 text-sm text-stone-300 shadow-2xl shadow-black/50 backdrop-blur sm:inset-x-6 sm:bottom-6">
            <p class="leading-relaxed">{{ $t('cookies.text') }}</p>
            <div class="mt-4 flex flex-wrap justify-end gap-3">
                <button type="button" class="px-4 py-2 text-xs font-semibold uppercase tracking-widest text-stone-400 hover:text-antika-cream" @click="choose(false)">{{ $t('cookies.refuse') }}</button>
                <button type="button" class="rounded-full bg-antika-coral px-5 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-antika-copper" @click="choose(true)">{{ $t('cookies.accept') }}</button>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.consent-enter-active, .consent-leave-active { transition: all 0.3s ease; }
.consent-enter-from, .consent-leave-to { opacity: 0; transform: translateY(12px); }
</style>
