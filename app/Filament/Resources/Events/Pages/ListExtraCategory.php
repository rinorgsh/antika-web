<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\ExtraCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExtraCategory extends ListRecords
{
    protected static string $resource = ExtraCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
