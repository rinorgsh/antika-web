<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Eyebrow from '@/Components/Eyebrow.vue';
import { t } from '@/i18n';

const page = usePage();
const site = computed(() => page.props.site);

const hours = computed(() => [
    { day: t('hours.mon_thu'), value: t('hours.evening') },
    { day: t('hours.tue'), value: t('hours.closed') },
    { day: t('hours.fri_sun'), value: t('hours.allday') },
]);

const mapEmbed = 'https://www.google.com/maps?q=Pater+Penninckxstraat+32,+1982+Zemst&output=embed';
</script>

<template>
    <Head :title="t('nav.contact')" />

    <AppLayout>
        <!-- En-tête de page (dégage le header fixe) -->
        <section class="relative flex min-h-[44vh] items-center overflow-hidden">
            <img src="/images/interior.jpg" alt="" class="absolute inset-0 h-full w-full object-cover" />
            <div class="absolute inset-0 bg-antika-ink/75"></div>
            <div class="relative mx-auto w-full max-w-7xl px-6 pt-28 text-center">
                <Eyebrow center>{{ $t('visit.eyebrow') }}</Eyebrow>
                <h1 class="mt-5 font-serif text-4xl text-antika-cream sm:text-5xl">{{ $t('visit.title') }}</h1>
                <a :href="site.links.maps" target="_blank" rel="noopener" class="mt-4 inline-block text-stone-200 hover:text-antika-cream">
                    {{ site.contact.address }}
                </a>
            </div>
        </section>

        <!-- Infos : visite + réservation -->
        <section class="py-20">
            <div class="mx-auto grid max-w-6xl gap-12 px-6 lg:grid-cols-2">
                <!-- Horaires + accès -->
                <div v-reveal class="border border-white/10 bg-antika-panel p-8 sm:p-10">
                    <p class="text-xs font-semibold uppercase tracking-widest text-antika-copper">{{ $t('visit.hours_title') }}</p>
                    <ul class="mt-5 divide-y divide-white/10">
                        <li v-for="row in hours" :key="row.day" class="flex justify-between py-3 text-stone-200">
                            <span>{{ row.day }}</span>
                            <span class="text-stone-400">{{ row.value }}</span>
                        </li>
                    </ul>

                    <p class="mt-8 text-xs font-semibold uppercase tracking-widest text-antika-copper">{{ $t('footer.contact_title') }}</p>
                    <a :href="`tel:${site.contact.phone_link}`" class="mt-4 block text-stone-200 hover:text-antika-cream">{{ site.contact.phone }}</a>
                    <a :href="`mailto:${site.contact.email}`" class="block text-stone-200 hover:text-antika-cream">{{ site.contact.email }}</a>

                    <a :href="site.links.maps" target="_blank" rel="noopener" class="mt-8 inline-block border-b border-antika-copper pb-1 text-xs font-semibold uppercase tracking-widest text-antika-cream hover:text-antika-copper">
                        {{ $t('visit.directions') }}
                    </a>
                </div>

                <!-- Réservation -->
                <div v-reveal="120" class="flex flex-col justify-center border border-white/10 bg-antika-panel p-8 text-center sm:p-10">
                    <Eyebrow center>{{ $t('reservation.eyebrow') }}</Eyebrow>
                    <h2 class="mt-5 font-serif text-3xl uppercase tracking-wide text-antika-cream">{{ $t('reservation.title') }}</h2>
                    <p class="mt-5 leading-relaxed text-stone-300">{{ $t('reservation.text') }}</p>
                    <button
                        type="button"
                        data-zc-action="open"
                        class="mx-auto mt-8 rounded-full bg-antika-coral px-8 py-3 text-sm font-medium text-white transition-colors hover:bg-antika-copper"
                    >
                        {{ $t('reservation.online') }}
                    </button>
                </div>
            </div>
        </section>

        <!-- Carte -->
        <section class="px-6 pb-24">
            <div class="mx-auto max-w-6xl overflow-hidden rounded-sm border border-white/10">
                <iframe
                    :src="mapEmbed"
                    class="h-[420px] w-full grayscale-[30%]"
                    style="border: 0"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Antika — Pater Penninckxstraat 32, 1982 Zemst"
                ></iframe>
            </div>
        </section>
    </AppLayout>
</template>
