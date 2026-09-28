<?php

namespace App\Filament\Resources\Events;

use App\Filament\Support\Translatable;
use App\Models\CallbackRequest;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/** Demandes de rappel : le contenu vient du visiteur, seuls statut et notes se modifient. */
class CallbackRequestResource extends Resource
{
    protected static ?string $model = CallbackRequest::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-phone-arrow-down-left';

    protected static string|UnitEnum|null $navigationGroup = 'Location de salle';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $modelLabel = 'demande de rappel';

    protected static ?string $pluralModelLabel = 'Demandes de rappel';

    protected static ?int $navigationSort = 12;

    protected static ?string $slug = 'callbacks';

    public static function getNavigationBadge(): ?string
    {
        $n = CallbackRequest::where('status', 'new')->count();

        return $n ? (string) $n : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('eventType');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columnSpanFull()->columns(2)->schema([
                Select::make('status')->label('Statut')->options(CallbackRequest::STATUSES)->required(),
                Textarea::make('notes')->label('Notes internes')->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        $fromAds = fn (CallbackRequest $c) => ! empty($c->tracking['gclid']) || ! empty($c->tracking['gbraid']) || ! empty($c->tracking['wbraid']);

        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Reçue')->since()->sortable()
                    ->tooltip(fn (CallbackRequest $c) => $c->created_at->format('d/m/Y H:i')),
                TextColumn::make('name')->label('Nom')->searchable()->weight('bold')
                    ->description(fn (CallbackRequest $c) => $c->message ? str($c->message)->limit(60) : null),
                TextColumn::make('phone')->label('Téléphone')->searchable()->copyable()
                    ->url(fn (CallbackRequest $c) => 'tel:'.preg_replace('/[^\d+]/', '', $c->phone)),
                TextColumn::make('eventType.name')->visibleFrom('md')->label('Type')
                    ->getStateUsing(fn ($record) => $record?->eventType ? Translatable::label($record->eventType->name) : null)->placeholder('—'),
                TextColumn::make('event_date')->visibleFrom('md')->label('Date')->date('d/m/Y')->placeholder('—'),
                TextColumn::make('guest_count')->visibleFrom('lg')->label('Invités')->placeholder('—')->alignEnd(),
                TextColumn::make('locale')->visibleFrom('lg')->label('Langue')->badge()->formatStateUsing(fn ($s) => strtoupper($s)),
                TextColumn::make('source')->visibleFrom('md')->label('Source')->badge()
                    ->getStateUsing(fn (CallbackRequest $c) => $fromAds($c) ? 'Ads' : null)->color('success')->placeholder(''),
                TextColumn::make('status')->label('Statut')->badge()
                    ->formatStateUsing(fn ($state) => CallbackRequest::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => CallbackRequest::STATUS_COLORS[$state] ?? 'gray'),
            ])
            ->filters([
                SelectFilter::make('status')->label('Statut')->options(CallbackRequest::STATUSES)->multiple(),
            ])
            ->recordActions([EditAction::make()->label('Suivi')]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCallbackRequests::route('/'),
            'edit' => Pages\EditCallbackRequest::route('/{record}/edit'),
        ];
    }
}
