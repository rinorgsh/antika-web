<?php

use App\Models\MenuCategory;
use Illuminate\Database\Migrations\Migration;

/*
 * Soirée « Red Flag » : photo du cocktail Red Flag (fournie le 28/10).
 */
return new class extends Migration
{
    public function up(): void
    {
        $cocktails = MenuCategory::query()->where('surface', 'event')->where('slug', 'cocktails')->first();

        $cocktails?->items()->where('slug', 'red_flag')->update(['photo' => 'red_flag']);
    }

    public function down(): void
    {
        // Données de carte : on revient en arrière depuis l'admin.
    }
};
