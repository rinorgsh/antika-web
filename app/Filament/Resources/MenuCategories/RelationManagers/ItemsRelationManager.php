<?php

namespace App\Filament\Resources\MenuCategories\RelationManagers;

use App\Filament\Resources\MenuItems\Schemas\MenuItemForm;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Plats de cette catégorie';

    public function form(Schema $schema): Schema
    {
        return MenuItemForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('position')
            ->defaultSort('position')
            ->columns([
                ToggleColumn::make('is_active')->label('Actif'),
                TextColumn::make('name')->label('Nom')
                    ->getStateUsing(fn ($record) => $record->name['fr'] ?? $record->default_name ?? $record->slug),
                TextInputColumn::make('price')->label('Prix (€)'),
                TextColumn::make('badges')->label('Options')->badge()
                    ->getStateUsing(function ($record) {
                        $b = [];
                        if ($record->per_person) $b[] = '/pers.';
                        if ($record->is_zero) $b[] = '0%';
                        if ($record->is_subheader) $b[] = 'sous-titre';
                        if (! empty($record->variants)) $b[] = 'variantes';

                        return $b;
                    }),
            ])
            ->headerActions([
                CreateAction::make()->label('Ajouter un plat'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
