<?php

namespace App\Filament\Resources\Festival\Pages;

use App\Filament\Resources\Festival\SponsorApplicationResource;
use Filament\Resources\Pages\EditRecord;

class EditSponsorApplication extends EditRecord
{
    protected static string $resource = SponsorApplicationResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('view', ['record' => $this->record]);
    }
}
