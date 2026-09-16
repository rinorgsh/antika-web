<?php

namespace App\Filament\Resources\Events;

use App\Filament\Support\Translatable;
use App\Models\BlockedDate;
use App\Models\Venue;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

/** Jours fermés à la location : le simulateur les affiche comme indisponibles. */
class BlockedDateResource extends Resource
{
    protected static ?string $model = BlockedDate::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-no-symbol';

    protected static string|UnitEnum|null $navigationGroup = 'Location de salle';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $modelLabel = 'date bloquée';

    protected static ?string $pluralModelLabel = 'Dates bloquées';

    protected static ?int $navigationSort = 70;

    protected static ?string $slug = 'blocked-dates';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('date')->label('Date')->required()->native(false)->displayFormat('d/m/Y'),
            Select::make('venue_id')->label('Espace')
                ->options(fn () => Venue::ordered()->get()->mapWithKeys(fn ($v) => [$v->id => Translatable::label($v->name)]))
                ->placeholder('Tout le domaine'),
            TextInput::make('reason')->label('Motif (interne)')->maxLength(255)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date')
            ->columns([
                TextColumn::make('date')->label('Date')->date('l d/m/Y')->sortable(),
                TextColumn::make('venue.name')->label('Espace')->getStateUsing(fn ($record) => Translatable::label($record?->venue?->name))->placeholder('Tout le domaine'),
                TextColumn::make('reason')->visibleFrom('sm')->label('Motif')->placeholder('—'),
            ])
            ->filters([
                TernaryFilter::make('upcoming')->label('Période')->default(true)
                    ->trueLabel('À venir')->falseLabel('Passées')->placeholder('Toutes')
                    ->queries(
                        true: fn ($q) => $q->whereDate('date', '>=', today()),
                        false: fn ($q) => $q->whereDate('date', '<', today()),
                        blank: fn ($q) => $q,
                    ),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlockedDate::route('/'),
            'create' => Pages\CreateBlockedDate::route('/create'),
            'edit' => Pages\EditBlockedDate::route('/{record}/edit'),
        ];
    }
}
