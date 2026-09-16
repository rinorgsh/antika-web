<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventMenuItem extends Model
{
    use HasTranslations;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
            'supplement_price' => 'decimal:2',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function menuCategory(): BelongsTo
    {
        return $this->belongsTo(EventMenuCategory::class, 'menu_category_id');
    }
}
