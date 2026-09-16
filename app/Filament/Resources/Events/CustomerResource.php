<?php

namespace App\Filament\Resources\Events;

use App\Models\Customer;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|UnitEnum|null $navigationGroup = 'Location de salle';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $modelLabel = 'client';

    protected static ?string $pluralModelLabel = 'Clients';

    protected static ?int $navigationSort = 20;

    protected static ?string $slug = 'customers';

    public static function getRecordTitle(?Model $record): string
    {
        return $record?->full_name ?? '';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columnSpanFull()->columns(2)->schema([
                TextInput::make('first_name')->label('Prénom')->required(),
                TextInput::make('last_name')->label('Nom')->required(),
                TextInput::make('email')->label('E-mail')->email()->required(),
                TextInput::make('phone')->label('Téléphone')->tel(),
                TextInput::make('company')->label('Société'),
                TextInput::make('city')->label('Ville'),
                Textarea::make('notes')->label('Notes internes')->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('full_name')->label('Nom')
                    ->searchable(['first_name', 'last_name'])->sortable(['last_name']),
                TextColumn::make('email')->label('E-mail')->searchable()->copyable(),
                TextColumn::make('phone')->visibleFrom('md')->label('Téléphone')->searchable(),
                TextColumn::make('company')->visibleFrom('lg')->label('Société')->placeholder('—')->toggleable(),
                TextColumn::make('quotes_count')->visibleFrom('md')->counts('quotes')->label('Demandes'),
                TextColumn::make('created_at')->visibleFrom('lg')->label('Depuis')->date('d/m/Y')->sortable(),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomer::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
