<?php

namespace App\Filament\Pages;

use App\Filament\MenuBoard\MenuBoardPage;
use BackedEnum;
use UnitEnum;

class CarteEvenement extends MenuBoardPage
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Carte de la soirée';

    protected static string|UnitEnum|null $navigationGroup = 'Soirée événement';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Carte de la soirée';

    /** La page événement est entièrement en anglais. */
    public function lang(): string
    {
        return 'en';
    }

    public function surfaces(): array
    {
        return ['event' => 'Soirée'];
    }

    public function previewUrl(): ?string
    {
        return route('event.preview');
    }
}
