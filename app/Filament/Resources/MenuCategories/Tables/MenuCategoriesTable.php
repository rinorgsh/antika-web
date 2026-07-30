<?php

namespace App\Filament\Resources\MenuCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MenuCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->columns([
                TextColumn::make('surface')->label('Surface')->badge()->sortable(),
                TextColumn::make('title')->label('Titre (FR)')
                    ->getStateUsing(fn ($record) => $record->title['fr'] ?? $record->slug),
                TextColumn::make('slug')->label('Clé')->color('gray'),
                TextColumn::make('items_count')->label('Plats')->counts('items')->badge(),
                TextColumn::make('position')->label('Ordre')->sortable(),
            ])
            ->filters([
                SelectFilter::make('surface')->label('Surface')
                    ->options(['food' => 'Nourriture', 'desserts' => 'Desserts', 'drinks' => 'Boissons']),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
