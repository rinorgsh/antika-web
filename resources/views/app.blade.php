<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Antika') }}</title>

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
