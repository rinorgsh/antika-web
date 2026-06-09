import { usePage } from '@inertiajs/vue3';

/*
 * Petit helper de traduction maison.
 * Les chaînes traduites de la langue active sont partagées par Laravel via
 * Inertia (voir HandleInertiaRequests::share -> 'translations').
 *
 * Utilisation dans un template : {{ $t('nav.menu') }}
 * Utilisation dans le <script setup> : import { t } from '@/i18n'; t('nav.menu')
 */
export function t(key, fallback = '') {
    const translations = usePage().props.translations || {};

    return (
        key.split('.').reduce((value, part) => {
            return value && typeof value === 'object' ? value[part] : undefined;
        }, translations) ?? (fallback || key)
    );
}

// Plugin Vue : rend $t disponible dans tous les templates sans import.
export const i18n = {
    install(app) {
        app.config.globalProperties.$t = t;
    },
};
