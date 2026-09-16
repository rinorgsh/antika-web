<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\CustomerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
