<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/** Réglages du simulateur (TVA, acompte, destinataires des demandes…). */
class EventSetting extends Model
{
    public const DEFAULTS = [
        'company_name' => 'Antika Events',
        'email' => 'info@antikaresto.com',
        'phone' => '+32 495 52 66 56',
        'whatsapp' => '32495526656',
        'address' => 'Pater Penninckxstraat 32, 1982 Zemst',
        'notify_emails' => '',
        'tax_rate' => '21',
        'deposit_percentage' => '30',
        'quote_validity_days' => '30',
        'terms_url' => '',
        'privacy_url' => '',
        // Site public (pages événements et d'annonces).
        'max_capacity' => '350',
        'google_rating' => '',
        'google_reviews_count' => '',
        'google_reviews_url' => '',
        'reviews' => '[]',
    ];

    protected $guarded = [];

    private const CACHE_KEY = 'event_settings';

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = Cache::rememberForever(self::CACHE_KEY, fn () => static::query()->pluck('value', 'key')->all());
        $value = $all[$key] ?? null;

        return filled($value) ? $value : ($default ?? self::DEFAULTS[$key] ?? null);
    }

    public static function put(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget(self::CACHE_KEY);
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /** Avis mis en avant sur le site : [{text, author, occasion}]. */
    public static function reviews(): array
    {
        $reviews = json_decode((string) static::get('reviews'), true);

        return is_array($reviews) ? array_values(array_filter($reviews, fn ($r) => filled($r['text'] ?? null))) : [];
    }

    /** Adresses qui reçoivent les nouvelles demandes (séparées par des virgules). */
    public static function notifyRecipients(): array
    {
        return collect(explode(',', (string) static::get('notify_emails', '')))
            ->merge(explode(',', (string) config('antika.events.notify_emails')))
            ->map(fn ($e) => trim($e))
            ->filter(fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
            ->unique()->values()->all()
            ?: [static::get('email')];
    }
}
