<?php

namespace App\Http\Middleware;

use App\Models\EventSetting;
use App\Support\EventsContent;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
            'translations' => EventsContent::withCapacity(trans('site')),
            // Coordonnées et liens externes (identiques quelle que soit la langue).
            // Sans le bloc « events » : il contient les e-mails de notification.
            'site' => Arr::except(config('antika'), ['events']),
            // Réglables dans l'admin (Location de salle > Réglages devis).
            'marketing' => [
                'capacity' => EventSetting::get('max_capacity'),
                'whatsapp' => preg_replace('/\D/', '', (string) EventSetting::get('whatsapp')),
                'rating' => EventSetting::get('google_rating'),
                'reviews_count' => EventSetting::get('google_reviews_count'),
                'reviews_url' => EventSetting::get('google_reviews_url'),
                'reviews' => EventSetting::reviews(),
                'privacy_url' => EventSetting::get('privacy_url') ?: '/privacy',
            ],
        ];
    }
}
