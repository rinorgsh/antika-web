<?php

use App\Http\Middleware\SetLocale;
use App\Services\MenuBuilder;
use Illuminate\Support\Facades\Http;
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

/*
|--------------------------------------------------------------------------
| Aperçu de la soirée événement (réservé à l'admin connecté)
|--------------------------------------------------------------------------
| On récupère la vraie page event.html du menu QR et on y injecte les
| données fraîches de la base. Aucune duplication du HTML : ce qu'on voit
| ici est exactement ce que verront les clients après publication.
*/
Route::get('/admin/event/preview', function (MenuBuilder $builder) {
    // Le middleware "auth" redirigerait vers une route login inexistante :
    // le panneau Filament a la sienne. On contrôle donc à la main.
    if (! auth()->check()) {
        return redirect('/admin/login');
    }

    $base = rtrim(config('antika.menu_url', 'https://menu.antika-resto.ovh/menu.pdf'), '/').'/';

    $res = Http::timeout(15)->get($base.'event.html');
    abort_unless($res->successful(), 502, "Page événement introuvable sur le menu QR.");

    $html = $res->body();

    // Les images, le logo et les polices sont relatifs à la page d'origine.
    $html = str_replace('<head>', '<head><base href="'.e($base).'">', $html);

    // Données non publiées, injectées avant le chargeur (voir event.html).
    $data = $builder->all()['event-data.js'];
    $banner = '<div style="background:#b5822f;color:#111;font:600 12px/1.4 system-ui;'
        .'padding:8px 14px;text-align:center;letter-spacing:.08em;text-transform:uppercase">'
        .'Aperçu — données non publiées</div>';
    $html = str_replace('</head>', '<script>'.$data.'</script></head>', $html);
    $html = preg_replace('/<body([^>]*)>/', '<body$1>'.$banner, $html, 1);

    return response($html)->header('Content-Type', 'text/html; charset=utf-8');
})->name('event.preview');
