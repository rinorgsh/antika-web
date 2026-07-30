<?php

namespace App\Filament\Resources\MenuItems\Tables;

use App\Models\MenuCategory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MenuItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->columns([
                TextColumn::make('category.surface')->label('Surface')->badge()->sortable(),
                TextColumn::make('category_label')->label('Catégorie')
                    ->getStateUsing(fn ($record) => $record->category->title['fr'] ?? $record->category->slug),
                TextColumn::make('name')->label('Nom')->searchable()
                    ->getStateUsing(fn ($record) => $record->name['fr'] ?? $record->default_name ?? $record->slug),
                TextInputColumn::make('price')->label('Prix (€)')->rules(['nullable', 'string', 'max:20']),
                TextColumn::make('photo')->label('Photo')->toggleable()->placeholder('—'),
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
            ->filters([
                SelectFilter::make('surface')
                    ->label('Surface')
                    ->options(['food' => 'Nourriture', 'desserts' => 'Desserts', 'drinks' => 'Boissons'])
                    ->query(fn (Builder $query, array $data) => filled($data['value'] ?? null)
                        ? $query->whereHas('category', fn ($q) => $q->where('surface', $data['value']))
                        : $query),
                SelectFilter::make('category_id')
                    ->label('Catégorie')
                    ->options(fn () => MenuCategory::orderBy('surface')->orderBy('position')->get()
                        ->mapWithKeys(fn ($c) => [$c->id => ucfirst($c->surface).' · '.($c->title['fr'] ?? $c->slug)])),
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
