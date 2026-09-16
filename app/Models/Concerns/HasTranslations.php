<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

/**
 * Champs traduits stockés en JSON {nl, fr, en}.
 *
 * Repli : langue demandée, puis français (langue des données reprises de
 * l'ancien simulateur), puis néerlandais, puis anglais. Un texte pas encore
 * traduit s'affiche donc dans la langue d'origine plutôt que vide.
 */
trait HasTranslations
{
    public const LOCALES = ['nl' => 'Nederlands', 'fr' => 'Français', 'en' => 'English'];

    public function tr(string $attribute, ?string $locale = null): string
    {
        $value = $this->getAttribute($attribute);
        if (! is_array($value)) {
            return (string) $value;
        }

        foreach (array_unique([$locale ?? app()->getLocale(), 'fr', 'nl', 'en']) as $l) {
            if (filled($value[$l] ?? null)) {
                return $value[$l];
            }
        }

        return '';
    }

    /** URL publique d'une image du disque « public » (ou URL absolue telle quelle). */
    public static function imageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }
        if (preg_match('#^https?://#', $path)) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}
