<?php

// Landingspagina's voor advertenties (/events/wedding…). Eén blok per gelegenheid.
return [
    'common' => [
        'trust' => ['Tot :capacity gasten', 'Met of zonder catering', 'Parking ter plaatse', 'Vlak bij de E19'],
        'cta' => 'Stel mijn offerte samen',
        'cta_note' => 'Gratis en vrijblijvend — snel antwoord.',
        'or_call' => 'of bel ons op',
        'why' => 'Waarom Antika',
        'steps_title' => 'In drie stappen',
        'steps' => [
            ['title' => 'Samenstellen', 'text' => 'Zaal, menu, dranken: uw keuzes in twee minuten.'],
            ['title' => 'Bespreken', 'text' => 'We bellen u terug om elk detail af te stemmen.'],
            ['title' => 'Genieten', 'text' => 'Op de grote dag zorgen wij voor alles.'],
        ],
        'capacity' => 'Tot :count gasten',
        'location' => 'Zemst · tussen Brussel, Mechelen en Leuven',
        'parking' => 'Parking ter plaatse',
    ],

    'wedding' => [
        'meta_title' => 'Feestzaal voor uw huwelijk in Zemst — Antika',
        'meta_description' => 'Vier uw huwelijk bij Antika in Zemst: feestzalen tot :capacity gasten, menu aan tafel of buffet, drankenformule à volonté. Vraag online uw offerte aan.',
        'eyebrow' => 'Huwelijksfeesten',
        'title' => 'Het huwelijksfeest van uw dromen, tussen vijvers en groen',
        'text' => 'Stijlvolle zalen, royale keuken en een team dat alles regelt, van de receptie tot de laatste dans.',
        'points' => [
            'Meerdere zalen te combineren, tot het volledige domein',
            'Menu aan tafel of buffet, samen met u uitgewerkt',
            'Drankenformules à volonté, cava en aperitief',
            'Terras en tuin voor receptie en foto\'s',
        ],
        'image' => '/images/events/banquet.webp',
    ],

    'birthday' => [
        'meta_title' => 'Zaal huren voor een verjaardag in Zemst — Antika',
        'meta_description' => 'Verjaardag, 18, 30 of 50 jaar… Huur een zaal bij Antika in Zemst met eten en drank inbegrepen. Online offerte in twee minuten.',
        'eyebrow' => 'Verjaardagen',
        'title' => 'Een onvergetelijke verjaardag, zonder zorgen',
        'text' => 'Van een diner in kleine kring tot een groot feest: een zaal op maat, lekker eten en drank à volonté.',
        'points' => [
            'Privésalon tot 40 gasten',
            'Royale buffetten of menu aan tafel',
            'Dranken à volonté: frisdrank, bier, wijn',
            'DJ, decoratie en taart op aanvraag',
        ],
        'image' => '/images/events/hall.webp',
    ],

    'communion' => [
        'meta_title' => 'Zaal voor communiefeest en doopfeest in Zemst — Antika',
        'meta_description' => 'Communie, doopsel of familiefeest bij Antika in Zemst: privézaal, kindermenu, tuin. Ontvang uw offerte op maat.',
        'eyebrow' => 'Communie & doopsel',
        'title' => 'Een mooi familiefeest, voor groot en klein',
        'text' => 'Een warme zaal, een menu voor volwassenen én kinderen, en een tuin waar de kleinsten kunnen spelen.',
        'points' => [
            'Kindermenu op aanvraag',
            'Privézaal voor de hele dag',
            'Tuin en terras',
            'Parking ter plaatse voor de hele familie',
        ],
        'image' => '/images/events/garden.webp',
    ],

    'venue-hire' => [
        'meta_title' => 'Feestzaal huren in Zemst, bij Mechelen — Antika',
        'meta_description' => 'Feestzaal te huur in Zemst voor elk feest: van 40 tot :capacity gasten, met of zonder catering, parking ter plaatse. Vraag gratis uw offerte aan.',
        'eyebrow' => 'Zaalverhuur',
        'title' => 'Een feestzaal voor elk feest, met of zonder catering',
        'text' => 'Verjaardag, huwelijk, communie, bedrijfsfeest of koffietafel: vijf ruimtes tussen de visvijvers van Elewijt, van 40 tot :capacity gasten. Enkel de zaal, of alles verzorgd door onze keuken.',
        'points' => [
            'Vijf ruimtes, te combineren tot het volledige domein',
            'Enkel de zaal, of catering en dranken door ons team',
            'Menu aan tafel, buffet of receptie met hapjes',
            'Parking ter plaatse, vlot bereikbaar via de E19',
        ],
        'image' => '/images/events/hall.webp',
    ],

    'funeral' => [
        'meta_title' => 'Koffietafel na een uitvaart in Zemst — Antika',
        'meta_description' => 'Een verzorgde koffietafel na de uitvaart in Zemst: privézaal, koffie en taart, broodjes of een warme maaltijd. Bel ons, we regelen het snel.',
        'eyebrow' => 'Koffietafel',
        'title' => 'Een rustige plek om samen te herdenken',
        'text' => 'Na het afscheid zorgen wij voor een sobere, verzorgde koffietafel, zodat u er kunt zijn voor uw familie. We helpen u snel en persoonlijk verder.',
        'points' => [
            'Privézaal op maat van uw gezelschap',
            'Koffie en taart, broodjes of een warme maaltijd',
            'Persoonlijke begeleiding, ook op korte termijn',
            'Parking ter plaatse voor familie en vrienden',
        ],
        'image' => '/images/events/garden.webp',
        'primary' => 'callback',
        'cta' => 'Vraag een voorstel aan',
        'steps' => [
            ['title' => 'Contact', 'text' => 'Bel ons of laat uw nummer achter.'],
            ['title' => 'Voorstel', 'text' => 'We stellen samen de koffietafel samen.'],
            ['title' => 'Ontvangst', 'text' => 'Wij zorgen voor alles, in alle rust.'],
        ],
    ],

    'corporate' => [
        'meta_title' => 'Bedrijfsfeest, personeelsfeest of seminarie in Zemst — Antika',
        'meta_description' => 'Personeelsfeest, receptie, teamdiner of seminarie bij Antika in Zemst, vlak bij de E19 tussen Brussel en Mechelen. Tot :capacity gasten, één factuur. Vraag gratis uw offerte aan.',
        'eyebrow' => 'Bedrijven',
        'title' => 'Uw bedrijfsevent, van teamdiner tot personeelsfeest',
        'text' => 'Personeelsfeest, klantenreceptie, seminarie of eindejaarsfeest: een unieke locatie vlak bij de E19, een team dat alles regelt en één duidelijke factuur op naam van uw bedrijf.',
        'points' => [
            'Van een vergadering in het privésalon tot :capacity gasten',
            'Walking dinner, buffet of diner aan tafel',
            'Drankenformules per persoon, zonder verrassingen',
            'Scherm, beamer, geluid en DJ op aanvraag',
            'Eén factuur op naam van uw bedrijf',
        ],
        'formats_title' => 'Formules voor bedrijven',
        'formats' => [
            ['title' => 'Eindejaarsfeest', 'text' => 'Diner of buffet, drankenformule en DJ voor uw personeel, in december of januari.'],
            ['title' => 'Receptie & walking dinner', 'text' => 'Staand, met hapjes en drank: ideaal om te netwerken met klanten en partners.'],
            ['title' => 'Seminarie & vergadering', 'text' => 'Privézaal met scherm en beamer, aangevuld met koffie, lunch of diner.'],
            ['title' => 'Teamdiner', 'text' => 'Een verzorgd menu aan tafel voor uw team, in een eigen ruimte.'],
        ],
        'image' => '/images/events/hall.webp',
    ],

    'year-end' => [
        'meta_title' => 'Eindejaarsfeest of kerstfeest voor uw personeel in Zemst — Antika',
        'meta_description' => 'Het eindejaarsfeest van uw bedrijf bij Antika in Zemst: privézaal tot :capacity gasten, diner of buffet, drankenformule en DJ. Data in december en januari gaan snel: vraag nu uw offerte aan.',
        'eyebrow' => 'Eindejaarsfeest',
        'title' => 'Het eindejaarsfeest van uw bedrijf, volledig geregeld',
        'text' => 'Diner of buffet, drankenformule à volonté, DJ en decoratie: wij zorgen voor alles, u geniet met uw team. De populairste data in december en januari zijn snel volzet.',
        'points' => [
            'Privézaal van 40 tot :capacity personen',
            'Diner aan tafel, buffet of walking dinner',
            'Drankenformule à volonté, per persoon',
            'DJ, decoratie en photobooth op aanvraag',
            'Eén factuur op naam van uw bedrijf',
        ],
        'image' => '/images/events/banquet.webp',
        'cta' => 'Controleer uw datum',
    ],
];
