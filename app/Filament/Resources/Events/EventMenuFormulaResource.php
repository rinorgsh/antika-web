<?php

namespace App\Filament\Resources\Events;

use App\Models\EventMenuFormula;
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

class EventMenuFormulaResource extends Resource
{
    protected static ?string $model = EventMenuFormula::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cake';

    protected static string|UnitEnum|null $navigationGroup = 'Location de salle';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $modelLabel = 'formule de menu';

    protected static ?string $pluralModelLabel = 'Formules de menu';

    protected static ?int $navigationSort = 30;

    protected static ?string $slug = 'event-menus';

    protected static ?string $navigationLabel = 'Formules de menu';

    public static function getRecordTitle(?Model $record): string
    {
        return $record ? Translatable::label($record->name) : '';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Formule')->columnSpanFull()->columns(3)->schema([
                Translatable::field('name', 'Nom', required: true),
                Translatable::field('short_description', 'Accroche (affichée dans le simulateur)', textarea: true),
                Translatable::field('description', 'Description complète', textarea: true),
                TextInput::make('price_per_person')->label('Prix par personne')->numeric()->prefix('€')->required()->default(0),
                Select::make('type')->label('Service')->options(['seated' => 'Servi à table (1 plat au choix par catégorie)', 'buffet' => 'Buffet (tous les plats)'])->required()->default('seated'),
                TextInput::make('slug')->label('Identifiant (URL)')->required()->maxLength(120)
                    ->unique(ignoreRecord: true)
                    ->default(fn () => 'nouveau-'.Str::lower(Str::random(5)))
                    ->helperText('Technique : minuscules et tirets.'),
                FileUpload::make('image_path')->label('Photo')->image()->imageEditor()->disk('public')->directory('evenements/menus'),
                TextInput::make('sort_order')->label('Ordre')->numeric()->default(0),
                Toggle::make('is_active')->label('Proposée dans le simulateur')->default(true),
            ]),
            Section::make('Catégories & plats')->columnSpanFull()->description('Ex. Entrée, Plat, Dessert. Glissez pour réordonner.')->schema([
                Repeater::make('menuCategories')->relationship()->hiddenLabel()
                    ->orderColumn('sort_order')->collapsible()->defaultItems(0)
                    ->itemLabel(fn (array $state) => $state['name']['fr'] ?? $state['name']['nl'] ?? 'Catégorie')
                    ->addActionLabel('Ajouter une catégorie')
                    ->schema([
                        Translatable::field('name', 'Nom de la catégorie', required: true),
                        Repeater::make('menuItems')->relationship()->label('Plats')
                            ->orderColumn('sort_order')->collapsible()->collapsed()->defaultItems(0)
                            ->itemLabel(fn (array $state) => $state['name']['fr'] ?? $state['name']['nl'] ?? 'Plat')
                            ->addActionLabel('Ajouter un plat')
                            ->columns(3)
                            ->schema([
                                Translatable::field('name', 'Nom', required: true),
                                Translatable::field('description', 'Description'),
                                TextInput::make('supplement_price')->label('Supplément / pers.')->numeric()->prefix('€')->default(0),
                                FileUpload::make('image_path')->label('Photo')->image()->imageEditor()->disk('public')->directory('evenements/dishes'),
                                Toggle::make('is_active')->label('Actif')->default(true),
                            ]),
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
                ImageColumn::make('image_path')->label('')->disk('public')->square(),
                TextColumn::make('name')->label('Nom')->getStateUsing(fn ($record) => Translatable::label($record?->name))
                    ->searchable(query: fn ($query, string $search) => $query->where('name', 'like', "%{$search}%")),
                TextColumn::make('type')->visibleFrom('sm')->label('Service')->badge()->formatStateUsing(fn ($state) => $state === 'buffet' ? 'Buffet' : 'Servi à table'),
                TextColumn::make('price_per_person')->label('Prix / pers.')->money('EUR', locale: 'fr_BE'),
                ToggleColumn::make('is_active')->label('Active'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventMenuFormula::route('/'),
            'create' => Pages\CreateEventMenuFormula::route('/create'),
            'edit' => Pages\EditEventMenuFormula::route('/{record}/edit'),
        ];
    }
}
