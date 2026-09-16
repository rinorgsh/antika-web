<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrinkOption extends Model
{
    use HasTranslations;

    public const UNIT_TYPES = [
        'glass' => 'Au verre (à volonté par personne)',
        'person' => 'Par personne',
        'bottle' => 'À la bouteille',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
            'price_per_unit' => 'decimal:2',
            'price_all_in' => 'decimal:2',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function drinkCategory(): BelongsTo
    {
        return $this->belongsTo(DrinkCategory::class);
    }
}
