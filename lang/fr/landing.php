<?php

// Pages d'atterrissage des annonces (/events/wedding…). Une entrée par occasion.
return [
    'common' => [
        'trust' => ['Jusqu\'à :capacity invités', 'Avec ou sans traiteur', 'Parking sur place', 'À deux pas de l\'E19'],
        'cta' => 'Composer mon devis',
        'cta_note' => 'Gratuit et sans engagement — réponse rapide.',
        'or_call' => 'ou appelez-nous au',
        'why' => 'Pourquoi chez Antika',
        'steps_title' => 'En trois étapes',
        'steps' => [
            ['title' => 'Composez', 'text' => 'Salle, menu, boissons : faites vos choix en deux minutes.'],
            ['title' => 'On en parle', 'text' => 'Nous vous rappelons pour affiner chaque détail.'],
            ['title' => 'Vous profitez', 'text' => 'Le jour J, nous nous occupons de tout.'],
        ],
        'capacity' => 'Jusqu\'à :count invités',
        'location' => 'Zemst · entre Bruxelles, Malines et Louvain',
        'parking' => 'Parking sur place',
    ],

    'wedding' => [
        'meta_title' => 'Salle de mariage à Zemst — Antika',
        'meta_description' => 'Fêtez votre mariage chez Antika à Zemst : salles de réception jusqu\'à :capacity invités, menu servi ou buffet, boissons à volonté. Demandez votre devis en ligne.',
        'eyebrow' => 'Mariages',
        'title' => 'Le mariage dont vous rêvez, entre étangs et verdure',
        'text' => 'Salles élégantes, cuisine généreuse et une équipe qui s\'occupe de tout, de l\'apéritif à la dernière danse.',
        'points' => [
            'Plusieurs salles combinables, jusqu\'au domaine entier',
            'Menu servi à table ou buffet, pensé avec vous',
            'Formules boissons à volonté, cava et apéritif',
            'Terrasse et jardin pour la réception et les photos',
        ],
        'image' => '/images/events/banquet.webp',
    ],

    'birthday' => [
        'meta_title' => 'Salle pour anniversaire à Zemst — Antika',
        'meta_description' => 'Anniversaire, 18, 30, 50 ans… Privatisez une salle chez Antika à Zemst avec menu et boissons compris. Devis en ligne en deux minutes.',
        'eyebrow' => 'Anniversaires',
        'title' => 'Un anniversaire mémorable, sans rien organiser',
        'text' => 'Du dîner en petit comité à la grande fête : une salle à votre mesure, un menu gourmand et des boissons à volonté.',
        'points' => [
            'Salon privé jusqu\'à 40 invités',
            'Buffets généreux ou menu servi',
            'Boissons à volonté : softs, bières, vins',
            'DJ, décoration et gâteau sur demande',
        ],
        'image' => '/images/events/hall.webp',
    ],

    'communion' => [
        'meta_title' => 'Salle pour communion et baptême à Zemst — Antika',
        'meta_description' => 'Communion, baptême ou fête de famille chez Antika à Zemst : salle privée, menu enfant, jardin. Recevez votre devis sur mesure.',
        'eyebrow' => 'Communions & baptêmes',
        'title' => 'Une belle fête de famille, petits et grands compris',
        'text' => 'Une salle chaleureuse, un menu pour les adultes comme pour les enfants, et un jardin où les plus jeunes peuvent profiter.',
        'points' => [
            'Menu enfant sur demande',
            'Salle privée à la journée',
            'Jardin et terrasse',
            'Parking sur place pour toute la famille',
        ],
        'image' => '/images/events/garden.webp',
    ],

    'venue-hire' => [
        'meta_title' => 'Location de salle à Zemst, près de Malines — Antika',
        'meta_description' => 'Salle à louer à Zemst pour toutes vos fêtes : de 40 à :capacity invités, avec ou sans traiteur, parking sur place. Demandez gratuitement votre devis.',
        'eyebrow' => 'Location de salle',
        'title' => 'Une salle pour chaque fête, avec ou sans traiteur',
        'text' => 'Anniversaire, mariage, communion, fête d\'entreprise ou réception après funérailles : cinq espaces au bord des étangs d\'Elewijt, de 40 à :capacity invités. La salle seule, ou tout organisé par notre cuisine.',
        'points' => [
            'Cinq espaces, combinables jusqu\'au domaine entier',
            'La salle seule, ou traiteur et boissons par notre équipe',
            'Menu servi, buffet ou réception avec bouchées',
            'Parking sur place, accès facile par l\'E19',
        ],
        'image' => '/images/events/hall.webp',
    ],

    'funeral' => [
        'meta_title' => 'Réception après funérailles à Zemst — Antika',
        'meta_description' => 'Une réception soignée après les funérailles à Zemst : salle privée, café et gâteaux, sandwiches ou repas chaud. Appelez-nous, nous organisons tout rapidement.',
        'eyebrow' => 'Réception après funérailles',
        'title' => 'Un lieu paisible pour se souvenir ensemble',
        'text' => 'Après l\'adieu, nous préparons une réception sobre et soignée, pour que vous puissiez être auprès de votre famille. Nous vous aidons rapidement et personnellement.',
        'points' => [
            'Salle privée adaptée à votre groupe',
            'Café et gâteaux, sandwiches ou repas chaud',
            'Accompagnement personnel, même dans un délai court',
            'Parking sur place pour la famille et les amis',
        ],
        'image' => '/images/events/garden.webp',
        'primary' => 'callback',
        'cta' => 'Demander une proposition',
        'steps' => [
            ['title' => 'Contact', 'text' => 'Appelez-nous ou laissez votre numéro.'],
            ['title' => 'Proposition', 'text' => 'Nous composons la réception ensemble.'],
            ['title' => 'Accueil', 'text' => 'Nous nous occupons de tout, en toute discrétion.'],
        ],
    ],

    'corporate' => [
        'meta_title' => 'Fête du personnel, réception ou séminaire à Zemst — Antika',
        'meta_description' => 'Fête du personnel, réception, dîner d\'équipe ou séminaire chez Antika à Zemst, près de l\'E19 entre Bruxelles et Malines. Jusqu\'à :capacity invités, une seule facture. Devis gratuit.',
        'eyebrow' => 'Entreprises',
        'title' => 'Votre événement d\'entreprise, du dîner d\'équipe à la fête du personnel',
        'text' => 'Fête du personnel, réception clients, séminaire ou fête de fin d\'année : un lieu unique près de l\'E19, une équipe qui s\'occupe de tout et une seule facture au nom de votre société.',
        'points' => [
            'De la réunion en salon privé jusqu\'à :capacity invités',
            'Walking dinner, buffet ou dîner à table',
            'Formules boissons par personne, sans surprise',
            'Écran, projecteur, sono et DJ sur demande',
            'Une seule facture au nom de votre société',
        ],
        'formats_title' => 'Formules entreprises',
        'formats' => [
            ['title' => 'Fête de fin d\'année', 'text' => 'Dîner ou buffet, formule boissons et DJ pour votre personnel, en décembre ou janvier.'],
            ['title' => 'Réception & walking dinner', 'text' => 'Debout, avec bouchées et boissons : idéal pour réseauter avec clients et partenaires.'],
            ['title' => 'Séminaire & réunion', 'text' => 'Salle privée avec écran et projecteur, complétée d\'un café, d\'un lunch ou d\'un dîner.'],
            ['title' => 'Dîner d\'équipe', 'text' => 'Un menu soigné servi à table pour votre équipe, dans un espace privé.'],
        ],
        'image' => '/images/events/hall.webp',
    ],

    'year-end' => [
        'meta_title' => 'Fête de fin d\'année d\'entreprise à Zemst — Antika',
        'meta_description' => 'La fête de fin d\'année de votre entreprise chez Antika à Zemst : salle privée jusqu\'à :capacity invités, dîner ou buffet, boissons et DJ. Les dates de décembre partent vite : demandez votre devis.',
        'eyebrow' => 'Fête de fin d\'année',
        'title' => 'La fête de fin d\'année de votre entreprise, clé en main',
        'text' => 'Dîner ou buffet, boissons à volonté, DJ et décoration : nous nous occupons de tout, vous profitez avec votre équipe. Les dates les plus demandées de décembre et janvier partent vite.',
        'points' => [
            'Salle privée de 40 à :capacity personnes',
            'Dîner à table, buffet ou walking dinner',
            'Formule boissons à volonté, par personne',
            'DJ, décoration et photobooth sur demande',
            'Une seule facture au nom de votre société',
        ],
        'image' => '/images/events/banquet.webp',
        'cta' => 'Vérifier ma date',
    ],
];
