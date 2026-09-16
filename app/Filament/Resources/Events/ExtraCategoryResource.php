<?php

namespace App\Filament\Resources\Events;

use App\Models\ExtraCategory;
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

class ExtraCategoryResource extends Resource
{
    protected static ?string $model = ExtraCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static string|UnitEnum|null $navigationGroup = 'Location de salle';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $modelLabel = 'catégorie d\'extras';

    protected static ?string $pluralModelLabel = 'Extras';

    protected static ?int $navigationSort = 45;

    protected static ?string $slug = 'event-extras';

    protected static ?string $navigationLabel = 'Extras';

    public static function getRecordTitle(?Model $record): string
    {
        return $record ? Translatable::label($record->name) : '';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Catégorie')->columnSpanFull()->columns(3)->schema([
                Translatable::field('name', 'Nom', required: true),
                Translatable::field('description', 'Description'),
                TextInput::make('slug')->label('Identifiant (URL)')->required()->maxLength(120)
                    ->unique(ignoreRecord: true)
                    ->default(fn () => 'nouveau-'.Str::lower(Str::random(5)))
                    ->helperText('Technique : minuscules et tirets.'),
                TextInput::make('sort_order')->label('Ordre')->numeric()->default(0),
            ]),
            Section::make('Extras')->columnSpanFull()->schema([
                Repeater::make('extraItems')->relationship()->hiddenLabel()
                    ->orderColumn('sort_order')->collapsible()->collapsed()->defaultItems(0)->columns(3)
                    ->itemLabel(fn (array $state) => $state['name']['fr'] ?? $state['name']['nl'] ?? 'Extra')
                    ->addActionLabel('Ajouter un extra')
                    ->schema([
                        Translatable::field('name', 'Nom', required: true),
                        Translatable::field('description', 'Description'),
                        TextInput::make('price')->label('Prix')->numeric()->prefix('€')->required()->default(0),
                        Select::make('price_type')->label('Tarification')->options(\App\Models\ExtraItem::PRICE_TYPES)->required()->default('fixed'),
                        TextInput::make('exclusive_group')->label('Groupe exclusif')
                            ->helperText('Même code = un seul choix possible (ex. « deco » pour les packs).'),
                        Toggle::make('is_default')->label('Coché par défaut'),
                        FileUpload::make('image_path')->label('Photo')->image()->imageEditor()->disk('public')->directory('evenements/extras'),
                        Toggle::make('is_active')->label('Actif')->default(true),
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
                TextColumn::make('extra_items_count')->counts('extraItems')->label('Extras'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExtraCategory::route('/'),
            'create' => Pages\CreateExtraCategory::route('/create'),
            'edit' => Pages\EditExtraCategory::route('/{record}/edit'),
        ];
    }
}
