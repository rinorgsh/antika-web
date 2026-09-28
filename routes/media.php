<?php

use App\Http\Controllers\CarteController;
use App\Services\Thumbnails;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Images
|--------------------------------------------------------------------------
| Chargées sans le groupe « web » : pas de session ni de cookie sur une
| image, sinon le navigateur ne peut pas la garder en cache et chaque photo
| coûte un démarrage complet de Laravel.
*/

// Fichiers du disque « public » (images téléversées) sans lien symbolique
// public/storage à recréer à chaque déploiement.
Route::get('/media/{path}', function (string $path) {
    abort_if(str_contains($path, '..'), 404);
    abort_unless(Storage::disk('public')->exists($path), 404);

    return response()->file(Storage::disk('public')->path($path), [
        'Cache-Control' => 'public, max-age=604800',
    ]);
})->where('path', '.*')->name('media');

// Miniatures WebP (voir App\Services\Thumbnails). Seule la première demande
// arrive ici : le fichier créé est ensuite servi directement par nginx.
Route::get('/thumbs/{width}/{path}', function (int $width, string $path) {
    $file = Thumbnails::make(substr($path, 0, -5), $width);
    abort_unless($file, 404);

    return response()->file($file, [
        'Content-Type' => 'image/webp',
        'Cache-Control' => 'public, max-age=31536000, immutable',
    ]);
})->whereNumber('width')->where('path', '.+\.webp')->name('thumbs');

// Photos et logos de la carte QR téléversés depuis l'admin.
Route::get('/carte/{dir}/{file}', [CarteController::class, 'upload'])
    ->whereIn('dir', ['photos', 'drinks', 'logos'])
    ->where('file', '[A-Za-z0-9._-]+')
    ->name('carte.upload');
