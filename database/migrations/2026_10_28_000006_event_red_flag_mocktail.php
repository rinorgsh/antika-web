<?php

use App\Models\MenuCategory;
use Illuminate\Database\Migrations\Migration;

/*
 * Soirée « Red Flag » : le cocktail Red Flag existe aussi en mocktail à 10 €
 * (« Red Flag Mocktail » en caisse), comme les deux Mojito.
 */
return new class extends Migration
{
    public function up(): void
    {
        $cocktails = MenuCategory::query()->where('surface', 'event')->where('slug', 'cocktails')->first();

        $cocktails?->items()->where('slug', 'red_flag')->update(['description' => ['en' => 'Virgin 0.0 % — 10 €']]);
    }

    public function down(): void
    {
        // Données de carte : on revient en arrière depuis l'admin.
    }
};
