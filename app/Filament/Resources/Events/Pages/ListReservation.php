<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\ReservationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReservation extends ListRecords
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
