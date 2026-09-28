<?php

namespace App\Support;

use App\Models\EventSetting;
use App\Models\Venue;
use App\Services\Thumbnails;

/** Données communes des pages publiques « événements » (page Événements, pages d'annonces). */
class EventsContent
{
    /** Remplace :capacity dans des textes traduits par la capacité réglée dans l'admin. */
    public static function withCapacity(mixed $strings): mixed
    {
        $capacity = (string) EventSetting::get('max_capacity');
        if (is_string($strings)) {
            return str_replace(':capacity', $capacity, $strings);
        }
        if (is_array($strings)) {
            array_walk_recursive($strings, function (&$v) use ($capacity) {
                if (is_string($v)) {
                    $v = str_replace(':capacity', $capacity, $v);
                }
            });
        }

        return $strings;
    }

    /** Salles actives, dans la langue du visiteur. */
    public static function venues(): array
    {
        $l = app()->getLocale();

        return Venue::active()->ordered()->with('venueImages')->get()->map(fn (Venue $v) => [
            'id' => $v->id,
            'name' => $v->tr('name', $l),
            'description' => $v->tr('short_description', $l) ?: $v->tr('description', $l),
            'image' => Thumbnails::url($v->coverImagePath(), 800),
            'capacity' => $v->capacity_seated,
            'has_parking' => $v->has_parking,
            'has_vestiaire' => $v->has_vestiaire,
            'has_private_toilets' => $v->has_private_toilets,
        ])->all();
    }
}
