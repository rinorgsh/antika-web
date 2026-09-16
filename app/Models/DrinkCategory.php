<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Catégorie de boissons du simulateur.
 * role : null = boissons à volonté / à la bouteille, « aperitif » = choix
 * d'apéritif (un seul), « bubbles » = bouteilles de bulles.
 */
class DrinkCategory extends Model
{
    use HasTranslations;

    public const ROLES = [
        'aperitif' => 'Apéritif (un choix parmi les options)',
        'bubbles' => 'Bulles (bouteilles)',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return ['name' => 'array', 'sort_order' => 'integer'];
    }

    public function drinkOptions(): HasMany
    {
        return $this->hasMany(DrinkOption::class)->orderBy('sort_order');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
