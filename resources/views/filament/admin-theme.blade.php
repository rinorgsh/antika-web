{{--
    Habillage de l'admin Antika, injecté dans <head> (PanelsRenderHook::HEAD_END).

    Pourquoi du CSS ici plutôt qu'un thème Filament compilé : un thème Filament v4
    exige Tailwind 4, alors que le site public est en Tailwind 3. On s'appuie donc
    sur les classes stables de Filament (fi-…), sans étape de build.

    Principe : trois niveaux bien distincts — fond de page chaud, barres de
    navigation, cartes blanches bordées — et le cuivre Antika pour ce qui est actif.
--}}
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=playfair-display:500,600&display=swap" rel="stylesheet">
<style>
    :root {
        --ak-copper: #d9551f;
        --ak-copper-soft: rgba(217, 85, 31, .09);
        --ak-page: #f4f0ea;
        --ak-bar: #ffffff;
        --ak-card: #ffffff;
        --ak-line: #e6dfd5;
        --ak-head: #faf7f2;
        --ak-muted: #8a7f73;
        --ak-shadow: 0 1px 2px rgba(41, 28, 16, .04), 0 6px 20px -12px rgba(41, 28, 16, .18);
    }
    .dark {
        --ak-copper-soft: rgba(217, 85, 31, .16);
        --ak-page: #0e0b09;
        --ak-bar: #16110e;
        --ak-card: #1b1612;
        --ak-line: #2f2620;
        --ak-head: #221b16;
        --ak-muted: #a39585;
        --ak-shadow: 0 1px 2px rgba(0, 0, 0, .3), 0 8px 24px -14px rgba(0, 0, 0, .6);
    }

    /* ---------- Fonds ---------- */
    .fi-body { background: var(--ak-page) !important; }
    .fi-logo { height: 2.9rem !important; }

    .fi-topbar,
    .fi-topbar > nav {
        background: var(--ak-bar) !important;
        border-bottom: 1px solid var(--ak-line);
        box-shadow: none !important;
    }

    .fi-sidebar,
    .fi-sidebar-header {
        background: var(--ak-bar) !important;
    }
    @media (min-width: 1024px) {
        .fi-sidebar { border-right: 1px solid var(--ak-line); }
        .fi-body-has-topbar .fi-sidebar-header { display: none; }
    }

    /* ---------- Titres ---------- */
    .fi-header-heading,
    .fi-simple-header-heading {
        font-family: 'Playfair Display', Georgia, serif;
        font-weight: 600 !important;
        letter-spacing: -.01em;
    }

    /* ---------- Menu latéral ---------- */
    .fi-sidebar-nav { padding-top: 1.25rem; }
    .fi-sidebar-group-btn { padding-top: .35rem; padding-bottom: .35rem; border-radius: .5rem; }
    .fi-sidebar-group-btn:hover { background: var(--ak-copper-soft); }
    .fi-sidebar-group-label {
        font-size: .7rem !important;
        font-weight: 700 !important;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--ak-muted) !important;
    }
    .fi-sidebar-item-btn { border-radius: .55rem !important; }
    .fi-sidebar-item-btn:hover { background: var(--ak-copper-soft) !important; }
    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn {
        background: var(--ak-copper-soft) !important;
        box-shadow: inset 3px 0 0 var(--ak-copper);
    }
    .fi-sidebar-item.fi-active .fi-sidebar-item-label,
    .fi-sidebar-item.fi-active .fi-sidebar-item-icon { color: var(--ak-copper) !important; }

    /* ---------- Cartes : sections, tableaux, stats, repeaters ---------- */
    .fi-section:not(.fi-section-not-contained),
    .fi-ta-ctn,
    .fi-wi-stats-overview-stat,
    .fi-fo-repeater-item,
    .fi-in-repeatable-item,
    .fi-dropdown-panel,
    .fi-modal-window {
        background: var(--ak-card) !important;
        border: 1px solid var(--ak-line) !important;
        border-radius: .9rem !important;
        box-shadow: var(--ak-shadow) !important;
        --tw-ring-shadow: 0 0 #0000 !important;
    }
    /* Élément de repeater dans une section : un cran plus clair que la carte. */
    .fi-section .fi-fo-repeater-item { background: var(--ak-head) !important; box-shadow: none !important; }
    .fi-section-header { border-bottom: 1px solid var(--ak-line); }
    .fi-section-header-heading { font-weight: 700 !important; }


    /* ---------- Boutons principaux : cuivre Antika, texte blanc lisible ---------- */
    .fi-btn.fi-color-primary:not(.fi-outlined) {
        background: #c64a16 !important;
        color: #fff !important;
    }
    .fi-btn.fi-color-primary:not(.fi-outlined):hover { background: #a93d10 !important; }
    .fi-btn.fi-color-primary:not(.fi-outlined) .fi-icon { color: #fff !important; }

    .fi-ta-header-cell,
    .fi-ta-header-ctn,
    .fi-ta-header-toolbar { background: var(--ak-head) !important; }
    .fi-ta-header-cell, .fi-ta-header-cell-sort-btn { font-size: .8rem !important; color: var(--ak-muted) !important; }
    .fi-ta-row:hover { background: var(--ak-copper-soft) !important; }
    .fi-ta-row, .fi-ta-header-ctn, .fi-ta-header-toolbar { border-color: var(--ak-line) !important; }

    .fi-fieldset {
        border-color: var(--ak-line) !important;
        border-radius: .75rem !important;
    }

    /* ---------- Connexion ---------- */
    .fi-simple-layout { background: var(--ak-page) !important; }
    .fi-simple-main {
        background: var(--ak-card) !important;
        border: 1px solid var(--ak-line);
        border-radius: 1rem !important;
        box-shadow: var(--ak-shadow) !important;
    }

    /* ---------- Téléphone ---------- */
    @media (max-width: 640px) {
        .fi-main { padding-left: .85rem !important; padding-right: .85rem !important; }
        .fi-header { gap: .75rem !important; }
        .fi-header-heading { font-size: 1.5rem !important; line-height: 1.2 !important; }
        .fi-header-actions-ctn { flex-wrap: wrap; gap: .5rem !important; width: 100%; }
        .fi-header-actions-ctn .fi-btn { flex: 1 1 auto; justify-content: center; }
        /* « Supprimer » ne doit pas devenir le plus gros bouton de l'écran. */
        .fi-header-actions-ctn .fi-btn.fi-color-danger { flex: 0 0 auto; margin-left: auto; }
        .fi-fieldset { padding: .75rem !important; }
        .fi-section-content { padding: 1rem !important; }
        .fi-breadcrumbs { display: none; }
        .fi-ta-header-toolbar { flex-wrap: wrap; }
        .fi-ta-search-field { width: 100%; }
        .fi-wi-stats-overview-stat { padding: 1rem !important; }
    }
</style>
