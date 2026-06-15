<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Site vitrine Antika
|--------------------------------------------------------------------------
| Pages publiques. La réservation et la commande à emporter pointent vers
| des plateformes externes (ZenChef / Takeaway), gérées via config/antika.php.
*/

Route::get('/', fn () => Inertia::render('Home'))->name('home');
Route::get('/menu', fn () => Inertia::render('Menu'))->name('menu');
Route::get('/events', fn () => Inertia::render('Events'))->name('events');
Route::get('/contact', fn () => Inertia::render('Contact'))->name('contact');

// Changement de langue : on mémorise le choix en session puis on revient en arrière.
Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, SetLocale::SUPPORTED, true)) {
        session(['locale' => $locale]);
    }

    return back();
})->name('locale.switch');

// SEO : robots.txt et sitemap.xml générés depuis l'URL de config.
Route::get('/robots.txt', function () {
    $content = "User-agent: *\nAllow: /\n\nSitemap: " . config('antika.url') . "/sitemap.xml\n";

    return response($content, 200, ['Content-Type' => 'text/plain']);
});

Route::get('/sitemap.xml', function () {
    $base = config('antika.url');
    $paths = ['/' => '1.0', '/menu' => '0.8', '/events' => '0.8', '/contact' => '0.6'];

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($paths as $path => $priority) {
        $loc = $base . $path;
        $xml .= "    <url><loc>{$loc}</loc><changefreq>monthly</changefreq><priority>{$priority}</priority></url>\n";
    }
    $xml .= '</urlset>';

    return response($xml, 200, ['Content-Type' => 'application/xml']);
});
