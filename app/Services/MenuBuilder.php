<?php

namespace App\Services;

use App\Models\MenuCategory;
use App\Models\MenuChef;
use App\Models\MenuSetting;

/**
 * Reconstruit les 3 fichiers de données du menu QR depuis la base :
 *   food-data.js  · desserts-data.js · boisson-data.js
 *
 * Sortie = même structure d'objet que les fichiers d'origine, donc le
 * rendu des pages (menu.html / dessert.html / boisson.html) est inchangé.
 */
class MenuBuilder
{
    /** @var string[] */
    public array $langs;

    public function __construct()
    {
        $langsSetting = MenuSetting::get('langs', []);
        $this->langs = array_map(fn ($l) => $l['code'], $langsSetting);
    }

    /** @return array<string,string> filename => contenu JS */
    public function all(): array
    {
        return [
            'food-data.js' => $this->js('FOOD', $this->food()),
            'desserts-data.js' => $this->js('DESSERTS', $this->desserts()),
            'boisson-data.js' => $this->js('BOISSON', $this->drinks()),
        ];
    }

    private function js(string $global, array $data): string
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        return "/* Généré automatiquement par l'admin Antika — ne pas éditer à la main. */\n"
            ."window.{$global} = {$json};\n";
    }

    /** Sous-objet {nl,fr,en,al} pour un champ traduit. */
    private function tr(?array $field): array
    {
        $out = [];
        foreach ($this->langs as $l) {
            $out[$l] = $field[$l] ?? '';
        }

        return $out;
    }

    /* ============================ FOOD ============================ */
    private function food(): array
    {
        $cats = MenuCategory::with('items')->where('surface', 'food')->orderBy('position')->get();

        $menu = [];
        $sections = array_fill_keys($this->langs, []);
        $notes = array_fill_keys($this->langs, []);
        $items = array_fill_keys($this->langs, []);

        foreach ($cats as $cat) {
            $section = ['id' => $cat->slug];
            if ($cat->has_note) {
                $section['note'] = true;
            }
            $section['items'] = [];

            foreach ($cat->items as $it) {
                $row = ['k' => $it->slug, 'ph' => $it->photo, 'p' => $it->price];
                if ($it->per_person) {
                    $row['pp'] = true;
                }
                if (! empty($it->tags)) {
                    $row['tags'] = $it->tags;
                }
                $section['items'][] = $row;

                foreach ($this->langs as $l) {
                    $items[$l][$it->slug] = ['n' => $it->name[$l] ?? '', 'd' => $it->description[$l] ?? ''];
                }
            }
            $menu[] = $section;

            foreach ($this->langs as $l) {
                $sections[$l][$cat->slug] = ['s' => $cat->subtitle[$l] ?? '', 't' => $cat->title[$l] ?? ''];
                if ($cat->has_note && $cat->note) {
                    $notes[$l][$cat->slug] = $cat->note[$l] ?? '';
                }
            }
        }

        $ui = MenuSetting::get('food.ui', []);
        $hero = MenuSetting::get('food.hero', []);

        $i18n = [];
        foreach ($this->langs as $l) {
            $i18n[$l] = [
                'ui' => $ui[$l] ?? [],
                'hero' => $hero[$l] ?? [],
                'sections' => $sections[$l],
                'notes' => $notes[$l],
                'items' => $items[$l],
            ];
        }

        return [
            'langs' => MenuSetting::get('langs', []),
            'menu' => $menu,
            'i18n' => $i18n,
            'chef' => $this->chef(),
        ];
    }

    private function chef(): array
    {
        $c = MenuChef::query()->first();
        if (! $c) {
            return [];
        }

        $out = ['img' => $c->image, 'price' => $c->price];
        foreach ($this->langs as $l) {
            $out[$l] = [
                'eyebrow' => $c->eyebrow[$l] ?? '',
                'title' => $c->title[$l] ?? '',
                'desc' => $c->description[$l] ?? '',
                'plabel' => $c->plabel[$l] ?? '',
                'preps' => $c->preps[$l] ?? [],
            ];
        }

        return $out;
    }

    /* ========================== DESSERTS ========================== */
    private function desserts(): array
    {
        $cats = MenuCategory::with('items')->where('surface', 'desserts')->orderBy('position')->get();

        $sections = [];
        $i18nSections = array_fill_keys($this->langs, []);
        $names = array_fill_keys($this->langs, []);

        foreach ($cats as $cat) {
            $section = ['id' => $cat->slug, 'items' => []];
            foreach ($cat->items as $it) {
                $row = ['k' => $it->slug, 'n' => $it->default_name, 'p' => $it->price];
                if ($it->photo) {
                    $row['img'] = $it->photo;
                }
                if ($it->logo) {
                    $row['logo'] = $it->logo;
                }
                $section['items'][] = $row;

                foreach ($this->langs as $l) {
                    if (! empty($it->name[$l])) {
                        $names[$l][$it->slug] = $it->name[$l];
                    }
                }
            }
            $sections[] = $section;

            foreach ($this->langs as $l) {
                $i18nSections[$l][$cat->slug] = ['s' => $cat->subtitle[$l] ?? '', 't' => $cat->title[$l] ?? ''];
            }
        }

        $head = MenuSetting::get('desserts.head', []);
        $i18n = [];
        foreach ($this->langs as $l) {
            $i18n[$l] = [
                'head' => $head[$l] ?? [],
                'sections' => $i18nSections[$l],
                'names' => (object) $names[$l],
            ];
        }

        return ['sections' => $sections, 'i18n' => $i18n];
    }

    /* =========================== DRINKS =========================== */
    private function drinks(): array
    {
        $cats = MenuCategory::with('items')->where('surface', 'drinks')->orderBy('position')->get();

        $sections = [];
        $i18nSections = array_fill_keys($this->langs, []);
        $names = array_fill_keys($this->langs, []);
        $desc = array_fill_keys($this->langs, []);

        foreach ($cats as $cat) {
            $section = ['id' => $cat->slug, 'items' => []];
            foreach ($cat->items as $it) {
                if ($it->is_subheader) {
                    $section['items'][] = ['sub' => $it->slug];

                    continue;
                }
                $row = ['k' => $it->slug];
                if ($it->default_name !== null) {
                    $row['n'] = $it->default_name;
                }
                if (! empty($it->variants)) {
                    $row['variants'] = $it->variants;
                } elseif ($it->price !== null) {
                    $row['p'] = $it->price;
                }
                if ($it->has_description) {
                    $row['d'] = 1;
                }
                if ($it->is_zero) {
                    $row['z'] = 1;
                }
                if ($it->photo) {
                    $row['img'] = $it->photo;
                }
                if ($it->logo) {
                    $row['logo'] = $it->logo;
                }
                if ($it->link) {
                    $row['link'] = $it->link;
                }
                $section['items'][] = $row;

                foreach ($this->langs as $l) {
                    if (! empty($it->name[$l])) {
                        $names[$l][$it->slug] = $it->name[$l];
                    }
                    if (! empty($it->description[$l])) {
                        $desc[$l][$it->slug] = $it->description[$l];
                    }
                }
            }
            $sections[] = $section;

            foreach ($this->langs as $l) {
                $i18nSections[$l][$cat->slug] = ['s' => $cat->subtitle[$l] ?? '', 't' => $cat->title[$l] ?? ''];
            }
        }

        $hero = MenuSetting::get('drinks.hero', []);
        $web = MenuSetting::get('drinks.web', []);
        $subs = MenuSetting::get('drinks.subs', []);
        $labels = MenuSetting::get('drinks.labels', []);

        $i18n = [];
        foreach ($this->langs as $l) {
            $i18n[$l] = [
                'hero' => $hero[$l] ?? [],
                'web' => $web[$l] ?? '',
                'sections' => $i18nSections[$l],
                'subs' => $subs[$l] ?? [],
                'labels' => $labels[$l] ?? [],
                'names' => (object) $names[$l],
                'desc' => (object) $desc[$l],
            ];
        }

        return [
            'sections' => $sections,
            'sharedTail' => MenuSetting::get('drinks.sharedTail', []),
            'i18n' => $i18n,
        ];
    }
}
