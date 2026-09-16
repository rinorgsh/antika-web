<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    use HasTranslations;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
            'short_description' => 'array',
            'price' => 'decimal:2',
            'capacity_seated' => 'integer',
            'capacity_standing' => 'integer',
            'has_parking' => 'boolean',
            'has_vestiaire' => 'boolean',
            'has_private_toilets' => 'boolean',
            'has_kitchen' => 'boolean',
            'surface_area' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function venueImages(): HasMany
    {
        return $this->hasMany(VenueImage::class)->orderBy('sort_order');
    }

    public function blockedDates(): HasMany
    {
        return $this->hasMany(BlockedDate::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /** Image principale (ou la première), en URL publique. */
    public function coverUrl(): ?string
    {
        $image = $this->venueImages->firstWhere('is_primary', true) ?? $this->venueImages->first();

        return static::imageUrl($image?->image_path);
    }

    /**
     * La salle est-elle libre ce jour-là ? Bloquée si une date est fermée
     * pour cette salle (ou pour tout le domaine : venue_id vide), ou si une
     * réservation non annulée existe déjà.
     */
    public function isAvailableOn(string $date): bool
    {
        $blocked = BlockedDate::query()
            ->whereDate('date', $date)
            ->where(fn ($q) => $q->whereNull('venue_id')->orWhere('venue_id', $this->id))
            ->exists();

        $booked = Reservation::query()
            ->where('venue_id', $this->id)
            ->whereDate('event_date', $date)
            ->where('status', '!=', 'cancelled')
            ->exists();

        return ! $blocked && ! $booked;
    }
}
