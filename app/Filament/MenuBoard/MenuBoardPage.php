<?php

namespace App\Filament\MenuBoard;

use App\Filament\Resources\MenuCategories\MenuCategoryResource;
use App\Filament\Resources\MenuItems\MenuItemResource;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Services\MenuPublisher;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/**
 * Écran « carte » : on voit la carte comme le client la verra — catégorie par
 * catégorie, avec les plats dedans. L'ordre, le prix et l'affichage se règlent
 * sur place ; le formulaire complet s'ouvre au clic sur un nom.
 *
 * Remplace la navigation en deux tableaux séparés (Catégories d'un côté, Plats
 * de l'autre), qui obligeait à changer d'écran pour déplacer un plat.
 *
 * Classe de base volontairement hors de app/Filament/Pages : Filament y
 * découvre et enregistre automatiquement les pages, une classe abstraite y
 * ferait échouer l'enregistrement.
 */
abstract class MenuBoardPage extends Page
{
    protected string $view = 'filament.pages.menu-board';

    /** Surfaces gérées par la page : ['food' => 'Nourriture', …]. */
    abstract public function surfaces(): array;

    /** Langue des libellés affichés (la soirée est en anglais). */
    public function lang(): string
    {
        return 'fr';
    }

    /** Lien « Aperçu » propre à la page, ou null. */
    public function previewUrl(): ?string
    {
        return null;
    }

    public string $surface = '';

    public function mount(): void
    {
        $this->surface = (string) array_key_first($this->surfaces());
    }

    public function setSurface(string $surface): void
    {
        if (array_key_exists($surface, $this->surfaces())) {
            $this->surface = $surface;
        }
    }

    /** Catégories de la surface courante, avec leurs plats, dans l'ordre. */
    public function getCategoriesProperty()
    {
        return MenuCategory::with(['items' => fn ($q) => $q->orderBy('position')])
            ->where('surface', $this->surface)
            ->orderBy('position')
            ->get();
    }

    /** Premier libellé non vide, dans la langue de la page puis en repli. */
    public function label(?array $field, string $defaut = '—'): string
    {
        foreach ([$this->lang(), 'fr', 'en', 'nl', 'al'] as $l) {
            if (! empty($field[$l])) {
                return $field[$l];
            }
        }

        return $defaut;
    }

    public function itemUrl(int $id): string
    {
        return MenuItemResource::getUrl('edit', ['record' => $id]);
    }

    public function categoryUrl(int $id): string
    {
        return MenuCategoryResource::getUrl('edit', ['record' => $id]);
    }

    /**
     * Vignette d'un plat. Deux écritures possibles en base :
     *   "calamars"          -> photos/calamars.jpg (téléversement admin)
     *   "logos/coca.png"    -> chemin complet, image déjà sur la carte
     */
    public function thumbUrl(?string $photo): ?string
    {
        if (! $photo) {
            return null;
        }
        $base = rtrim(config('antika.menu_url', 'https://menu.antika-resto.ovh/menu.pdf'), '/');

        return str_contains($photo, '/')
            ? "{$base}/{$photo}"
            : "{$base}/photos/{$photo}.jpg";
    }

    /* ---------------------------------------------------------------- */
    /*  Réglages au fil de l'eau                                         */
    /* ---------------------------------------------------------------- */

    public function reorderCategories(array $ids): void
    {
        foreach (array_values($ids) as $i => $id) {
            MenuCategory::whereKey($id)->update(['position' => $i]);
        }
    }

    public function reorderItems(array $ids): void
    {
        foreach (array_values($ids) as $i => $id) {
            MenuItem::whereKey($id)->update(['position' => $i]);
        }
    }

    public function toggleItem(int $id): void
    {
        $item = MenuItem::find($id);
        if ($item) {
            $item->update(['is_active' => ! $item->is_active]);
        }
    }

    public function savePrice(int $id, ?string $price): void
    {
        $price = trim((string) $price);
        MenuItem::whereKey($id)->update(['price' => $price === '' ? null : $price]);
    }

    public function deleteItem(int $id): void
    {
        MenuItem::whereKey($id)->delete();
        Notification::make()->title('Plat supprimé')->success()->send();
    }

    /**
     * Crée un brouillon inactif puis ouvre sa fiche : un plat incomplet ne
     * peut pas se retrouver sur la carte tant qu'il n'est pas activé.
     */
    public function addItem(int $categoryId)
    {
        $cat = MenuCategory::findOrFail($categoryId);

        $item = MenuItem::create([
            'category_id' => $cat->id,
            'slug' => 'nouveau_'.substr(uniqid(), -6),
            'position' => (int) MenuItem::where('category_id', $cat->id)->max('position') + 1,
            'is_active' => false,
            'name' => [$this->lang() => 'Nouveau plat'],
        ]);

        return redirect($this->itemUrl($item->id));
    }

    public function addCategory()
    {
        $cat = MenuCategory::create([
            'surface' => $this->surface,
            'slug' => 'nouvelle_'.substr(uniqid(), -6),
            'position' => (int) MenuCategory::where('surface', $this->surface)->max('position') + 1,
            'title' => [$this->lang() => 'Nouvelle catégorie'],
        ]);

        return redirect($this->categoryUrl($cat->id));
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        if ($url = $this->previewUrl()) {
            $actions[] = Action::make('preview')
                ->label('Aperçu')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url($url)
                ->openUrlInNewTab();
        }

        $actions[] = Action::make('publish')
            ->label('Publier la carte')
            ->icon('heroicon-o-rocket-launch')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Publier la carte en ligne ?')
            ->modalDescription('Les changements seront visibles par les clients dans une minute environ.')
            ->modalSubmitActionLabel('Oui, publier')
            ->action(function (MenuPublisher $publisher) {
                if (! $publisher->isConfigured()) {
                    Notification::make()->title('Publication non configurée')
                        ->body("Le jeton GitHub n'est pas renseigné.")->warning()->send();

                    return;
                }
                try {
                    $publisher->publish("Mise à jour de la carte depuis l'admin Antika");
                    Notification::make()->title('Carte publiée ✓')
                        ->body('Visible sur le menu QR dans ~1 min.')->success()->send();
                } catch (\Throwable $e) {
                    Notification::make()->title('Échec de la publication')
                        ->body($e->getMessage())->danger()->persistent()->send();
                }
            });

        return $actions;
    }
}
