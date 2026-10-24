<?php

namespace App\Filament\Resources\Festival;

use App\Models\SponsorApplication;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

/** Festival 1912 (temporaire) : demandes reçues via /sponsors. Seuls statut et notes se modifient. */
class SponsorApplicationResource extends Resource
{
    protected static ?string $model = SponsorApplication::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static string|UnitEnum|null $navigationGroup = 'Festival 1912';

    protected static ?string $navigationLabel = 'Inscriptions';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $modelLabel = 'inscription';

    protected static ?string $pluralModelLabel = 'Inscriptions Festival 1912';

    protected static ?string $slug = 'festival-1912';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        $n = SponsorApplication::where('status', 'new')->count();

        return $n ? (string) $n : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Suivi')->columnSpanFull()->columns(2)->schema([
                Select::make('status')->label('Statut')->options(SponsorApplication::STATUSES)->required(),
                Textarea::make('notes')->label('Notes internes')->rows(4)->columnSpanFull()
                    ->helperText('Visible uniquement dans l\'admin.'),
            ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)->columnSpanFull()->schema([
                Section::make('Contact')->columnSpan(1)->schema([
                    TextEntry::make('name')->label('Nom'),
                    TextEntry::make('organization')->label('Entreprise / organisation')->placeholder('—'),
                    TextEntry::make('phone')->label('Téléphone')->copyable()
                        ->url(fn (SponsorApplication $a) => 'tel:'.preg_replace('/[^\d+]/', '', $a->phone)),
                    TextEntry::make('email')->label('E-mail')->copyable()
                        ->url(fn (SponsorApplication $a) => "mailto:{$a->email}?subject=".rawurlencode('Festival 1912')),
                    TextEntry::make('locale')->label('Langue')->badge()
                        ->formatStateUsing(fn ($state) => SponsorApplication::LOCALE_LABELS[$state] ?? $state),
                ]),
                Section::make('Activité')->columnSpan(2)->schema([
                    TextEntry::make('activity')->label('Activité principale')->placeholder('—'),
                    TextEntry::make('promote')->label('À promouvoir pendant le festival')->placeholder('—'),
                    TextEntry::make('sell')->label('À vendre sur place')->placeholder('—'),
                    TextEntry::make('message')->label('Demandes particulières')->placeholder('—'),
                ]),
            ]),
            Section::make('Suivi')->columnSpanFull()->columns(4)->schema([
                TextEntry::make('participation_type')->label('Type')->badge()
                    ->formatStateUsing(fn ($state) => SponsorApplication::TYPES[$state] ?? $state)
                    ->color(fn ($state) => SponsorApplication::TYPE_COLORS[$state] ?? 'gray'),
                TextEntry::make('status')->label('Statut')->badge()
                    ->formatStateUsing(fn ($state) => SponsorApplication::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => SponsorApplication::STATUS_COLORS[$state] ?? 'gray'),
                TextEntry::make('created_at')->label('Reçue le')->dateTime('d/m/Y à H:i'),
                TextEntry::make('notes')->label('Notes internes')->placeholder('—'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (SponsorApplication $a) => static::getUrl('view', ['record' => $a]))
            ->columns([
                TextColumn::make('created_at')->visibleFrom('md')->label('Reçue')->since()->sortable()
                    ->tooltip(fn (SponsorApplication $a) => $a->created_at->format('d/m/Y H:i')),
                TextColumn::make('name')->label('Nom')->searchable()->weight('bold')
                    ->description(fn (SponsorApplication $a) => $a->organization),
                TextColumn::make('organization')->label('Organisation')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('participation_type')->label('Type')->badge()
                    ->formatStateUsing(fn ($state) => SponsorApplication::TYPES[$state] ?? $state)
                    ->color(fn ($state) => SponsorApplication::TYPE_COLORS[$state] ?? 'gray'),
                TextColumn::make('phone')->visibleFrom('md')->label('Téléphone')->searchable()->copyable(),
                TextColumn::make('email')->visibleFrom('lg')->label('E-mail')->searchable()->copyable(),
                TextColumn::make('locale')->visibleFrom('lg')->label('Langue')->badge()
                    ->formatStateUsing(fn ($s) => $s === 'sq' ? 'AL' : strtoupper($s)),
                TextColumn::make('status')->label('Statut')->badge()
                    ->formatStateUsing(fn ($state) => SponsorApplication::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => SponsorApplication::STATUS_COLORS[$state] ?? 'gray'),
            ])
            ->filters([
                SelectFilter::make('participation_type')->label('Type')->options(SponsorApplication::TYPES)->multiple(),
                SelectFilter::make('status')->label('Statut')->options(SponsorApplication::STATUSES)->multiple(),
            ])
            ->recordActions([ViewAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSponsorApplications::route('/'),
            'view' => Pages\ViewSponsorApplication::route('/{record}'),
            'edit' => Pages\EditSponsorApplication::route('/{record}/edit'),
        ];
    }
}
