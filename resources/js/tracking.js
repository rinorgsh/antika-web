import { router } from '@inertiajs/vue3';

/*
 * Suivi Google Analytics 4 + Google Ads.
 *
 * - Aucun script Google n'est chargé si les identifiants ne sont pas
 *   renseignés (config/antika.php -> tracking), voir app.blade.php.
 * - Consentement (RGPD) : tout est refusé par défaut ; le bandeau
 *   CookieConsent.vue met à jour le consentement.
 * - Provenance : gclid et utm_* de l'annonce sont gardés 90 jours dans le
 *   navigateur puis envoyés avec la demande de devis (visible dans l'admin).
 */

const ATTRIBUTION_KEY = 'antika_attribution';
const CONSENT_KEY = 'antika_consent';
const PARAMS = ['gclid', 'gbraid', 'wbraid', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];

let config = {};

function gtag() {
    if (typeof window.gtag === 'function') {
        window.gtag(...arguments);
    }
}

export function trackingEnabled() {
    return Boolean(config.ga4_id || config.ads_id);
}

export function captureAttribution() {
    try {
        const query = new URLSearchParams(window.location.search);
        const found = {};
        PARAMS.forEach((p) => {
            const v = query.get(p);
            if (v) found[p] = v.slice(0, 250);
        });
        if (Object.keys(found).length) {
            found.landing_page = window.location.pathname;
            localStorage.setItem(ATTRIBUTION_KEY, JSON.stringify({ at: Date.now(), data: found }));
        }
    } catch (e) {
        /* stockage indisponible (navigation privée…) : tant pis */
    }
}

export function attribution() {
    try {
        const raw = JSON.parse(localStorage.getItem(ATTRIBUTION_KEY) || 'null');
        if (raw && Date.now() - raw.at < 90 * 24 * 3600 * 1000) return raw.data;
    } catch (e) {
        /* ignoré */
    }
    return {};
}

export function event(name, params = {}) {
    gtag('event', name, params);
}

/**
 * Données du formulaire pour les conversions améliorées Google Ads : Google
 * les hache avant envoi (seulement si le visiteur a accepté les cookies).
 */
export function setUserData({ email, phone } = {}) {
    const data = {};
    if (email) data.email = email.trim().toLowerCase();
    if (phone) {
        // Format international attendu (+32…) : 0495… devient +32495…
        const digits = phone.replace(/[^\d+]/g, '');
        data.phone_number = digits.startsWith('+') ? digits : digits.startsWith('00') ? `+${digits.slice(2)}` : digits.startsWith('0') ? `+32${digits.slice(1)}` : `+${digits}`;
    }
    if (Object.keys(data).length) gtag('set', 'user_data', data);
}

/** Conversion Google Ads (libellé défini dans config/antika.php -> tracking). */
export function adsConversion(labelKey, params = {}) {
    const label = config[labelKey];
    if (config.ads_id && label) {
        gtag('event', 'conversion', { send_to: `${config.ads_id}/${label}`, ...params });
    }
}

/** Réaffiche le bandeau cookies (lien « Cookie-instellingen » du pied de page). */
export function reopenConsent() {
    try {
        localStorage.removeItem(CONSENT_KEY);
    } catch (e) {
        /* ignoré */
    }
    window.dispatchEvent(new Event('antika:consent'));
}

export function storedConsent() {
    try {
        return localStorage.getItem(CONSENT_KEY);
    } catch (e) {
        return null;
    }
}

export function setConsent(granted) {
    try {
        localStorage.setItem(CONSENT_KEY, granted ? 'granted' : 'denied');
    } catch (e) {
        /* ignoré */
    }
    const value = granted ? 'granted' : 'denied';
    gtag('consent', 'update', {
        ad_storage: value,
        ad_user_data: value,
        ad_personalization: value,
        analytics_storage: value,
    });
}

export function installTracking(trackingConfig) {
    config = trackingConfig || {};
    captureAttribution();

    if (!trackingEnabled()) return;

    // Site en une page (Inertia) : une page vue par navigation.
    router.on('navigate', (e) => {
        event('page_view', {
            page_location: window.location.href,
            page_path: e.detail.page.url,
            page_title: document.title,
        });
    });

    // Clics téléphone / WhatsApp, où qu'ils soient sur le site.
    document.addEventListener('click', (e) => {
        const link = e.target.closest && e.target.closest('a[href]');
        if (!link) return;
        const href = link.getAttribute('href') || '';
        if (href.startsWith('tel:')) {
            event('phone_click', { link_url: href });
            adsConversion('ads_call_label');
        } else if (href.includes('wa.me/')) {
            event('whatsapp_click', { link_url: href });
            adsConversion('ads_contact_label');
        } else if (href.startsWith('mailto:')) {
            event('email_click', { link_url: href });
            adsConversion('ads_contact_label');
        }
    });
}
