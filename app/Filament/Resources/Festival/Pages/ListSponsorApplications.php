<?php

namespace App\Filament\Resources\Festival\Pages;

use App\Filament\Resources\Festival\SponsorApplicationResource;
use App\Models\SponsorApplication;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListSponsorApplications extends ListRecords
{
    protected static string $resource = SponsorApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('page')->label('Voir la page')->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')->url(route('sponsors.index'), shouldOpenInNewTab: true),

            // Export Excel : CSV point-virgule avec BOM, s'ouvre directement dans Excel (BE/FR).
            Action::make('export')->label('Exporter (Excel)')->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => response()->streamDownload(function () {
                    $out = fopen('php://output', 'w');
                    fwrite($out, "\xEF\xBB\xBF");
                    fputcsv($out, ['Reçue le', 'Type', 'Nom', 'Organisation', 'Téléphone', 'E-mail', 'Activité', 'À promouvoir', 'À vendre', 'Demandes', 'Langue', 'Statut', 'Notes'], ';');
                    SponsorApplication::orderBy('created_at')->each(function (SponsorApplication $a) use ($out) {
                        fputcsv($out, [
                            $a->created_at->format('d/m/Y H:i'),
                            SponsorApplication::TYPES[$a->participation_type] ?? $a->participation_type,
                            $a->name, $a->organization, $a->phone, $a->email,
                            $a->activity, $a->promote, $a->sell, $a->message,
                            SponsorApplication::LOCALE_LABELS[$a->locale] ?? $a->locale,
                            SponsorApplication::STATUSES[$a->status] ?? $a->status,
                            $a->notes,
                        ], ';');
                    });
                    fclose($out);
                }, 'festival-1912-inscriptions-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8'])),
        ];
    }
}
