<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuChef extends Model
{
    protected $table = 'menu_chef';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'eyebrow' => 'array',
            'title' => 'array',
            'description' => 'array',
            'plabel' => 'array',
            'preps' => 'array',
        ];
    }
}
