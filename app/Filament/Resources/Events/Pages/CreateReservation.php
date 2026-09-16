<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\ReservationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReservation extends CreateRecord
{
    protected static string $resource = ReservationResource::class;

}
