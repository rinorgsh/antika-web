<?php

namespace App\Filament\Resources\MenuChefs\Pages;

use App\Filament\Resources\MenuChefs\MenuChefResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMenuChef extends EditRecord
{
    protected static string $resource = MenuChefResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
