<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventType extends Model
{
    use HasTranslations;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['name' => 'array', 'description' => 'array', 'is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
