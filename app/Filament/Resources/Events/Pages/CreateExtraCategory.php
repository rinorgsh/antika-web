<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\ExtraCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExtraCategory extends CreateRecord
{
    protected static string $resource = ExtraCategoryResource::class;

}
