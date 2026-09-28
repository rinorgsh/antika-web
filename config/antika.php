<?php

/*
|--------------------------------------------------------------------------
| Informations du restaurant Antika
|--------------------------------------------------------------------------
| Données qui ne changent pas selon la langue : coordonnées, liens vers
| les plateformes externes (réservation, simulateur, commande), images.
| Modifiables ici sans toucher au code des pages.
*/

return [
    // URL publique du site (canonical, Open Graph, sitemap). À adapter au domaine réel.
    'url' => rtrim(env('ANTIKA_SITE_URL', 'https://antikaresto.com'), '/'),

    // Données SEO / référencement.
    'seo' => [
        'cuisine' => ['Albanian', 'Mediterranean', 'Seafood'],
        'price_range' => '€€',
        // Coordonnées GPS approximatives (Elewijt / Zemst) — À VÉRIFIER avec l'adresse exacte.
        'geo' => ['lat' => '50.9595', 'lng' => '4.5103'],
    ],

    // Exploitant (mention légale obligatoire sur un site belge : nom + numéro d'entreprise).
    'company' => [
        'name' => 'BMZ EVENT BV',
        'number' => '0632.633.901',
    ],

    'contact' => [
        'address' => 'Pater Penninckxstraat 32, 1982 Zemst',
        'phone' => '+32 495 52 66 56',
        'phone_link' => '+32495526656',
        'email' => 'info@antikaresto.com',
    ],

    'links' => [
        // À remplacer par les URLs réelles fournies par le client.
        // Carte interactive multilingue (photos, boissons, desserts) — la même que le QR au resto,
        // servie par le site lui-même (public/carte + CarteController).
        'menu' => '/carte',
        'reserve' => env('ANTIKA_RESERVE_URL', 'https://bookings.zenchef.com/'),
        'takeaway' => env('ANTIKA_TAKEAWAY_URL', 'https://www.takeaway.com/'),
        // Simulateur de devis location de salle / événements (plateforme Baba Events).
        'simulator' => '/events/simulator',
        'maps' => 'https://maps.app.goo.gl/h4vQLeVi1iGkpzSV7',
    ],

    'social' => [
        'instagram' => env('ANTIKA_INSTAGRAM_URL', ''),
        'facebook' => env('ANTIKA_FACEBOOK_URL', ''),
    ],

    // Simulateur de devis événements (/events/simulator).
    'events' => [
        // Reçoivent chaque nouvelle demande, en plus des adresses saisies dans l'admin.
        'notify_emails' => env('ANTIKA_EVENTS_NOTIFY', 'antika.info1982@gmail.com,molenveld.village@gmail.com'),
    ],

    // Pages d'atterrissage par occasion (/events/{clé}) => slug du type
    // d'événement présélectionné dans le simulateur. Textes : lang/*/landing.php.
    'landings' => [
        'venue-hire' => null,          // location de salle, toutes occasions
        'wedding' => 'mariage',
        'birthday' => 'anniversaire',
        'communion' => 'communion',
        'corporate' => 'evenement-entreprise',
        'year-end' => 'evenement-entreprise',   // eindejaarsfeest (saison sept.–janv.)
        'funeral' => 'funerailles',     // koffietafel
    ],

    // Suivi Google (Analytics 4 + Google Ads). Vide = aucun script chargé.
    'tracking' => [
        'ga4_id' => env('ANTIKA_GA4_ID', ''),                  // G-XXXXXXXXXX
        'ads_id' => env('ANTIKA_GADS_ID', ''),                 // AW-XXXXXXXXXX
        'ads_quote_label' => env('ANTIKA_GADS_QUOTE_LABEL', ''), // libellé de conversion « demande de devis »
        'ads_call_label' => env('ANTIKA_GADS_CALL_LABEL', ''),   // libellé de conversion « clic sur le téléphone »
        'ads_lead_label' => env('ANTIKA_GADS_LEAD_LABEL', ''),   // « demande de rappel » (formulaire court)
        'ads_contact_label' => env('ANTIKA_GADS_CONTACT_LABEL', ''), // « clic WhatsApp / e-mail » (conversion secondaire)
    ],

    // Images du carrousel d'accueil (défilement automatique) : les trois salles
    // (La Villa Feestzaal, Antika Restaurant Zaal, Privat Salon), en WebP + version -sm mobile.
    'hero_images' => [
        '/images/hero/villa-feestzaal.webp',
        '/images/hero/restaurant-zaal.webp',
        '/images/hero/privat-salon.webp',
    ],

    // Quelques chiffres clés affichés sur le site (modifiables).
    // La capacité événements se règle dans l'admin (Réglages devis).
    'stats' => [
        'seats' => '120',
        'years' => '10',
    ],
];
