<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\QuoteResource;
use App\Filament\Resources\Events\ReservationResource;
use App\Models\Quote;
use App\Models\Reservation;
use App\Services\QuotePdf;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewQuote extends ViewRecord
{
    protected static string $resource = QuoteResource::class;

    public function getTitle(): string
    {
        return "Demande {$this->record->quote_number}";
    }

    protected function getHeaderActions(): array
    {
        /** @var Quote $quote */
        $quote = $this->record;
        $phone = preg_replace('/\D/', '', (string) $quote->customer->phone);
        // Numéro belge saisi en 04… -> format international pour WhatsApp.
        $whatsapp = str_starts_with($phone, '0') ? '32'.substr($phone, 1) : $phone;

        return [
            Action::make('status')
                ->label('Changer le statut')->icon('heroicon-o-arrow-path')->color('gray')
                ->schema([Select::make('status')->label('Statut')->options(Quote::STATUSES)->required()])
                ->fillForm(fn () => ['status' => $quote->status])
                ->action(function (array $data) use ($quote) {
                    $quote->update(['status' => $data['status']]);
                    Notification::make()->title('Statut mis à jour')->success()->send();
                }),

            Action::make('pdf')
                ->label('Devis PDF')->icon('heroicon-o-document-arrow-down')->color('gray')
                ->action(fn () => response()->streamDownload(
                    fn () => print(QuotePdf::make($quote, showPrices: true)->output()),
                    "devis-{$quote->quote_number}.pdf",
                )),

            Action::make('reservation')
                ->label($quote->reservation ? 'Voir la réservation' : 'Convertir en réservation')
                ->icon('heroicon-o-calendar-days')->color('primary')
                ->url(fn () => $quote->reservation ? ReservationResource::getUrl('edit', ['record' => $quote->reservation]) : null)
                ->requiresConfirmation(fn () => ! $quote->reservation)
                ->modalDescription('Crée une réservation confirmée à cette date et passe la demande en « Accepté ».')
                ->action(function () use ($quote) {
                    $reservation = Reservation::create([
                        'quote_id' => $quote->id,
                        'customer_id' => $quote->customer_id,
                        'venue_id' => $quote->quoteVenues()->value('venue_id'),
                        'event_date' => $quote->event_date,
                        'status' => 'confirmed',
                    ]);
                    $quote->update(['status' => 'accepted']);
                    Notification::make()->title('Réservation créée')
                        ->body('La date est désormais indisponible dans le simulateur pour cet espace.')->success()->send();

                    return redirect(ReservationResource::getUrl('edit', ['record' => $reservation]));
                }),

            ActionGroup::make([
                Action::make('call')->label('Appeler')->icon('heroicon-o-phone')
                    ->url('tel:+'.$whatsapp),
                Action::make('whatsapp')->label('WhatsApp')->icon('heroicon-o-chat-bubble-left-right')
                    ->url("https://wa.me/{$whatsapp}", shouldOpenInNewTab: true),
                Action::make('email')->label('E-mail')->icon('heroicon-o-envelope')
                    ->url("mailto:{$quote->customer->email}?subject=".rawurlencode("Votre demande {$quote->quote_number} — Antika")),
                EditAction::make()->label('Suivi & notes'),
                DeleteAction::make(),
            ])->label('Contacter')->icon('heroicon-o-ellipsis-vertical')->button()->color('gray'),
        ];
    }
}
