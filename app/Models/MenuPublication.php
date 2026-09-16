<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class MenuPublication extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['files' => 'array'];
    }

    private const CACHE_KEY = 'carte.published_files';

    /**
     * Fichiers de données actuellement en ligne.
     *
     * Tant que rien n'a été publié depuis le site (premier déploiement), on
     * sert la copie de la dernière version GitHub, rangée dans resources/.
     *
     * @return array<string,string>
     */
    public static function currentFiles(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $latest = static::query()->latest('id')->first();
            if ($latest) {
                return $latest->files;
            }

            $files = [];
            foreach (glob(resource_path('carte/initial/*')) as $path) {
                $files[basename($path)] = file_get_contents($path);
            }

            return $files;
        });
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }
}
