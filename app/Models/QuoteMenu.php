<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteMenu extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'guest_count' => 'integer',
            'price_per_person' => 'decimal:2',
            'supplements_total' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function menuFormula(): BelongsTo
    {
        return $this->belongsTo(EventMenuFormula::class, 'menu_formula_id');
    }

    public function quoteMenuChoices(): HasMany
    {
        return $this->hasMany(QuoteMenuChoice::class);
    }
}
