<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\BlockedDateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBlockedDate extends CreateRecord
{
    protected static string $resource = BlockedDateResource::class;

}
