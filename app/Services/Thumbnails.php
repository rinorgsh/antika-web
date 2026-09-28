<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/**
 * Miniatures WebP des images téléversées (disque « public »).
 *
 * /thumbs/400/evenements/dishes/soep.jpg.webp est créé à la première demande
 * dans public/thumbs/…, au même chemin que l'adresse : ensuite nginx le sert
 * directement, sans passer par PHP. Une photo de 300 Ko affichée en vignette
 * devient un fichier d'environ 20 Ko.
 */
class Thumbnails
{
    /** Largeurs autorisées (évite qu'on génère n'importe quelle taille via l'URL). */
    public const WIDTHS = [160, 400, 800, 1200];

    /** Adresse de la miniature, ou l'image d'origine si ce n'est pas une image locale (null si absente). */
    public static function url(?string $path, int $width): ?string
    {
        // Fichier absent (supprimé, import incomplet) : pas d'image plutôt qu'une image cassée.
        if (! $path || (! preg_match('#^https?://#', $path) && ! Storage::disk('public')->exists($path))) {
            return null;
        }
        if (preg_match('#^https?://#', $path)) {
            return $path;
        }
        if (! preg_match('/\.(jpe?g|png|webp)$/i', $path)) {
            return Storage::disk('public')->url($path);
        }

        return '/thumbs/'.$width.'/'.ltrim($path, '/').'.webp';
    }

    /** Crée la miniature si besoin et renvoie son chemin sur le disque (null si la source manque). */
    public static function make(string $path, int $width): ?string
    {
        $disk = Storage::disk('public');
        if (! in_array($width, self::WIDTHS, true) || str_contains($path, '..') || ! $disk->exists($path)) {
            return null;
        }

        $target = public_path("thumbs/{$width}/{$path}.webp");
        if (is_file($target) && filemtime($target) >= $disk->lastModified($path)) {
            return $target;
        }

        $source = @imagecreatefromstring($disk->get($path));
        if (! $source) {
            return null;
        }

        // Jamais agrandie : une petite image garde sa taille.
        $w = imagesx($source);
        $h = imagesy($source);
        $newW = min($width, $w);
        $newH = (int) round($h * $newW / $w);

        $thumb = imagecreatetruecolor($newW, $newH);
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newW, $newH, $w, $h);

        if (! is_dir(dirname($target))) {
            mkdir(dirname($target), 0755, true);
        }
        // Écriture atomique : un visiteur ne reçoit jamais un fichier à moitié écrit.
        $tmp = $target.'.'.getmypid().'.tmp';
        imagewebp($thumb, $tmp, 72);
        rename($tmp, $target);

        imagedestroy($source);
        imagedestroy($thumb);

        return $target;
    }
}
