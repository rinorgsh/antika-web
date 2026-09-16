<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\ReservationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReservation extends EditRecord
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
