<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\CallbackRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListCallbackRequests extends ListRecords
{
    protected static string $resource = CallbackRequestResource::class;
}
