<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';

const page = usePage();
const site = computed(() => page.props.site);

const mobileOpen = ref(false);
const scrolled = ref(false);

const onScroll = () => { scrolled.value = window.scrollY > 40; };
onMounted(() => { onScroll(); window.addEventListener('scroll', onScroll, { passive: true }); });
onBeforeUnmount(() => window.removeEventListener('scroll', onScroll));

const navLinks = computed(() => [
    { label: 'nav.home', href: '/' },
    { label: 'nav.menu', href: '/menu' },
    { label: 'nav.events_short', href: '/events' },
    { label: 'nav.contact', href: '/contact' },
]);

const isActive = (href) => page.url === href || (href !== '/' && page.url.startsWith(href));
</script>

<template>
    <div class="flex min-h-screen flex-col bg-antika-ink text-stone-200">
        <header class="fixed inset-x-0 top-0 z-50 transition-all duration-300" :class="scrolled ? 'bg-antika-ink/95 shadow-lg shadow-black/30 backdrop-blur' : 'bg-gradient-to-b from-antika-ink/80 to-transparent'">
            <!-- Barre supérieure : coordonnées (se replie au scroll) -->
            <div class="hidden overflow-hidden border-b border-white/10 transition-all duration-300 md:block" :class="scrolled ? 'max-h-0 border-transparent opacity-0' : 'max-h-12 opacity-100'">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-2.5 text-xs text-stone-300">
                    <a :href="site.links.maps" target="_blank" rel="noopener" class="hover:text-antika-cream">{{ site.contact.address }}</a>
                    <div class="flex items-center gap-6">
                        <a :href="`tel:${site.contact.phone_link}`" class="hover:text-antika-cream">{{ site.contact.phone }}</a>
                        <a :href="`mailto:${site.contact.email}`" class="hover:text-antika-cream">{{ site.contact.email }}</a>
                        <LanguageSwitcher />
                    </div>
                </div>
            </div>

            <!-- Barre de navigation -->
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 transition-all duration-300" :class="scrolled ? 'py-3' : 'py-4'">
                <Link href="/" class="flex items-center">
                    <img src="/images/logo.png" alt="Antika" class="w-auto transition-all duration-300" :class="scrolled ? 'h-11' : 'h-14'" />
                </Link>

                <nav class="hidden items-center gap-8 lg:flex">
                    <Link
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        class="text-sm font-medium uppercase tracking-wide transition-colors"
                        :class="isActive(link.href) ? 'text-antika-copper' : 'text-stone-200 hover:text-antika-cream'"
                    >{{ $t(link.label) }}</Link>
                    <!-- Take away masqué (à réactiver si besoin) :
                    <a :href="site.links.takeaway" target="_blank" rel="noopener" class="text-sm font-medium uppercase tracking-wide text-stone-200 transition-colors hover:text-antika-cream">{{ $t('nav.takeaway') }}</a>
                    -->
                </nav>

                <div class="hidden items-center gap-5 lg:flex">
                    <button
                        type="button"
                        data-zc-action="open"
                        class="border border-antika-cream/60 px-6 py-2.5 text-xs font-semibold uppercase tracking-widest text-antika-cream transition-colors hover:bg-antika-cream hover:text-antika-ink"
                    >{{ $t('nav.reserve') }}</button>
                </div>

                <button class="text-antika-cream lg:hidden" @click="mobileOpen = !mobileOpen" aria-label="Menu">
                    <svg v-if="!mobileOpen" class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    <svg v-else class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- Navigation mobile -->
            <transition name="slide">
                <nav v-if="mobileOpen" class="border-t border-white/10 bg-antika-ink/98 px-6 py-6 backdrop-blur lg:hidden">
                    <div class="flex flex-col gap-5">
                        <Link
                            v-for="link in navLinks"
                            :key="link.href"
                            :href="link.href"
                            class="text-base uppercase tracking-wide"
                            :class="isActive(link.href) ? 'text-antika-copper' : 'text-stone-100'"
                            @click="mobileOpen = false"
                        >{{ $t(link.label) }}</Link>
                        <!-- Take away masqué (à réactiver si besoin) :
                        <a :href="site.links.takeaway" target="_blank" rel="noopener" class="text-base uppercase tracking-wide text-stone-100">{{ $t('nav.takeaway') }}</a>
                        -->

                        <button type="button" data-zc-action="open" class="mt-1 border border-antika-cream/60 px-6 py-3 text-center text-xs font-semibold uppercase tracking-widest text-antika-cream" @click="mobileOpen = false">{{ $t('nav.reserve') }}</button>
                        <div class="pt-2"><LanguageSwitcher /></div>
                    </div>
                </nav>
            </transition>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <!-- Pied de page -->
        <footer class="border-t border-white/10 bg-antika-panel">
            <div class="mx-auto grid max-w-7xl gap-10 px-6 py-14 sm:grid-cols-2 lg:grid-cols-4">
                <div class="sm:col-span-2 lg:col-span-1">
                    <img src="/images/logo.png" alt="Antika" class="h-16 w-auto" />
                    <p class="mt-4 max-w-xs text-sm text-stone-400">{{ $t('footer.tagline') }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-antika-copper">{{ $t('footer.address_title') }}</p>
                    <a :href="site.links.maps" target="_blank" rel="noopener" class="mt-3 block text-sm text-stone-300 hover:text-antika-cream">{{ site.contact.address }}</a>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-antika-copper">{{ $t('footer.contact_title') }}</p>
                    <a :href="`tel:${site.contact.phone_link}`" class="mt-3 block text-sm text-stone-300 hover:text-antika-cream">{{ site.contact.phone }}</a>
                    <a :href="`mailto:${site.contact.email}`" class="block text-sm text-stone-300 hover:text-antika-cream">{{ site.contact.email }}</a>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-antika-copper">{{ $t('footer.hours_title') }}</p>
                    <ul class="mt-3 space-y-1 text-sm text-stone-300">
                        <li class="flex justify-between gap-4"><span>{{ $t('hours.mon_thu') }}</span><span class="text-stone-500">{{ $t('hours.evening') }}</span></li>
                        <li class="flex justify-between gap-4"><span>{{ $t('hours.tue') }}</span><span class="text-stone-500">{{ $t('hours.closed') }}</span></li>
                        <li class="flex justify-between gap-4"><span>{{ $t('hours.fri_sun') }}</span><span class="text-stone-500">{{ $t('hours.allday') }}</span></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/10 py-5 text-center text-xs text-stone-500">
                © {{ new Date().getFullYear() }} Antika Restaurant — {{ $t('footer.rights') }}
            </div>
        </footer>
    </div>
</template>

<style scoped>
.slide-enter-active, .slide-leave-active { transition: all 0.25s ease; }
.slide-enter-from, .slide-leave-to { opacity: 0; transform: translateY(-8px); }
</style>
