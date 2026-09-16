<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $guarded = [];

    protected $appends = ['full_name'];

    protected function fullName(): Attribute
    {
        return Attribute::make(get: fn () => trim("{$this->first_name} {$this->last_name}"));
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
