<?php

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Database\Migrations\Migration;

/*
 * Soirée « Red Flag », derniers retours du client (29/10) : photos propres à
 * la soirée pour le Mezze, le Steak et le Suprême de Poulet. Le poulet est
 * servi sans salade : « & salade fraîche » sort de son descriptif.
 */
return new class extends Migration
{
    public function up(): void
    {
        $food = MenuCategory::query()->where('surface', 'event')->where('slug', 'bbq')->first();
        if (! $food) {
            return;   // base sans carte événement (tests, installation neuve)
        }

        $food->items()->where('slug', 'steak_filet_mignon')->update(['photo' => 'rf_steak']);

        $food->items()->where('slug', 'supreme_poulet')->update([
            'photo' => 'rf_poulet',
            'description' => ['en' => 'Pommes de terre dorées au four & sauce douce et sucrée du Chef.'],
        ]);

        $food->items()
            ->where(fn ($q) => $q->where('slug', 'mezze_baba_grill')->orWhere('photo', 'like', 'mezze_baba_grill%'))
            ->get()
            ->each(fn (MenuItem $it) => $it->update(['photo' => 'rf_mezze']));
    }

    public function down(): void
    {
        // Données de carte : on revient en arrière depuis l'admin.
    }
};
