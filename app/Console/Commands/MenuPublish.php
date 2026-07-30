<?php

namespace App\Console\Commands;

use App\Services\MenuPublisher;
use Illuminate\Console\Command;

class MenuPublish extends Command
{
    protected $signature = 'menu:publish {--message= : Message de commit}';

    protected $description = 'Régénère la carte et la publie sur le menu QR (commit GitHub)';

    public function handle(MenuPublisher $publisher): int
    {
        if (! $publisher->isConfigured()) {
            $this->error('Jeton GitHub manquant (ANTIKA_GITHUB_TOKEN).');

            return self::FAILURE;
        }

        $message = $this->option('message') ?: 'Mise à jour de la carte depuis l\'admin Antika';

        try {
            $url = $publisher->publish($message);
            $this->info('Carte publiée ✓');
            $this->line('Commit : '.$url);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Échec : '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
