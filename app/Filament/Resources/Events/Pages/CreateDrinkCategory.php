<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\DrinkCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDrinkCategory extends CreateRecord
{
    protected static string $resource = DrinkCategoryResource::class;

}
