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
        $locale = $request->session()->get('locale', self::DEFAULT);

        if (! in_array($locale, self::SUPPORTED, true)) {
            $locale = self::DEFAULT;
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
