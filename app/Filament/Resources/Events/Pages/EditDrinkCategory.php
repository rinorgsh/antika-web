<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\DrinkCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDrinkCategory extends EditRecord
{
    protected static string $resource = DrinkCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
