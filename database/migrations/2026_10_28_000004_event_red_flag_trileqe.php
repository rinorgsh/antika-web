<?php

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Database\Migrations\Migration;

/*
 * Soirée « Red Flag », retours du client (28/10) : le Mezze mix est servi
 * avec des patates (plus de salade) et Trileqe à 10 € ferme la carte.
 */
return new class extends Migration
{
    public function up(): void
    {
        $food = MenuCategory::query()->where('surface', 'event')->where('slug', 'bbq')->first();
        if (! $food) {
            return;   // base sans carte événement (tests, installation neuve)
        }

        $food->items()
            ->where(fn ($q) => $q->where('slug', 'mezze_baba_grill')->orWhere('photo', 'like', 'mezze_baba_grill%'))
            ->get()
            ->each(fn (MenuItem $it) => $it->update(['description' => ['en' => 'Pjatë e bollshme për t’u ndarë, me miks mishrash të pjekur në skarë, patate, garnitura dhe salca. Ideale për ta shijuar së bashku me familjen dhe miqtë, nën atmosferën e muzikës live. ( MINIMUM 2 persona )']]));

        MenuItem::query()->updateOrCreate(
            ['category_id' => $food->id, 'slug' => 'trileqe'],
            [
                'name' => ['en' => 'Trileqe'],
                'price' => '10',
                'is_active' => true,
                'position' => (int) $food->items()->where('is_active', true)->where('slug', '!=', 'trileqe')->max('position') + 1,
            ],
        );
    }

    public function down(): void
    {
        // Données de carte : on revient en arrière depuis l'admin.
    }
};
