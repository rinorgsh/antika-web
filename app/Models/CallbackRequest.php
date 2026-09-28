<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Demande de rappel (formulaire court des pages événements). */
class CallbackRequest extends Model
{
    public const STATUSES = [
        'new' => 'À rappeler',
        'called' => 'Rappelé',
        'quoted' => 'Devis envoyé',
        'won' => 'Réservé',
        'lost' => 'Sans suite',
    ];

    public const STATUS_COLORS = [
        'new' => 'warning',
        'called' => 'info',
        'quoted' => 'info',
        'won' => 'success',
        'lost' => 'gray',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'guest_count' => 'integer',
            'tracking' => 'array',
        ];
    }

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class);
    }
}
