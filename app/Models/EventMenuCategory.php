<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventMenuCategory extends Model
{
    use HasTranslations;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['name' => 'array', 'sort_order' => 'integer'];
    }

    public function menuFormula(): BelongsTo
    {
        return $this->belongsTo(EventMenuFormula::class, 'menu_formula_id');
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(EventMenuItem::class, 'menu_category_id')->orderBy('sort_order');
    }
}
