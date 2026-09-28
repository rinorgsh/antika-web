<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Langues supportées par le site. Le néerlandais est la langue par défaut
     * (le restaurant est situé à Zemst, en Flandre).
     */
    public const SUPPORTED = ['nl', 'fr', 'en'];

    public const DEFAULT = 'nl';

    public function handle(Request $request, Closure $next): Response
    {
        // ?lang=fr dans l'adresse (liens d'annonces, QR…) : prioritaire et mémorisé.
        $requested = $request->query('lang');
        if (is_string($requested) && in_array($requested, self::SUPPORTED, true)) {
            $request->session()->put('locale', $requested);
        }

        // Pas de choix explicite : néerlandais, quelle que soit la langue du navigateur.
        // Les annonces FR pointent vers ?lang=fr ; le sélecteur de langue reste disponible.
        $locale = $request->session()->get('locale', self::DEFAULT);

        if (! in_array($locale, self::SUPPORTED, true)) {
            $locale = self::DEFAULT;
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
