<?php

namespace App\Http\Controllers;

use App\Models\MenuPublication;
use App\Services\MenuBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Carte QR servie par le site (/carte/).
 *
 * Les pages HTML, scripts et images d'origine sont des fichiers statiques
 * dans public/carte (servis directement par nginx). Laravel ne répond que
 * pour ce qui change :
 *   - les fichiers de données publiés depuis l'admin (food-data.js…) ;
 *   - les photos/logos téléversés depuis l'admin (storage/app/public/carte).
 */
class CarteController extends Controller
{
    /** Fichiers de données autorisés => type MIME. */
    public const DATA_FILES = [
        'food-data.js' => 'application/javascript',
        'desserts-data.js' => 'application/javascript',
        'boisson-data.js' => 'application/javascript',
        'event-data.js' => 'application/javascript',
        'config.json' => 'application/json',
    ];

    public function data(string $file): Response
    {
        $files = MenuPublication::currentFiles();
        abort_unless(isset($files[$file]), 404);

        return response($files[$file], 200, [
            'Content-Type' => self::DATA_FILES[$file].'; charset=utf-8',
            // Toujours revalider : une publication doit se voir tout de suite.
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }

    /** Photos et logos téléversés depuis l'admin (noms uniques => cache long). */
    public function upload(string $dir, string $file): Response
    {
        $path = "carte/{$dir}/{$file}";
        abort_unless(Storage::disk('public')->exists($path), 404);

        return response()->file(Storage::disk('public')->path($path), [
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }

    /**
     * Ancienne adresse du menu : les QR codes imprimés pointent sur
     * menu.antika-resto.ovh/menu.pdf (anciennement un PDF, puis un dossier
     * GitHub Pages). On renvoie vers la carte, en gardant la page et la langue.
     */
    public function legacy(Request $request, string $path = ''): RedirectResponse
    {
        $target = rtrim(config('antika.url'), '/').'/carte/'.ltrim($path, '/');

        // En local (ou sur l'adresse on-forge), on reste sur le même hôte.
        if ($request->getHost() === parse_url(config('app.url'), PHP_URL_HOST)) {
            $target = url('/carte/'.ltrim($path, '/'));
        }

        if ($qs = $request->getQueryString()) {
            $target .= '?'.$qs;
        }

        // 302 et non 301 : un 301 reste gravé dans les téléphones pour toujours.
        return redirect()->away($target, 302);
    }

    /** Aperçu de la soirée avec les données NON publiées (admin connecté). */
    public function eventPreview(MenuBuilder $builder): Response|RedirectResponse
    {
        if (! auth()->check()) {
            return redirect('/admin/login');
        }

        $html = file_get_contents(public_path('carte/event.html'));
        $html = str_replace('<head>', '<head><base href="'.e(url('/carte').'/').'">', $html);

        $data = $builder->all()['event-data.js'];
        $banner = '<div style="background:#b5822f;color:#111;font:600 12px/1.4 system-ui;'
            .'padding:8px 14px;text-align:center;letter-spacing:.08em;text-transform:uppercase">'
            .'Aperçu — données non publiées</div>';

        // event.html utilise window.EVENT s'il existe déjà, sans charger event-data.js.
        $html = str_replace('</head>', '<script>'.$data.'</script></head>', $html);
        $html = preg_replace('/<body([^>]*)>/', '<body$1>'.$banner, $html, 1);

        return response($html)->header('Content-Type', 'text/html; charset=utf-8');
    }
}
