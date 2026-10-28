<?php

use App\Models\MenuCategory;
use Illuminate\Database\Migrations\Migration;

/*
 * Soirée « Red Flag » : photo de la Trileqe, reprise du catalogue du
 * simulateur (evenements/dishes/trilece.jpg) et copiée dans carte/photos.
 */
return new class extends Migration
{
    public function up(): void
    {
        $food = MenuCategory::query()->where('surface', 'event')->where('slug', 'bbq')->first();

        $food?->items()->where('slug', 'trileqe')->whereNull('photo')->update(['photo' => 'trileqe']);
    }

    public function down(): void
    {
        // Données de carte : on revient en arrière depuis l'admin.
    }
};
