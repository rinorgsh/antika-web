import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { i18n } from './i18n';
import { reveal } from './reveal';
import { installTracking } from './tracking';


createInertiaApp({
    // Les titres des pages d'annonces contiennent déjà « Antika » : pas de doublon.
    title: (title) => (!title ? 'Antika' : title.includes('Antika') ? title : `${title} — Antika`),
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        installTracking(props.initialPage.props.site?.tracking);

        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(i18n)
            .directive('reveal', reveal)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
