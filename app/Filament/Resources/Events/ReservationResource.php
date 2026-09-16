<?php

namespace App\Filament\Resources\Events;

use App\Filament\Support\Translatable;
use App\Models\Quote;
use App\Models\Reservation;
use App\Models\Venue;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|UnitEnum|null $navigationGroup = 'Location de salle';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $modelLabel = 'réservation';

    protected static ?string $pluralModelLabel = 'Réservations';

    protected static ?int $navigationSort = 15;

    protected static ?string $slug = 'reservations';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columnSpanFull()->columns(2)->schema([
                Select::make('customer_id')->label('Client')->relationship('customer', 'last_name')
                    ->getOptionLabelFromRecordUsing(fn ($c) => "{$c->full_name} ({$c->email})")
                    ->searchable(['first_name', 'last_name', 'email'])->required(),
                Select::make('quote_id')->label('Devis d\'origine')->relationship('quote', 'quote_number')->searchable(),
                DatePicker::make('event_date')->label('Date de l\'événement')->required()->native(false)->displayFormat('d/m/Y'),
                Select::make('venue_id')->label('Espace')
                    ->options(fn () => Venue::ordered()->get()->mapWithKeys(fn ($v) => [$v->id => Translatable::label($v->name)])),
                Select::make('status')->label('Statut')->options(Reservation::STATUSES)->required()->default('confirmed'),
                Textarea::make('cancellation_reason')->label('Motif d\'annulation')->rows(2)
                    ->visible(fn ($get) => $get('status') === 'cancelled'),
                Textarea::make('admin_notes')->label('Notes internes')->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('event_date')
            ->columns([
                TextColumn::make('event_date')->label('Date')->date('D d/m/Y')->sortable(),
                TextColumn::make('customer.full_name')->label('Client')
                    ->searchable(query: fn ($q, $search) => $q->whereHas('customer', fn ($c) => $c->where('last_name', 'like', "%{$search}%")->orWhere('first_name', 'like', "%{$search}%"))),
                TextColumn::make('venue.name')->visibleFrom('md')->label('Espace')->getStateUsing(fn ($record) => Translatable::label($record?->venue?->name))->placeholder('—'),
                TextColumn::make('quote.guest_count_adults')->visibleFrom('lg')->label('Invités')->placeholder('—'),
                TextColumn::make('quote.total')->visibleFrom('md')->label('Montant')->money('EUR', locale: 'fr_BE')->placeholder('—'),
                TextColumn::make('status')->label('Statut')->badge()
                    ->formatStateUsing(fn ($state) => Reservation::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => Reservation::STATUS_COLORS[$state] ?? 'gray'),
            ])
            ->filters([
                SelectFilter::make('status')->label('Statut')->options(Reservation::STATUSES),
                TernaryFilter::make('upcoming')->label('Période')->default(true)
                    ->trueLabel('À venir')->falseLabel('Passées')->placeholder('Toutes')
                    ->queries(
                        true: fn ($q) => $q->whereDate('event_date', '>=', today()),
                        false: fn ($q) => $q->whereDate('event_date', '<', today()),
                        blank: fn ($q) => $q,
                    ),
            ])
            ->recordActions([
                Action::make('quote')->label('Devis')->icon('heroicon-o-document-text')->color('gray')
                    ->visible(fn (Reservation $r) => $r->quote_id !== null)
                    ->url(fn (Reservation $r) => QuoteResource::getUrl('view', ['record' => $r->quote_id])),
                Action::make('status')->label('Statut')->icon('heroicon-o-arrow-path')->color('gray')
                    ->schema([
                        Select::make('status')->label('Nouveau statut')->options(Reservation::STATUSES)->required(),
                        Textarea::make('reason')->label('Motif (si annulation)')->rows(2),
                    ])
                    ->fillForm(fn (Reservation $r) => ['status' => $r->status])
                    ->action(fn (Reservation $r, array $data) => $r->moveTo($data['status'], $data['reason'] ?? null)),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservation::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}
