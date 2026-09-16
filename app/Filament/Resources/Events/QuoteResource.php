<?php

namespace App\Filament\Resources\Events;

use App\Filament\Support\Translatable;
use App\Models\EventType;
use App\Models\Quote;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static string|UnitEnum|null $navigationGroup = 'Location de salle';

    protected static ?string $navigationLabel = 'Demandes de devis';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $modelLabel = 'demande de devis';

    protected static ?string $pluralModelLabel = 'Demandes de devis';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'quotes';

    protected static ?string $recordTitleAttribute = 'quote_number';

    /** Pastille : demandes pas encore traitées. */
    public static function getNavigationBadge(): ?string
    {
        $n = Quote::where('status', 'new')->count();

        return $n ? (string) $n : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['customer', 'eventType']);
    }

    /** Seuls le suivi et les notes se modifient : le contenu vient du client. */
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Suivi')->columnSpanFull()->columns(2)->schema([
                Select::make('status')->label('Statut')->options(Quote::STATUSES)->required(),
                DatePicker::make('valid_until')->label('Valable jusqu\'au')->native(false)->displayFormat('d/m/Y'),
                Textarea::make('admin_notes')->label('Notes internes')->rows(5)->columnSpanFull()
                    ->helperText('Visible uniquement dans l\'admin.'),
            ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)->columnSpanFull()->schema([
                Section::make('Client')->columnSpan(1)->schema([
                    TextEntry::make('customer.full_name')->label('Nom'),
                    TextEntry::make('customer.email')->label('E-mail')->copyable()
                        ->url(fn (Quote $q) => 'mailto:'.$q->customer->email),
                    TextEntry::make('customer.phone')->label('Téléphone')->copyable()
                        ->url(fn (Quote $q) => 'tel:'.preg_replace('/[^0-9+]/', '', (string) $q->customer->phone)),
                    TextEntry::make('customer.company')->label('Société')->placeholder('—'),
                    TextEntry::make('locale')->label('Langue')->badge()
                        ->formatStateUsing(fn ($state) => ['nl' => 'Néerlandais', 'fr' => 'Français', 'en' => 'Anglais'][$state] ?? $state),
                ]),
                Section::make('Événement')->columnSpan(1)->schema([
                    TextEntry::make('eventType.name')->label('Type')->getStateUsing(fn ($record) => Translatable::label($record?->eventType?->name)),
                    TextEntry::make('event_date')->label('Date')->date('l d F Y'),
                    TextEntry::make('event_time_slot')->label('Moment')->formatStateUsing(fn ($state) => Quote::TIME_SLOTS[$state] ?? $state),
                    TextEntry::make('guest_count_adults')->label('Invités'),
                ]),
                Section::make('Suivi')->columnSpan(1)->schema([
                    TextEntry::make('status')->label('Statut')->badge()
                        ->formatStateUsing(fn ($state) => Quote::STATUSES[$state] ?? $state)
                        ->color(fn ($state) => Quote::STATUS_COLORS[$state] ?? 'gray'),
                    TextEntry::make('total')->label('Estimation TVAC')->money('EUR', locale: 'fr_BE'),
                    TextEntry::make('created_at')->label('Reçue le')->dateTime('d/m/Y à H:i'),
                    TextEntry::make('tracking')->label('Provenance')
                        ->getStateUsing(fn (Quote $q) => match (true) {
                            ! empty($q->tracking['gclid']) || ! empty($q->tracking['gbraid']) || ! empty($q->tracking['wbraid']) => 'Google Ads',
                            ! empty($q->tracking['utm_source']) => 'Campagne : '.$q->tracking['utm_source'],
                            default => 'Site (direct / naturel)',
                        })
                        ->badge()->color(fn ($state) => $state === 'Google Ads' ? 'success' : 'gray'),
                    TextEntry::make('admin_notes')->label('Notes internes')->placeholder('—'),
                ]),
            ]),
            Section::make('Sélection du client')->columnSpanFull()->schema([
                View::make('filament.events.quote-lines'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (Quote $q) => static::getUrl('view', ['record' => $q]))
            ->columns([
                TextColumn::make('quote_number')->label('N°')->searchable()->weight('bold'),
                TextColumn::make('created_at')->visibleFrom('md')->label('Reçue')->since()->sortable()
                    ->tooltip(fn (Quote $q) => $q->created_at->format('d/m/Y H:i')),
                TextColumn::make('customer.full_name')->label('Client')
                    ->description(fn (Quote $q) => $q->customer->phone)
                    ->searchable(query: fn (Builder $q, string $search) => $q->whereHas('customer', fn ($c) => $c
                        ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"))),
                TextColumn::make('eventType.name')->visibleFrom('md')->label('Type')->getStateUsing(fn ($record) => Translatable::label($record?->eventType?->name)),
                TextColumn::make('event_date')->label('Date')->date('d/m/Y')->sortable()->visibleFrom('sm'),
                TextColumn::make('guest_count_adults')->visibleFrom('lg')->label('Invités')->alignEnd(),
                TextColumn::make('total')->visibleFrom('sm')->label('Estimation')->money('EUR', locale: 'fr_BE')->sortable()->alignEnd(),
                TextColumn::make('source')->visibleFrom('md')->label('Source')->badge()
                    ->getStateUsing(fn (Quote $q) => ! empty($q->tracking['gclid']) || ! empty($q->tracking['gbraid']) || ! empty($q->tracking['wbraid']) ? 'Ads' : null)
                    ->color('success')->placeholder(''),
                TextColumn::make('status')->label('Statut')->badge()
                    ->formatStateUsing(fn ($state) => Quote::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => Quote::STATUS_COLORS[$state] ?? 'gray'),
            ])
            ->filters([
                SelectFilter::make('status')->label('Statut')->options(Quote::STATUSES)->multiple(),
                SelectFilter::make('event_type_id')->label('Type')
                    ->options(fn () => EventType::ordered()->get()->mapWithKeys(fn ($t) => [$t->id => Translatable::label($t->name)])),
                TernaryFilter::make('ads')->label('Google Ads')
                    ->trueLabel('Venues d\'une annonce')->falseLabel('Hors annonces')
                    ->queries(
                        true: fn ($q) => $q->whereNotNull('tracking')->where('tracking', 'like', '%clid%'),
                        false: fn ($q) => $q->where(fn ($w) => $w->whereNull('tracking')->orWhere('tracking', 'not like', '%clid%')),
                        blank: fn ($q) => $q,
                    ),
                Filter::make('upcoming')->label('Événement à venir')
                    ->query(fn ($q) => $q->whereDate('event_date', '>=', today())),
                TrashedFilter::make(),
            ])
            ->recordActions([ViewAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuote::route('/'),
            'view' => Pages\ViewQuote::route('/{record}'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
        ];
    }
}
