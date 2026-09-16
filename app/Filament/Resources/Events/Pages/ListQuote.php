<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\QuoteResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListQuote extends ListRecords
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('simulator')->label('Voir le simulateur')->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')->url(route('simulator.index'), shouldOpenInNewTab: true),
        ];
    }
}
