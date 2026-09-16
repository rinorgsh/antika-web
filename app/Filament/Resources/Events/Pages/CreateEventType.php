<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEventType extends CreateRecord
{
    protected static string $resource = EventTypeResource::class;

}
