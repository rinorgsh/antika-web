<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\VenueResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVenue extends ListRecords
{
    protected static string $resource = VenueResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
