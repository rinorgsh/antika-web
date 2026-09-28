<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Events\CallbackRequestResource;
use App\Filament\Resources\Events\QuoteResource;
use App\Filament\Resources\Events\ReservationResource;
use App\Models\CallbackRequest;
use App\Models\Quote;
use App\Models\Reservation;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/** Chiffres clés des événements, en tête du tableau de bord. */
class EventsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -2;

    protected function getStats(): array
    {
        $new = Quote::where('status', 'new')->count();
        $month = Quote::where('created_at', '>=', now()->startOfMonth())->count();
        $ads = Quote::where('created_at', '>=', now()->subDays(30))->where('tracking', 'like', '%clid%')->count()
            + CallbackRequest::where('created_at', '>=', now()->subDays(30))->where('tracking', 'like', '%clid%')->count();
        $callbacks = CallbackRequest::where('status', 'new')->count();
        $upcoming = Reservation::whereDate('event_date', '>=', today())->where('status', '!=', 'cancelled')->count();

        return [
            Stat::make('Demandes à traiter', $new)
                ->description($new ? 'Nouvelles demandes de devis' : 'Tout est traité')
                ->color($new ? 'warning' : 'success')
                ->url(QuoteResource::getUrl('index', ['filters' => ['status' => ['values' => ['new']]]])),
            Stat::make('Clients à rappeler', $callbacks)
                ->description($callbacks ? 'Demandes de rappel' : 'Aucun rappel en attente')
                ->color($callbacks ? 'warning' : 'success')
                ->url(CallbackRequestResource::getUrl('index')),
            Stat::make('Demandes ce mois-ci', $month)
                ->description('Depuis le '.now()->startOfMonth()->format('d/m'))
                ->url(QuoteResource::getUrl('index')),
            Stat::make('Via Google Ads (30 j)', $ads)
                ->description('Devis et rappels venus d\'une annonce'),
            Stat::make('Événements à venir', $upcoming)
                ->description('Réservations confirmées')
                ->url(ReservationResource::getUrl('index')),
        ];
    }
}
