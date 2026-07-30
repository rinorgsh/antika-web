<?php

namespace App\Filament\Resources\MenuChefs\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MenuChefsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Titre (FR)')
                    ->getStateUsing(fn ($record) => $record->title['fr'] ?? '—'),
                TextColumn::make('price')->label('Prix')->suffix(' €'),
                TextColumn::make('image')->label('Photo')->color('gray'),
            ])
            ->recordActions([
                EditAction::make()->label('Modifier'),
            ]);
    }
}
