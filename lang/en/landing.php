<?php

// Ad landing pages (/events/wedding…). One block per occasion.
return [
    'common' => [
        'cta' => 'Build my quote',
        'cta_note' => 'Free and without obligation — quick reply.',
        'or_call' => 'or call us on',
        'why' => 'Why Antika',
        'steps_title' => 'In three steps',
        'steps' => [
            ['title' => 'Build', 'text' => 'Venue, menu, drinks: make your choices in two minutes.'],
            ['title' => 'Talk', 'text' => 'We call you back to fine-tune every detail.'],
            ['title' => 'Enjoy', 'text' => 'On the day, we take care of everything.'],
        ],
        'capacity' => 'Up to :count guests',
        'location' => 'Zemst · between Brussels, Mechelen and Leuven',
        'parking' => 'On-site parking',
    ],

    'wedding' => [
        'meta_title' => 'Wedding venue in Zemst — Antika',
        'meta_description' => 'Celebrate your wedding at Antika in Zemst: reception halls for up to 350 guests, seated menu or buffet, open bar. Request your quote online.',
        'eyebrow' => 'Weddings',
        'title' => 'The wedding you dream of, among ponds and greenery',
        'text' => 'Elegant halls, generous food and a team that takes care of everything, from the drinks reception to the last dance.',
        'points' => [
            'Several combinable halls, up to the whole estate',
            'Seated menu or buffet, designed with you',
            'Open bar formulas, cava and drinks reception',
            'Terrace and garden for the reception and photos',
        ],
        'image' => '/images/events/banquet.jpg',
    ],

    'birthday' => [
        'meta_title' => 'Birthday party venue in Zemst — Antika',
        'meta_description' => 'Birthday, 18th, 30th, 50th… Hire a private room at Antika in Zemst with food and drinks included. Online quote in two minutes.',
        'eyebrow' => 'Birthdays',
        'title' => 'A memorable birthday, with nothing to organise',
        'text' => 'From an intimate dinner to a big party: a room that fits, delicious food and unlimited drinks.',
        'points' => [
            'Private lounge for up to 40 guests',
            'Generous buffets or seated menu',
            'Unlimited drinks: soft drinks, beer, wine',
            'DJ, decoration and cake on request',
        ],
        'image' => '/images/events/hall.jpg',
    ],

    'communion' => [
        'meta_title' => 'Communion & christening venue in Zemst — Antika',
        'meta_description' => 'Communion, christening or family party at Antika in Zemst: private room, children\'s menu, garden. Get your tailor-made quote.',
        'eyebrow' => 'Communions & christenings',
        'title' => 'A lovely family celebration, for young and old',
        'text' => 'A warm room, a menu for adults and children alike, and a garden where the little ones can play.',
        'points' => [
            'Children\'s menu on request',
            'Private room for the whole day',
            'Garden and terrace',
            'On-site parking for the whole family',
        ],
        'image' => '/images/events/garden.jpg',
    ],

    'corporate' => [
        'meta_title' => 'Corporate event venue in Zemst — Antika',
        'meta_description' => 'Team dinner, reception, seminar or staff party at Antika in Zemst, easy to reach from Brussels and Mechelen. Online quote.',
        'eyebrow' => 'Companies',
        'title' => 'Receptions and team dinners, made easy',
        'text' => 'Staff party, client reception or seminar: a unique setting close to the E19, with a clear invoice.',
        'points' => [
            'Easy access from Brussels and Mechelen',
            'Buffet or seated menu',
            'Per-person drinks packages',
            'Screen, sound and lighting on request',
        ],
        'image' => '/images/events/hall.jpg',
    ],
];
