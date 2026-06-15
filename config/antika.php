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

    'contact' => [
        'address' => 'Pater Penninckxstraat 32, 1982 Zemst',
        'phone' => '+32 495 52 66 56',
        'phone_link' => '+32495526656',
        'email' => 'info@antikaresto.com',
    ],

    'links' => [
        // À remplacer par les URLs réelles fournies par le client.
        'reserve' => env('ANTIKA_RESERVE_URL', 'https://bookings.zenchef.com/'),
        'takeaway' => env('ANTIKA_TAKEAWAY_URL', 'https://www.takeaway.com/'),
        // Simulateur de devis location de salle / événements (plateforme Baba Events).
        'simulator' => env('ANTIKA_SIMULATOR_URL', 'https://baba-event.on-forge.com/simulateur'),
        'maps' => 'https://maps.app.goo.gl/h4vQLeVi1iGkpzSV7',
    ],

    'social' => [
        'instagram' => env('ANTIKA_INSTAGRAM_URL', ''),
        'facebook' => env('ANTIKA_FACEBOOK_URL', ''),
    ],

    // Images du carrousel d'accueil (défilement automatique).
    // 01 = vraie salle Antika, 02 = salle banquet, 04 = carpaccio, 05 = calamars
    // (vraies photos client) ; 03 = visuel gastronomique libre de droits (Pexels).
    'hero_images' => [
        '/images/hero/01.jpg',
        '/images/hero/02.jpg',
        '/images/hero/04.jpg',
        '/images/hero/05.jpg',
        '/images/hero/03.jpg',
    ],

    // Quelques chiffres clés affichés sur le site (modifiables).
    'stats' => [
        'seats' => '120',
        'event_capacity' => '150',
        'years' => '10',
    ],
];
