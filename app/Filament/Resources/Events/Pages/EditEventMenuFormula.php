<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventMenuFormulaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEventMenuFormula extends EditRecord
{
    protected static string $resource = EventMenuFormulaResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
