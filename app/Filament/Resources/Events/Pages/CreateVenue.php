<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\VenueResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVenue extends CreateRecord
{
    protected static string $resource = VenueResource::class;

}
