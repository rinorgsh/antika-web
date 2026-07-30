<?php

namespace App\Filament\Resources\MenuItems\Pages;

use App\Filament\Resources\MenuItems\MenuItemResource;
use App\Services\MenuPublisher;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListMenuItems extends ListRecords
{
    protected static string $resource = MenuItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('publish')
                ->label('Publier la carte')
                ->icon('heroicon-o-rocket-launch')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Publier la carte en ligne ?')
                ->modalDescription('Les modifications seront envoyées au menu QR (menu.antika-resto.ovh). Visible par les clients en une minute environ.')
                ->modalSubmitActionLabel('Oui, publier')
                ->action(function (MenuPublisher $publisher) {
                    if (! $publisher->isConfigured()) {
                        Notification::make()
                            ->title('Publication non configurée')
                            ->body('Le jeton GitHub (ANTIKA_GITHUB_TOKEN) n\'est pas encore renseigné.')
                            ->warning()->send();

                        return;
                    }

                    try {
                        $url = $publisher->publish('Mise à jour de la carte depuis l\'admin Antika');
                        Notification::make()
                            ->title('Carte publiée ✓')
                            ->body('Les changements seront visibles sur le menu QR dans ~1 min.')
                            ->success()->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Échec de la publication')
                            ->body($e->getMessage())
                            ->danger()->persistent()->send();
                    }
                }),
            CreateAction::make()->label('Nouveau plat'),
        ];
    }
}
