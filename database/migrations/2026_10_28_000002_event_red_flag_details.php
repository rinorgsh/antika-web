<?php

use App\Models\MenuCategory;
use App\Models\MenuSetting;
use Illuminate\Database\Migrations\Migration;

/*
 * Soirée « Red Flag » : les infos des affiches Instagram (accroche, line-up,
 * portes, réservation) et la mention « signature » sur le cocktail Red Flag.
 */
return new class extends Migration
{
    public function up(): void
    {
        $cocktails = MenuCategory::query()->where('surface', 'event')->where('slug', 'cocktails')->first();
        if (! $cocktails) {
            return;   // base sans carte événement (tests, installation neuve)
        }

        MenuSetting::put('event.tagline', "Don't take it… you know he is a..");
        MenuSetting::put('event.lineup', 'Ank · FemX · Valbon · Gokhan · Faruk');
        MenuSetting::put('event.doors', '19:00');
        MenuSetting::put('event.reservation', '+32 495 52 66 56');

        $cocktails->items()->where('slug', 'red_flag')->update(['hint' => 'signature']);
    }

    public function down(): void
    {
        // Données de carte : on revient en arrière depuis l'admin.
    }
};
