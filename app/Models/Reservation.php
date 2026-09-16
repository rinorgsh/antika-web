<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    public const STATUSES = [
        'confirmed' => 'Confirmée',
        'deposit_paid' => 'Acompte payé',
        'fully_paid' => 'Payée',
        'completed' => 'Terminée',
        'cancelled' => 'Annulée',
    ];

    public const STATUS_COLORS = [
        'confirmed' => 'info',
        'deposit_paid' => 'warning',
        'fully_paid' => 'success',
        'completed' => 'gray',
        'cancelled' => 'danger',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'deposit_paid_at' => 'datetime',
            'fully_paid_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /** Passe au statut donné en datant les étapes de paiement / annulation. */
    public function moveTo(string $status, ?string $reason = null): void
    {
        $data = ['status' => $status];
        if (in_array($status, ['deposit_paid', 'fully_paid'], true) && ! $this->deposit_paid_at) {
            $data['deposit_paid_at'] = now();
        }
        if ($status === 'fully_paid' && ! $this->fully_paid_at) {
            $data['fully_paid_at'] = now();
        }
        if ($status === 'cancelled') {
            $data['cancelled_at'] = now();
            $data['cancellation_reason'] = $reason;
        }
        $this->update($data);
    }
}
