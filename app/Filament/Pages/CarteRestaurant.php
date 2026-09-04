<?php

namespace App\Filament\Pages;

use App\Filament\MenuBoard\MenuBoardPage;
use BackedEnum;
use UnitEnum;

class CarteRestaurant extends MenuBoardPage
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Carte restaurant';

    protected static string|UnitEnum|null $navigationGroup = 'Carte';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Carte du restaurant';

    public function surfaces(): array
    {
        return ['food' => 'Nourriture', 'desserts' => 'Desserts', 'drinks' => 'Boissons'];
    }

    public function previewUrl(): ?string
    {
        return rtrim(config('antika.menu_url'), '/').'/';
    }
}
