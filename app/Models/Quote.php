<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use SoftDeletes;

    public const STATUSES = [
        'new' => 'Nouvelle demande',
        'contacted' => 'Client contacté',
        'sent' => 'Devis envoyé',
        'accepted' => 'Accepté',
        'declined' => 'Refusé',
        'expired' => 'Expiré',
        'cancelled' => 'Annulé',
    ];

    public const STATUS_COLORS = [
        'new' => 'warning',
        'contacted' => 'info',
        'sent' => 'info',
        'accepted' => 'success',
        'declined' => 'danger',
        'expired' => 'gray',
        'cancelled' => 'gray',
    ];

    public const TIME_SLOTS = ['day' => 'Journée', 'evening' => 'Soirée', 'full_day' => 'Journée entière'];

    /** Relations à charger pour afficher un devis complet. */
    public const FULL = [
        'customer',
        'eventType',
        'quoteVenues.venue',
        'quoteMenus.menuFormula',
        'quoteMenus.quoteMenuChoices.menuItem',
        'quoteDrinks.drinkOption.drinkCategory',
        'quoteExtras.extraItem.extraCategory',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'valid_until' => 'date',
            'guest_count_adults' => 'integer',
            'guest_count_children' => 'integer',
            'child_menu' => 'boolean',
            'subtotal' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'deposit_percentage' => 'decimal:2',
            'tracking' => 'array',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class);
    }

    public function quoteVenues(): HasMany
    {
        return $this->hasMany(QuoteVenue::class);
    }

    public function quoteMenus(): HasMany
    {
        return $this->hasMany(QuoteMenu::class);
    }

    public function quoteDrinks(): HasMany
    {
        return $this->hasMany(QuoteDrink::class);
    }

    public function quoteExtras(): HasMany
    {
        return $this->hasMany(QuoteExtra::class);
    }

    public function reservation(): HasOne
    {
        return $this->hasOne(Reservation::class);
    }

    public function guestCount(): int
    {
        return (int) $this->guest_count_adults + (int) $this->guest_count_children;
    }

    /** Recalcule les totaux à partir des lignes. */
    public function calculateTotals(): self
    {
        $this->subtotal = $this->quoteVenues()->sum('price')
            + $this->quoteMenus()->sum('total')
            + $this->quoteDrinks()->sum('total')
            + $this->quoteExtras()->sum('total');
        $this->tax_amount = round($this->subtotal * ($this->tax_rate / 100), 2);
        $this->total = $this->subtotal + $this->tax_amount;
        $this->deposit_amount = round($this->total * ($this->deposit_percentage / 100), 2);
        $this->save();

        return $this;
    }

    /** Numéro DEV-AAAA-NNNN, séquence par année. */
    public static function generateQuoteNumber(): string
    {
        $prefix = 'DEV-'.now()->format('Y');
        $latest = static::withTrashed()
            ->where('quote_number', 'like', "{$prefix}-%")
            ->orderByDesc('quote_number')
            ->value('quote_number');

        $next = $latest ? (int) substr($latest, strrpos($latest, '-') + 1) + 1 : 1;

        return sprintf('%s-%04d', $prefix, $next);
    }
}
