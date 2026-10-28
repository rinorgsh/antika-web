<?php

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuSetting;
use Illuminate\Database\Migrations\Migration;

/*
 * Carte de la soirée « Red Flag » (vendredi 30/10/2026, Labi & Ylli),
 * d'après le message du client du 28/10.
 *
 * La carte en prod a été retouchée dans l'admin (soirée « White Sensation ») :
 * on retrouve donc chaque ligne par sa clé, son nom ou sa photo plutôt que
 * par son id. Rien n'est supprimé, on ne fait qu'éteindre ce qui sort.
 *
 * Rien n'est visible des clients avant « Publier la carte » (admin > Événement).
 */
return new class extends Migration
{
    public function up(): void
    {
        $cocktails = $this->category('cocktails');
        $alc = $this->category('alc');
        $food = $this->category('bbq');

        if (! $cocktails || ! $alc || ! $food) {
            return;   // base sans carte événement (tests, installation neuve)
        }

        MenuSetting::put('event.title', 'Red Flag');
        MenuSetting::put('event.subtitle', 'Labi & Ylli · 30 Oct');
        MenuSetting::put('event.theme', 'redflag');

        // --- Cocktails : Red Flag, Mojito, Mojito Strawberry --------------
        $this->keepOnly($cocktails, [
            $this->upsert($cocktails, 'red_flag', ['Red Flag'], [
                'name' => ['en' => 'Red Flag'],
                'price' => '12',
            ]),
            $this->upsert($cocktails, 'mojito', ['Mojito Original', 'Mojito']),
            $this->upsert($cocktails, 'mojito_straw', ['Mojito Strawberry']),
        ]);

        // --- Bouteilles & champagne : nouveaux prix -----------------------
        foreach ([
            'b_eristoff_red' => '120',
            'b_belvedere' => '160',
            'b_bellini' => '70',
            'c_lanson' => '120',
            'c_veuve' => '170',
            'c_moet' => '220',
        ] as $slug => $price) {
            $alc->items()->where('slug', $slug)->update(['price' => $price]);
        }

        // --- 5 shots : plus de Tequila ni Sweet Island --------------------
        // Le sous-titre « 5 shots = 25 € » disparaît seul : MenuBuilder masque
        // un sous-titre sans ligne active.
        $alc->items()->whereIn('slug', ['s_tequila', 's_island'])->update(['is_active' => false]);

        // --- Food ----------------------------------------------------------
        $this->keepOnly($food, [
            // « comme l'autre menu » : on reprend la ligne de White Sensation.
            $this->upsert($food, 'porcion_fli', ['Porcion fli 🇦🇱'], [
                'name' => ['en' => 'Porcion fli 🇦🇱'],
                'description' => ['en' => 'Porcion fli me mjaltë dhe djathë'],
                'photo' => 'porcion_fli',
            ], ['price' => '15', 'per_person' => false]),
            $this->upsert($food, 'salade_cesar_royale', ['Salade César Royale'], [
                'name' => ['en' => 'Salade César Royale'],
                'description' => ['en' => 'Poulet tendre, parmesan, croûtons dorés & sauce César onctueuse.'],
                'photo' => 'salade-caesar',
            ], ['price' => '20', 'per_person' => false]),
            $this->upsert($food, 'supreme_poulet', ['Suprême de Poulet'], [
                'name' => ['en' => 'Suprême de Poulet'],
                'description' => ['en' => 'Pommes de terre dorées au four, sauce douce et sucrée du Chef & salade fraîche.'],
                'photo' => 'poulet-grille',
            ], ['price' => '25', 'per_person' => false]),
            $this->upsert($food, 'steak_filet_mignon', ['Steak Filet Mignon'], [
                'name' => ['en' => 'Steak Filet Mignon'],
                'description' => ['en' => 'Pommes de terre croustillantes au four, sauce signature maison & salade fraîche.'],
                'photo' => 'filet-mignon',
            ], ['price' => '30', 'per_person' => false]),
            $this->upsert($food, 'mezze_baba_grill', ['Mezze mix Baba Grill 🇦🇱'], [
                'name' => ['en' => 'Mezze mix Baba Grill 🇦🇱'],
                'description' => ['en' => 'Pjatë e bollshme për t’u ndarë, me miks mishrash të pjekur në skarë, sallata, garnitura dhe salca. Ideale për ta shijuar së bashku me familjen dhe miqtë, nën atmosferën e muzikës live. ( MINIMUM 2 persona )'],
                'photo' => 'mezze_baba_grill',
            ], ['price' => '25', 'per_person' => true]),
        ]);
    }

    public function down(): void
    {
        // Données de carte : on revient en arrière depuis l'admin.
    }

    private function category(string $slug): ?MenuCategory
    {
        return MenuCategory::query()->where('surface', 'event')->where('slug', $slug)->first();
    }

    /**
     * Retrouve la ligne (clé, puis nom, puis photo — une photo re-téléversée
     * prend un suffixe « -3f9a1c2b ») ou la crée avec $defaults.
     * $force est appliqué dans tous les cas.
     */
    private function upsert(MenuCategory $cat, string $slug, array $names, array $defaults = [], array $force = []): MenuItem
    {
        $items = $cat->items()->where('is_subheader', false)->get();

        $item = $items->firstWhere('slug', $slug)
            ?? $items->first(fn (MenuItem $it) => in_array(trim($it->name['en'] ?? ''), $names, true))
            ?? (isset($defaults['photo'])
                ? $items->first(fn (MenuItem $it) => $it->photo === $defaults['photo'] || str_starts_with((string) $it->photo, $defaults['photo'].'-'))
                : null);

        if (! $item) {
            $item = new MenuItem(['category_id' => $cat->id, 'slug' => $slug] + $defaults);
        }

        $item->fill($force + ['is_active' => true]);
        $item->save();

        return $item;
    }

    /** Éteint les autres lignes de la section et range les gardées dans l'ordre donné. */
    private function keepOnly(MenuCategory $cat, array $keep): void
    {
        $ids = array_map(fn (MenuItem $it) => $it->id, $keep);

        $cat->items()->where('is_subheader', false)->whereNotIn('id', $ids)->update(['is_active' => false]);

        foreach ($keep as $position => $item) {
            $item->update(['position' => $position]);
        }
    }
};
