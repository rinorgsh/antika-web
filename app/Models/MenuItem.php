<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_subheader' => 'boolean',
            'per_person' => 'boolean',
            'is_zero' => 'boolean',
            'has_description' => 'boolean',
            'tags' => 'array',
            'variants' => 'array',
            'name' => 'array',
            'description' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }
}
