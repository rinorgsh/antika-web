<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\DrinkCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDrinkCategory extends ListRecords
{
    protected static string $resource = DrinkCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
