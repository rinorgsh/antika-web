<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtraItem extends Model
{
    use HasTranslations;

    public const PRICE_TYPES = [
        'fixed' => 'Forfait',
        'per_person' => 'Par personne',
        'per_unit' => 'Par unité',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
            'price' => 'decimal:2',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function extraCategory(): BelongsTo
    {
        return $this->belongsTo(ExtraCategory::class);
    }
}
