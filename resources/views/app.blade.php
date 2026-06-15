<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php
            $locale = app()->getLocale();
            $site = config('antika');
            $url = $site['url'];
            $canonical = $url . (request()->getPathInfo() === '/' ? '' : request()->getPathInfo());
            $metaTitle = trans('site.meta.title');
            $metaDesc = trans('site.meta.description');
            $ogImage = $url . '/images/og-image.jpg';
            $ogLocale = ['nl' => 'nl_BE', 'fr' => 'fr_BE', 'en' => 'en_GB'][$locale] ?? 'nl_BE';
            $jsonLd = [
                '@context' => 'https://schema.org',
                '@type' => 'Restaurant',
                'name' => 'Antika Restaurant',
                'image' => $ogImage,
                'logo' => $url . '/images/logo.png',
                '@id' => $url,
                'url' => $url,
                'telephone' => $site['contact']['phone'],
                'email' => $site['contact']['email'],
                'servesCuisine' => $site['seo']['cuisine'],
                'priceRange' => $site['seo']['price_range'],
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => 'Pater Penninckxstraat 32',
                    'postalCode' => '1982',
                    'addressLocality' => 'Zemst',
                    'addressCountry' => 'BE',
                ],
                'geo' => [
                    '@type' => 'GeoCoordinates',
                    'latitude' => $site['seo']['geo']['lat'],
                    'longitude' => $site['seo']['geo']['lng'],
                ],
                'hasMap' => $site['links']['maps'],
                'acceptsReservations' => true,
                'openingHoursSpecification' => [
                    ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => ['Monday', 'Wednesday', 'Thursday'], 'opens' => '17:00', 'closes' => '23:59'],
                    ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => ['Friday', 'Saturday', 'Sunday'], 'opens' => '12:00', 'closes' => '23:59'],
                ],
            ];
            $social = array_values(array_filter([$site['social']['instagram'] ?? '', $site['social']['facebook'] ?? '']));
            if ($social) { $jsonLd['sameAs'] = $social; }
        @endphp

        <title inertia>{{ $metaTitle }}</title>
        <meta name="description" content="{{ $metaDesc }}">
        <meta name="robots" content="index, follow">
        <meta name="author" content="Antika Restaurant">
        <meta name="theme-color" content="#0c0a09">
        <link rel="canonical" href="{{ $canonical }}">

        <!-- Open Graph -->
        <meta property="og:type" content="restaurant">
        <meta property="og:site_name" content="Antika Restaurant">
        <meta property="og:title" content="{{ $metaTitle }}">
        <meta property="og:description" content="{{ $metaDesc }}">
        <meta property="og:url" content="{{ $canonical }}">
        <meta property="og:image" content="{{ $ogImage }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:locale" content="{{ $ogLocale }}">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $metaTitle }}">
        <meta name="twitter:description" content="{{ $metaDesc }}">
        <meta name="twitter:image" content="{{ $ogImage }}">

        <!-- Favicons -->
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="manifest" href="/site.webmanifest">

        <!-- Données structurées Restaurant -->
        <script type="application/ld+json">@json($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|playfair-display:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia

        {{-- Widget de réservation Zenchef (modale déclenchée par les boutons data-zc-action="open") --}}
        <script>;(function (d, s, id) {const el = d.getElementsByTagName(s)[0]; if (d.getElementById(id) || el.parentNode == null) {return;} var js = d.createElement(s);  js.id = id; js.async = true; js.src = 'https://sdk.zenchef.com/v1/sdk.min.js';  el.parentNode.insertBefore(js, el); })(document, 'script', 'zenchef-sdk')</script>
        <div
            class="zc-widget-config"
            data-restaurant="378407"
            data-open="2000"></div>
    </body>
</html>
