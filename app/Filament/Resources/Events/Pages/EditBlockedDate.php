<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\BlockedDateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBlockedDate extends EditRecord
{
    protected static string $resource = BlockedDateResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
