<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteMenuChoice extends Model
{
    protected $guarded = [];

    public function quoteMenu(): BelongsTo
    {
        return $this->belongsTo(QuoteMenu::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(EventMenuItem::class, 'menu_item_id');
    }
}
