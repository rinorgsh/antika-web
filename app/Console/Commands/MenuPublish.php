<?php

namespace App\Console\Commands;

use App\Services\MenuPublisher;
use Illuminate\Console\Command;

class MenuPublish extends Command
{
    protected $signature = 'menu:publish {--message= : Note enregistrée dans l\'historique}';

    protected $description = 'Régénère la carte et la publie sur le menu QR (/carte/)';

    public function handle(MenuPublisher $publisher): int
    {
        $message = $this->option('message') ?: 'Mise à jour de la carte depuis l\'admin Antika';

        try {
            $publication = $publisher->publish($message);
            $this->info("Carte publiée (publication n°{$publication->id}).");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Échec : '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
