<?php

use App\Http\Middleware\SetLocale;
use App\Http\Controllers\CarteController;
use App\Http\Controllers\SimulatorController;
use Illuminate\Support\Facades\Storage;
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

/*
|--------------------------------------------------------------------------
| Simulateur de devis événements (ex baba-event.on-forge.com/simulateur)
|--------------------------------------------------------------------------
*/
Route::prefix('events/simulator')->name('simulator.')->controller(SimulatorController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/availability', 'availability')->middleware('throttle:60,1')->name('availability');
    Route::post('/', 'submit')->middleware('throttle:6,1')->name('submit');
    Route::get('/confirmation/{quoteNumber}', 'confirmation')->name('confirmation');
    Route::get('/pdf/{quoteNumber}', 'pdf')->name('pdf');
});

// Pages d'atterrissage par occasion (Google Ads) : /events/wedding, /events/birthday…
Route::get('/events/{occasion}', function (string $occasion) {
    return Inertia::render('EventLanding', [
        'occasion' => $occasion,
        'eventType' => config("antika.landings.{$occasion}"),
        'strings' => ['common' => trans('landing.common'), $occasion => trans("landing.{$occasion}")],
    ]);
})->whereIn('occasion', array_keys(config('antika.landings')))->name('events.landing');
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
    $content = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /events/simulator/confirmation\nDisallow: /events/simulator/pdf\n\nSitemap: " . config('antika.url') . "/sitemap.xml\n";

    return response($content, 200, ['Content-Type' => 'text/plain']);
});

Route::get('/sitemap.xml', function () {
    $base = config('antika.url');
    $paths = ['/' => '1.0', '/menu' => '0.8', '/carte/' => '0.7', '/events' => '0.9', '/events/simulator' => '0.9'];
    foreach (array_keys(config('antika.landings')) as $occasion) {
        $paths["/events/{$occasion}"] = '0.8';
    }
    $paths['/contact'] = '0.6';

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($paths as $path => $priority) {
        $loc = $base . $path;
        $xml .= "    <url><loc>{$loc}</loc><changefreq>monthly</changefreq><priority>{$priority}</priority></url>\n";
    }
    $xml .= '</urlset>';

    return response($xml, 200, ['Content-Type' => 'application/xml']);
});

/*
|--------------------------------------------------------------------------
| Carte QR (/carte/)
|--------------------------------------------------------------------------
| Pages et images d'origine : fichiers statiques de public/carte. Ici
| uniquement les données publiées depuis l'admin et les images téléversées.
*/
Route::redirect('/carte', '/carte/', 302);
Route::get('/carte/{file}', [CarteController::class, 'data'])
    ->whereIn('file', array_keys(CarteController::DATA_FILES))
    ->name('carte.data');
Route::get('/carte/{dir}/{file}', [CarteController::class, 'upload'])
    ->whereIn('dir', ['photos', 'drinks', 'logos'])
    ->where('file', '[A-Za-z0-9._-]+')
    ->name('carte.upload');

// QR codes imprimés : menu.antika-resto.ovh/menu.pdf(/…) -> antikaresto.com/carte/…
Route::get('/menu.pdf/{path?}', [CarteController::class, 'legacy'])
    ->where('path', '.*')
    ->name('carte.legacy');

// Aperçu de la soirée avec les données non publiées (admin connecté).
Route::get('/admin/event/preview', [CarteController::class, 'eventPreview'])->name('event.preview');

// Fichiers du disque « public » (images téléversées) sans lien symbolique
// public/storage à recréer à chaque déploiement.
Route::get('/media/{path}', function (string $path) {
    abort_if(str_contains($path, '..'), 404);
    abort_unless(Storage::disk('public')->exists($path), 404);

    return response()->file(Storage::disk('public')->path($path), [
        'Cache-Control' => 'public, max-age=604800',
    ]);
})->where('path', '.*')->name('media');
