<?php

namespace App\Filament\Pages;

use App\Models\EventSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;

/** Réglages du simulateur de devis (coordonnées, TVA, destinataires…). */
class EventQuoteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Réglages devis';

    protected static string|UnitEnum|null $navigationGroup = 'Location de salle';

    protected static ?int $navigationSort = 90;

    protected static ?string $title = 'Réglages du simulateur de devis';

    protected static ?string $slug = 'quote-settings';

    /** @var array<string,mixed> */
    public array $data = [];

    public function mount(): void
    {
        foreach (array_keys(EventSetting::DEFAULTS) as $key) {
            $this->data[$key] = EventSetting::get($key);
        }
    }

    public function content(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Section::make('Coordonnées')
                ->description('Affichées sur les PDF et dans l\'e-mail envoyé au client.')
                ->columns(2)->schema([
                    TextInput::make('company_name')->label('Nom affiché')->required(),
                    TextInput::make('email')->label('E-mail de contact')->email()->required()
                        ->helperText('Adresse de réponse des e-mails envoyés aux clients.'),
                    TextInput::make('phone')->label('Téléphone')->required(),
                    TextInput::make('whatsapp')->label('Numéro WhatsApp')->placeholder('32495526656')
                        ->helperText('Format international, chiffres seulement.'),
                    TextInput::make('address')->label('Adresse')->columnSpanFull(),
                ]),
            Section::make('Notifications')->schema([
                TextInput::make('notify_emails')->label('Recevoir les nouvelles demandes')
                    ->placeholder('adresse1@exemple.be, adresse2@exemple.be')
                    ->helperText('Séparées par des virgules. S\'ajoutent aux adresses configurées sur le serveur ('.config('antika.events.notify_emails').').'),
            ]),
            Section::make('Calcul')->columns(3)->schema([
                TextInput::make('tax_rate')->label('TVA (%)')->numeric()->required(),
                TextInput::make('deposit_percentage')->label('Acompte (%)')->numeric()->required(),
                TextInput::make('quote_validity_days')->label('Validité d\'un devis (jours)')->numeric()->required(),
            ]),
            Section::make('Mentions légales')
                ->description('Si renseignés, la case à cocher du formulaire renvoie vers ces pages.')
                ->columns(2)->schema([
                    TextInput::make('terms_url')->label('Conditions générales (URL)')->url(),
                    TextInput::make('privacy_url')->label('Politique de confidentialité (URL)')->url(),
                ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')->label('Enregistrer')->icon('heroicon-o-check')
                ->action(function () {
                    $this->validate();
                    foreach (array_keys(EventSetting::DEFAULTS) as $key) {
                        EventSetting::put($key, $this->data[$key] ?? null);
                    }
                    Notification::make()->title('Réglages enregistrés')->success()->send();
                }),
        ];
    }
}
