<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Models\MenuPublication;
use Illuminate\Support\Facades\Storage;

/**
 * Publie la carte QR servie par le site lui-même (/carte/).
 *
 * Avant, la carte vivait sur GitHub Pages et « Publier » y poussait un commit.
 * Désormais :
 *   - les fichiers de données (food-data.js…) sont figés dans la table
 *     menu_publications, servis par CarteController ;
 *   - les photos/logos téléversés sont rangés dans storage/app/public/carte,
 *     un dossier conservé d'un déploiement à l'autre.
 *
 * Effet immédiat : plus d'attente de redéploiement GitHub.
 */
class MenuPublisher
{
    /** Dossier des images téléversées, sur le disque « public ». */
    public const UPLOAD_ROOT = 'carte';

    public function __construct(private MenuBuilder $builder)
    {
    }

    /** Conservé pour compatibilité : la publication n'a plus besoin de réglage. */
    public function isConfigured(): bool
    {
        return true;
    }

    /** @return MenuPublication la publication créée */
    public function publish(string $message): MenuPublication
    {
        $this->syncUploads();

        $publication = MenuPublication::create([
            'files' => $this->builder->all(),
            'message' => $message,
            'user_id' => auth()->id(),
        ]);

        // On garde un historique raisonnable (retour arrière possible).
        $keep = MenuPublication::query()->orderByDesc('id')->limit(30)->pluck('id');
        MenuPublication::query()->whereNotIn('id', $keep)->delete();

        return $publication;
    }

    /**
     * Range les photos/logos téléversés dans le dossier de la carte et met à
     * jour les références en base.
     *
     * Le nom reçoit une empreinte du contenu (calamars-3f9a1c2b.jpg) : une
     * nouvelle photo a donc toujours une nouvelle adresse. Sans ça, un
     * téléphone qui a déjà la photo en cache continuerait d'afficher
     * l'ancienne, et une image livrée avec le site masquerait la nouvelle.
     */
    private function syncUploads(): void
    {
        $disk = Storage::disk('public');

        $items = MenuItem::with('category')
            ->where(fn ($q) => $q->whereNotNull('photo_upload')->orWhereNotNull('logo_upload'))
            ->get();

        foreach ($items as $item) {
            $surface = $item->category->surface;

            if ($item->photo_upload && $disk->exists($item->photo_upload)) {
                $bytes = $disk->get($item->photo_upload);
                $hash = substr(md5($bytes), 0, 8);
                $ext = strtolower(pathinfo($item->photo_upload, PATHINFO_EXTENSION)) ?: 'jpg';

                if ($surface === 'food' || $surface === 'event') {
                    // Ces deux rendus ajoutent "photos/" + ".jpg" : la référence
                    // stockée est la clé nue, sans extension.
                    $disk->put(self::UPLOAD_ROOT."/photos/{$item->slug}-{$hash}.jpg", $bytes);
                    $item->photo = "{$item->slug}-{$hash}";
                } else {
                    $disk->put(self::UPLOAD_ROOT."/drinks/{$item->slug}-{$hash}.{$ext}", $bytes);
                    $item->photo = "{$item->slug}-{$hash}.{$ext}";
                }
                $disk->delete($item->photo_upload);
                $item->photo_upload = null;
            }

            if ($item->logo_upload && $disk->exists($item->logo_upload)) {
                $bytes = $disk->get($item->logo_upload);
                $hash = substr(md5($bytes), 0, 8);
                $ext = strtolower(pathinfo($item->logo_upload, PATHINFO_EXTENSION)) ?: 'png';
                $disk->put(self::UPLOAD_ROOT."/logos/{$item->slug}-{$hash}.{$ext}", $bytes);
                $item->logo = "{$item->slug}-{$hash}.{$ext}";
                $disk->delete($item->logo_upload);
                $item->logo_upload = null;
            }

            $item->save();
        }
    }
}
