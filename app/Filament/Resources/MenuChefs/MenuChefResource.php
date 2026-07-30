<?php

namespace App\Filament\Resources\MenuChefs;

use App\Filament\Resources\MenuChefs\Pages\CreateMenuChef;
use App\Filament\Resources\MenuChefs\Pages\EditMenuChef;
use App\Filament\Resources\MenuChefs\Pages\ListMenuChefs;
use App\Filament\Resources\MenuChefs\Schemas\MenuChefForm;
use App\Filament\Resources\MenuChefs\Tables\MenuChefsTable;
use App\Models\MenuChef;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MenuChefResource extends Resource
{
    protected static ?string $model = MenuChef::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static string|\UnitEnum|null $navigationGroup = 'Carte';

    protected static ?string $navigationLabel = 'Suggestion du chef';

    protected static ?string $modelLabel = 'suggestion du chef';

    protected static ?string $pluralModelLabel = 'suggestion du chef';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return MenuChefForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MenuChefsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenuChefs::route('/'),
            'create' => CreateMenuChef::route('/create'),
            'edit' => EditMenuChef::route('/{record}/edit'),
        ];
    }
}
