<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventMenuFormulaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEventMenuFormula extends ListRecords
{
    protected static string $resource = EventMenuFormulaResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
