<?php

namespace App\Filament\Pages;

use App\Models\MenuSetting;
use App\Services\MenuPublisher;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Réglages de la soirée + interrupteur Restaurant / Événement.
 *
 * L'interrupteur n'agit qu'au moment de la publication : il est écrit dans
 * config.json, que les cinq pages du menu lisent à chaque ouverture. Tant
 * qu'on n'a pas publié, les clients voient toujours la carte du restaurant.
 */
class EventSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Événement';

    protected static string|UnitEnum|null $navigationGroup = 'Carte';

    protected static ?int $navigationSort = 40;

    protected static ?string $title = 'Soirée événement';

    /** @var array<string,mixed> */
    public array $data = [];

    public function mount(): void
    {
        $this->data = [
            'enabled' => (bool) MenuSetting::get('event.enabled', false),
            'title' => MenuSetting::get('event.title', ''),
            'subtitle' => MenuSetting::get('event.subtitle', ''),
            'footerNote' => MenuSetting::get('event.footerNote', ''),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Basculer la carte')
                    ->description("Quand l'interrupteur est allumé et la carte publiée, le QR code ouvre la page événement au lieu du menu du restaurant. Toutes les pages du menu redirigent, même en arrivant par un favori.")
                    ->schema([
                        Toggle::make('enabled')
                            ->label('Afficher la page événement aux clients')
                            ->helperText("Prend effet à la publication. Éteindre pour revenir à la carte normale."),
                    ]),

                Section::make('La soirée')
                    ->description('Ces textes sont en anglais : la page événement l\'est entièrement.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Titre de la soirée')
                            ->placeholder('Tropical Night')
                            ->maxLength(60),
                        TextInput::make('subtitle')
                            ->label('Sous-titre')
                            ->placeholder('Lounge · Bar · Discotheque')
                            ->maxLength(80),
                        TextInput::make('footerNote')
                            ->label('Mention de bas de page')
                            ->placeholder('Prices in euros, VAT and service included.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Enregistrer')
                ->icon('heroicon-o-check')
                ->action(function () {
                    foreach (['enabled', 'title', 'subtitle', 'footerNote'] as $k) {
                        MenuSetting::put("event.{$k}", $this->data[$k] ?? null);
                    }

                    Notification::make()
                        ->title('Réglages enregistrés')
                        ->body("Rien n'est encore visible par les clients : il faut publier la carte.")
                        ->success()->send();
                }),

            Action::make('publish')
                ->label('Publier la carte')
                ->icon('heroicon-o-rocket-launch')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading(fn () => ($this->data['enabled'] ?? false)
                    ? 'Basculer les clients sur la page événement ?'
                    : 'Revenir à la carte du restaurant ?')
                ->modalDescription(fn () => ($this->data['enabled'] ?? false)
                    ? "Le QR code ouvrira la soirée « ".($this->data['title'] ?: 'sans titre')." ». Visible en une minute environ."
                    : 'Le QR code réouvrira la carte du restaurant. Visible en une minute environ.')
                ->modalSubmitActionLabel('Oui, publier')
                ->action(function (MenuPublisher $publisher) {
                    // On enregistre avant de publier : sinon on publierait l'état d'avant.
                    foreach (['enabled', 'title', 'subtitle', 'footerNote'] as $k) {
                        MenuSetting::put("event.{$k}", $this->data[$k] ?? null);
                    }

                    if (! $publisher->isConfigured()) {
                        Notification::make()
                            ->title('Publication non configurée')
                            ->body("Le jeton GitHub (ANTIKA_GITHUB_TOKEN) n'est pas renseigné.")
                            ->warning()->send();

                        return;
                    }

                    try {
                        $publisher->publish(($this->data['enabled'] ?? false)
                            ? 'Bascule sur la page événement'
                            : 'Retour à la carte du restaurant');

                        Notification::make()
                            ->title('Carte publiée ✓')
                            ->body(($this->data['enabled'] ?? false)
                                ? 'Les clients verront la soirée dans ~1 min.'
                                : 'Les clients reverront la carte du restaurant dans ~1 min.')
                            ->success()->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Échec de la publication')
                            ->body($e->getMessage())
                            ->danger()->persistent()->send();
                    }
                }),
        ];
    }
}
