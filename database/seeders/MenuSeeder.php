<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use App\Models\MenuChef;
use App\Models\MenuItem;
use App\Models\MenuSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Importe le contenu actuel de la carte (menu-seed.json, généré depuis
 * food-data.js / desserts-data.js / boisson-data.js) dans la base.
 *
 * Idempotent : on repart d'une base propre à chaque exécution.
 */
class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/menu-seed.json');
        $seed = json_decode(file_get_contents($path), true);

        DB::table('menu_items')->delete();
        DB::table('menu_categories')->delete();

        foreach ($seed['categories'] as $cat) {
            $category = MenuCategory::create([
                'surface' => $cat['surface'],
                'slug' => $cat['slug'],
                'position' => $cat['position'],
                'has_note' => $cat['has_note'],
                'title' => $cat['title'],
                'subtitle' => $cat['subtitle'],
                'note' => $cat['note'],
            ]);

            foreach ($cat['items'] as $it) {
                MenuItem::create([
                    'category_id' => $category->id,
                    'slug' => $it['slug'],
                    'position' => $it['position'],
                    'is_subheader' => $it['is_subheader'],
                    'price' => $it['price'],
                    'per_person' => $it['per_person'],
                    'is_zero' => $it['is_zero'],
                    'has_description' => $it['has_description'],
                    'tags' => $it['tags'],
                    'variants' => $it['variants'],
                    'photo' => $it['photo'],
                    'logo' => $it['logo'],
                    'link' => $it['link'],
                    'default_name' => $it['default_name'],
                    'name' => $it['name'],
                    'description' => $it['description'],
                ]);
            }
        }

        foreach ($seed['settings'] as $key => $value) {
            MenuSetting::put($key, $value);
        }

        MenuChef::query()->delete();
        MenuChef::create($seed['chef']);

        $this->command->info(sprintf(
            'Carte importée : %d catégories, %d items.',
            MenuCategory::count(),
            MenuItem::count(),
        ));
    }
}
