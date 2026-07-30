<?php

namespace App\Filament\Resources\MenuChefs\Pages;

use App\Filament\Resources\MenuChefs\MenuChefResource;
use Filament\Resources\Pages\ListRecords;

class ListMenuChefs extends ListRecords
{
    protected static string $resource = MenuChefResource::class;

    // Singleton : pas de création (une seule suggestion du chef).
    protected function getHeaderActions(): array
    {
        return [];
    }
}
