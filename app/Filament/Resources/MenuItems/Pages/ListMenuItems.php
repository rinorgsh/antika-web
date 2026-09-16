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
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Publier la carte en ligne ?')
                ->modalDescription('Les modifications seront visibles immédiatement sur le menu QR.')
                ->modalSubmitActionLabel('Oui, publier')
                ->action(function (MenuPublisher $publisher) {
                    try {
                        $publisher->publish('Mise à jour de la carte depuis l\'admin Antika');
                        Notification::make()
                            ->title('Carte publiée')
                            ->body('Visible tout de suite sur le menu QR.')
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
