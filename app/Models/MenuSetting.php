<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuSetting extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    public static function get(string $key, $default = null)
    {
        $row = static::query()->where('key', $key)->first();

        return $row ? $row->value : $default;
    }

    public static function put(string $key, $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
