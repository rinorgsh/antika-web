<?php

namespace App\Filament\Resources\Events;

use App\Models\DrinkCategory;
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

class DrinkCategoryResource extends Resource
{
    protected static ?string $model = DrinkCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static string|UnitEnum|null $navigationGroup = 'Location de salle';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $modelLabel = 'catégorie de boissons';

    protected static ?string $pluralModelLabel = 'Boissons';

    protected static ?int $navigationSort = 40;

    protected static ?string $slug = 'event-drinks';

    protected static ?string $navigationLabel = 'Boissons';

    public static function getRecordTitle(?Model $record): string
    {
        return $record ? Translatable::label($record->name) : '';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Catégorie')->columnSpanFull()->columns(3)->schema([
                Translatable::field('name', 'Nom', required: true),
                TextInput::make('slug')->label('Identifiant (URL)')->required()->maxLength(120)
                    ->unique(ignoreRecord: true)
                    ->default(fn () => 'nouveau-'.Str::lower(Str::random(5)))
                    ->helperText('Technique : minuscules et tirets.'),
                Select::make('role')->label('Affichage')->placeholder('Boissons (à volonté ou à la bouteille)')
                    ->options(DrinkCategory::ROLES),
                TextInput::make('sort_order')->label('Ordre')->numeric()->default(0),
            ]),
            Section::make('Options')->columnSpanFull()->schema([
                Repeater::make('drinkOptions')->relationship()->hiddenLabel()
                    ->orderColumn('sort_order')->collapsible()->defaultItems(0)->columns(3)
                    ->itemLabel(fn (array $state) => $state['name']['fr'] ?? $state['name']['nl'] ?? 'Option')
                    ->addActionLabel('Ajouter une boisson')
                    ->schema([
                        Translatable::field('name', 'Nom', required: true),
                        Translatable::field('description', 'Précision (apéritif)'),
                        Select::make('unit_type')->label('Vente')->options(\App\Models\DrinkOption::UNIT_TYPES)->required()->default('glass'),
                        TextInput::make('price_all_in')->label('Forfait à volonté / pers.')->numeric()->prefix('€'),
                        TextInput::make('price_per_unit')->label('Prix unitaire / bouteille')->numeric()->prefix('€'),
                        FileUpload::make('image_path')->label('Photo')->image()->imageEditor()->disk('public')->directory('evenements/drinks'),
                        Toggle::make('is_active')->label('Active')->default(true),
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
                TextColumn::make('name')->label('Nom')->getStateUsing(fn ($record) => Translatable::label($record?->name))
                    ->searchable(query: fn ($query, string $search) => $query->where('name', 'like', "%{$search}%")),
                TextColumn::make('role')->label('Affichage')->badge()->placeholder('Boissons')
                    ->formatStateUsing(fn ($state) => ['aperitif' => 'Apéritif', 'bubbles' => 'Bulles'][$state] ?? $state),
                TextColumn::make('drink_options_count')->counts('drinkOptions')->label('Options'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDrinkCategory::route('/'),
            'create' => Pages\CreateDrinkCategory::route('/create'),
            'edit' => Pages\EditDrinkCategory::route('/{record}/edit'),
        ];
    }
}
