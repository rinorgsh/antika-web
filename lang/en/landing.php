<?php

// Ad landing pages (/events/wedding…). One block per occasion.
return [
    'common' => [
        'trust' => ['Up to :capacity guests', 'With or without catering', 'On-site parking', 'Right by the E19'],
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
        'meta_description' => 'Celebrate your wedding at Antika in Zemst: reception halls for up to :capacity guests, seated menu or buffet, open bar. Request your quote online.',
        'eyebrow' => 'Weddings',
        'title' => 'The wedding you dream of, among ponds and greenery',
        'text' => 'Elegant halls, generous food and a team that takes care of everything, from the drinks reception to the last dance.',
        'points' => [
            'Several combinable halls, up to the whole estate',
            'Seated menu or buffet, designed with you',
            'Open bar formulas, cava and drinks reception',
            'Terrace and garden for the reception and photos',
        ],
        'image' => '/images/events/banquet.webp',
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
        'image' => '/images/events/hall.webp',
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
        'image' => '/images/events/garden.webp',
    ],

    'venue-hire' => [
        'meta_title' => 'Venue hire in Zemst, near Mechelen — Antika',
        'meta_description' => 'Party venue for hire in Zemst for every occasion: 40 to :capacity guests, with or without catering, on-site parking. Request your free quote.',
        'eyebrow' => 'Venue hire',
        'title' => 'A venue for every celebration, with or without catering',
        'text' => 'Birthday, wedding, communion, company party or funeral reception: five spaces by the fishponds of Elewijt, for 40 to :capacity guests. The venue only, or everything taken care of by our kitchen.',
        'points' => [
            'Five spaces, combinable up to the entire estate',
            'The venue only, or catering and drinks by our team',
            'Seated menu, buffet or reception with bites',
            'On-site parking, easy access via the E19',
        ],
        'image' => '/images/events/hall.webp',
    ],

    'funeral' => [
        'meta_title' => 'Funeral reception in Zemst — Antika',
        'meta_description' => 'A caring funeral reception in Zemst: private room, coffee and cake, sandwiches or a hot meal. Call us, we arrange everything quickly.',
        'eyebrow' => 'Funeral reception',
        'title' => 'A peaceful place to remember together',
        'text' => 'After the farewell, we prepare a simple, caring reception so you can be there for your family. We help you quickly and personally.',
        'points' => [
            'Private room suited to your group',
            'Coffee and cake, sandwiches or a hot meal',
            'Personal support, even at short notice',
            'On-site parking for family and friends',
        ],
        'image' => '/images/events/garden.webp',
        'primary' => 'callback',
        'cta' => 'Request a proposal',
        'steps' => [
            ['title' => 'Contact', 'text' => 'Call us or leave your number.'],
            ['title' => 'Proposal', 'text' => 'We plan the reception together.'],
            ['title' => 'Reception', 'text' => 'We take care of everything, quietly.'],
        ],
    ],

    'corporate' => [
        'meta_title' => 'Company party, reception or seminar in Zemst — Antika',
        'meta_description' => 'Staff party, reception, team dinner or seminar at Antika in Zemst, right by the E19 between Brussels and Mechelen. Up to :capacity guests, one invoice. Free quote.',
        'eyebrow' => 'Companies',
        'title' => 'Your company event, from team dinner to staff party',
        'text' => 'Staff party, client reception, seminar or year-end party: a unique venue right by the E19, a team that handles everything and one clear invoice in your company’s name.',
        'points' => [
            'From a meeting in the private lounge up to :capacity guests',
            'Walking dinner, buffet or seated dinner',
            'Per-person drinks packages, no surprises',
            'Screen, projector, sound and DJ on request',
            'One invoice in your company’s name',
        ],
        'formats_title' => 'Packages for companies',
        'formats' => [
            ['title' => 'Year-end party', 'text' => 'Dinner or buffet, drinks package and DJ for your staff, in December or January.'],
            ['title' => 'Reception & walking dinner', 'text' => 'Standing, with bites and drinks: ideal for networking with clients and partners.'],
            ['title' => 'Seminar & meeting', 'text' => 'Private room with screen and projector, with coffee, lunch or dinner.'],
            ['title' => 'Team dinner', 'text' => 'A refined seated menu for your team, in a private space.'],
        ],
        'image' => '/images/events/hall.webp',
    ],

    'year-end' => [
        'meta_title' => 'Company year-end party in Zemst — Antika',
        'meta_description' => 'Your company’s year-end party at Antika in Zemst: private room for up to :capacity guests, dinner or buffet, drinks and DJ. December dates go fast: request your quote now.',
        'eyebrow' => 'Year-end party',
        'title' => 'Your company’s year-end party, fully taken care of',
        'text' => 'Dinner or buffet, open bar, DJ and decoration: we handle everything, you enjoy it with your team. The most popular dates in December and January fill up fast.',
        'points' => [
            'Private room for 40 to :capacity people',
            'Seated dinner, buffet or walking dinner',
            'Open-bar drinks package, per person',
            'DJ, decoration and photo booth on request',
            'One invoice in your company’s name',
        ],
        'image' => '/images/events/banquet.webp',
        'cta' => 'Check my date',
    ],
];
