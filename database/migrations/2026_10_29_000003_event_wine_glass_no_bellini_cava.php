<?php

use App\Models\MenuCategory;
use Illuminate\Database\Migrations\Migration;

/*
 * Carte événement : Bellini et Cava sont déjà sous « Alc° > In glass ».
 * On les éteint dans « Soft & Wine > Wine by the glass » (message du 29/10).
 *
 * Rien n'est visible des clients avant « Publier la carte ».
 */
return new class extends Migration
{
    public function up(): void
    {
        $soft = MenuCategory::query()->where('surface', 'event')->where('slug', 'soft')->first();

        if (! $soft) {
            return;   // base sans carte événement (tests, installation neuve)
        }

        // Par clé, ou par nom si la ligne a été recréée dans l'admin.
        $soft->items()
            ->where('is_subheader', false)
            ->where(fn ($q) => $q->whereIn('slug', ['wg_bellini', 'wg_cava'])
                ->orWhereIn('name->en', ['Bellini', 'Cava']))
            ->update(['is_active' => false]);
    }

    public function down(): void
    {
        // Données de carte : on revient en arrière depuis l'admin.
    }
};
