<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            ...parent::share($request),
            // Internationalisation : la langue active, la liste des langues
            // disponibles, et toutes les chaînes traduites de la langue active.
            'locale' => $locale,
            'locales' => SetLocale::SUPPORTED,
            'translations' => trans('site'),
            // Coordonnées et liens externes (identiques quelle que soit la langue).
            'site' => config('antika'),
        ];
    }
}
