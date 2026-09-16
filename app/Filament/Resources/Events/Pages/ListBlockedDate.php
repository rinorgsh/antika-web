<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\BlockedDateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBlockedDate extends ListRecords
{
    protected static string $resource = BlockedDateResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
