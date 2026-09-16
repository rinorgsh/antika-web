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

        $locale = $request->session()->get('locale') ?? $this->fromBrowser($request);

        if (! in_array($locale, self::SUPPORTED, true)) {
            $locale = self::DEFAULT;
        }

        app()->setLocale($locale);

        return $next($request);
    }

    /**
     * Première visite sans choix : langue du navigateur si on la propose
     * (un Bruxellois francophone arrive en français), sinon néerlandais.
     */
    private function fromBrowser(Request $request): string
    {
        $preferred = $request->getPreferredLanguage(self::SUPPORTED);

        return $request->headers->has('Accept-Language') && $preferred ? $preferred : self::DEFAULT;
    }
}
