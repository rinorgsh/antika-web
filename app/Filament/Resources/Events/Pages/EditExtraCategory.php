<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\ExtraCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExtraCategory extends EditRecord
{
    protected static string $resource = ExtraCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
