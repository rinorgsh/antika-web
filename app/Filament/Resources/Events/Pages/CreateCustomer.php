<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\CustomerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

}
