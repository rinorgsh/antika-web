<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;

/**
 * Champ traduit du simulateur : un bloc « Nom » avec NL / FR / EN côte à côte.
 * Le français est obligatoire (langue de repli sur le site).
 */
class Translatable
{
    public static function field(string $name, string $label, bool $textarea = false, bool $required = false): Fieldset
    {
        $inputs = [];
        foreach (['nl' => 'NL', 'fr' => 'FR', 'en' => 'EN'] as $code => $short) {
            $input = $textarea
                ? Textarea::make("{$name}.{$code}")->rows(2)
                : TextInput::make("{$name}.{$code}")->maxLength(255);
            $input->label($short)->required($required && $code === 'fr');
            $inputs[] = $input;
        }

        return Fieldset::make($label)->schema($inputs)->columns(3)->columnSpanFull();
    }

    /** Libellé français (ou première langue remplie) pour les tableaux. */
    public static function label(array|string|null $value): string
    {
        if (! is_array($value)) {
            return filled($value) ? $value : '—';
        }
        foreach (['fr', 'nl', 'en'] as $l) {
            if (filled($value[$l] ?? null)) {
                return $value[$l];
            }
        }

        return '—';
    }
}
