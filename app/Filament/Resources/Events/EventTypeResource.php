<?php

namespace App\Filament\Resources\Events;

use App\Models\EventType;
use App\Filament\Support\Translatable;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use UnitEnum;

class EventTypeResource extends Resource
{
    protected static ?string $model = EventType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|UnitEnum|null $navigationGroup = 'Location de salle';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $modelLabel = 'type d\'événement';

    protected static ?string $pluralModelLabel = 'Types d\'événements';

    protected static ?int $navigationSort = 60;

    protected static ?string $slug = 'event-types';

    public static function getRecordTitle(?Model $record): string
    {
        return $record ? Translatable::label($record->name) : '';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columnSpanFull()->columns(3)->schema([
                Translatable::field('name', 'Nom', required: true),
                Translatable::field('description', 'Description courte', textarea: true),
                TextInput::make('slug')->label('Identifiant (URL)')->required()->maxLength(120)
                    ->unique(ignoreRecord: true)
                    ->default(fn () => 'nouveau-'.Str::lower(Str::random(5)))
                    ->helperText('Technique : minuscules et tirets.'),
                Select::make('icon')->label('Icône')->options([
                    'heart' => 'Cœur (mariage)', 'ring' => 'Bague (fiançailles)', 'cake' => 'Gâteau (anniversaire)',
                    'cross' => 'Croix (communion)', 'droplet' => 'Goutte (baptême)', 'briefcase' => 'Mallette (entreprise)',
                    'party' => 'Étincelles (fête)', 'baby' => 'Visage (baby shower)', 'flower' => 'Fleur (funérailles)',
                    'award' => 'Trophée (diplôme)', 'star' => 'Étoile (autre)',
                ])->default('star'),
                TextInput::make('sort_order')->label('Ordre')->numeric()->default(0),
                Toggle::make('is_active')->label('Proposé dans le simulateur')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('name')->label('Nom')->getStateUsing(fn ($record) => Translatable::label($record?->name))
                    ->searchable(query: fn ($query, string $search) => $query->where('name', 'like', "%{$search}%")),
                TextColumn::make('slug')->visibleFrom('md')->label('Identifiant')->color('gray'),
                TextColumn::make('quotes_count')->visibleFrom('sm')->counts('quotes')->label('Demandes'),
                ToggleColumn::make('is_active')->label('Actif'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventType::route('/'),
            'create' => Pages\CreateEventType::route('/create'),
            'edit' => Pages\EditEventType::route('/{record}/edit'),
        ];
    }
}
