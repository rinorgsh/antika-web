<?php

namespace App\Filament\Resources\Festival\Pages;

use App\Filament\Resources\Festival\SponsorApplicationResource;
use App\Models\SponsorApplication;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewSponsorApplication extends ViewRecord
{
    protected static string $resource = SponsorApplicationResource::class;

    public function getTitle(): string
    {
        return $this->record->organization ?: $this->record->name;
    }

    protected function getHeaderActions(): array
    {
        /** @var SponsorApplication $a */
        $a = $this->record;

        return [
            Action::make('status')
                ->label('Changer le statut')->icon('heroicon-o-arrow-path')->color('gray')
                ->schema([Select::make('status')->label('Statut')->options(SponsorApplication::STATUSES)->required()])
                ->fillForm(fn () => ['status' => $a->status])
                ->action(function (array $data) use ($a) {
                    $a->update(['status' => $data['status']]);
                    Notification::make()->title('Statut mis à jour')->success()->send();
                }),
            EditAction::make()->label('Suivi & notes'),
            DeleteAction::make(),
        ];
    }
}
