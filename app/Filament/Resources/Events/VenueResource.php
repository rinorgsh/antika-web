<?php

namespace App\Filament\Resources\Events;

use App\Models\Venue;
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

class VenueResource extends Resource
{
    protected static ?string $model = Venue::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|UnitEnum|null $navigationGroup = 'Location de salle';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $modelLabel = 'espace';

    protected static ?string $pluralModelLabel = 'Espaces & salles';

    protected static ?int $navigationSort = 50;

    protected static ?string $slug = 'venues';

    public static function getRecordTitle(?Model $record): string
    {
        return $record ? Translatable::label($record->name) : '';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Présentation')->columnSpanFull()->columns(3)->schema([
                Translatable::field('name', 'Nom', required: true),
                Translatable::field('short_description', 'Accroche (affichée dans le simulateur)', textarea: true),
                Translatable::field('description', 'Description complète', textarea: true),
                TextInput::make('slug')->label('Identifiant (URL)')->required()->maxLength(120)
                    ->unique(ignoreRecord: true)
                    ->default(fn () => 'nouveau-'.Str::lower(Str::random(5)))
                    ->helperText('Technique : minuscules et tirets.'),
                TextInput::make('sort_order')->label('Ordre')->numeric()->default(0),
                Toggle::make('is_active')->label('Proposé dans le simulateur')->default(true),
            ]),
            Section::make('Tarif & capacité')->columnSpanFull()->columns(4)->schema([
                TextInput::make('price')->label('Prix de location')->numeric()->prefix('€')->required()->default(0),
                TextInput::make('capacity_seated')->label('Places assises')->numeric(),
                TextInput::make('capacity_standing')->label('Places debout')->numeric(),
                TextInput::make('surface_area')->label('Surface (m²)')->numeric(),
                Toggle::make('has_parking')->label('Parking'),
                Toggle::make('has_vestiaire')->label('Vestiaire'),
                Toggle::make('has_private_toilets')->label('Sanitaires privés'),
                Toggle::make('has_kitchen')->label('Cuisine'),
            ]),
            Section::make('Photos')->columnSpanFull()->schema([
                Repeater::make('venueImages')->relationship()->hiddenLabel()
                    ->orderColumn('sort_order')->grid(3)->defaultItems(0)
                    ->addActionLabel('Ajouter une photo')
                    ->schema([
                        FileUpload::make('image_path')->hiddenLabel()->image()->imageEditor()
                            ->disk('public')->directory('evenements/venues')->required(),
                        Toggle::make('is_primary')->label('Photo principale'),
                    ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('venueImages.image_path')->label('')->disk('public')->limit(1)->square(),
                TextColumn::make('name')->label('Nom')->getStateUsing(fn ($record) => Translatable::label($record?->name))
                    ->searchable(query: fn ($query, string $search) => $query->where('name', 'like', "%{$search}%")),
                TextColumn::make('price')->label('Prix')->money('EUR', locale: 'fr_BE'),
                TextColumn::make('capacity_seated')->visibleFrom('sm')->label('Places'),
                ToggleColumn::make('is_active')->label('Actif'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVenue::route('/'),
            'create' => Pages\CreateVenue::route('/create'),
            'edit' => Pages\EditVenue::route('/{record}/edit'),
        ];
    }
}
