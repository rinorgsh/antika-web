<?php

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Database\Migrations\Migration;

/*
 * Soirée « Red Flag », message du client du 29/10 : plus de Moët Ice en stock,
 * remplacé par le Moët & Chandon Brut Impérial (même prix), et le
 * Laurent-Perrier Brut à 200 € rejoint les champagnes, entre Veuve et Moët.
 */
return new class extends Migration
{
    public function up(): void
    {
        $alc = MenuCategory::query()->where('surface', 'event')->where('slug', 'alc')->first();
        if (! $alc) {
            return;   // base sans carte événement (tests, installation neuve)
        }

        $moet = $alc->items()->where('slug', 'c_moet')->first();
        if (! $moet) {
            return;
        }

        $moet->update([
            'name' => ['en' => 'Moët & Chandon Brut Impérial'],
            'price' => '220',
            'photo' => 'moet_chandon',
        ]);

        if ($alc->items()->where('slug', 'c_laurent_perrier')->exists()) {
            return;
        }

        // Le Laurent-Perrier prend la place du Moët, qui recule d'un cran avec la suite.
        $alc->items()->where('position', '>=', $moet->position)->increment('position');

        MenuItem::query()->create([
            'category_id' => $alc->id,
            'slug' => 'c_laurent_perrier',
            'position' => $moet->position,
            'name' => ['en' => 'Laurent-Perrier Brut'],
            'price' => '200',
            'photo' => 'laurent_perrier',
            'is_active' => true,
        ]);
    }

    public function down(): void
    {
        // Données de carte : on revient en arrière depuis l'admin.
    }
};
