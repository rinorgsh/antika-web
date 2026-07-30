<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuCategory extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'subtitle' => 'array',
            'note' => 'array',
            'has_note' => 'boolean',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'category_id')->orderBy('position');
    }
}
