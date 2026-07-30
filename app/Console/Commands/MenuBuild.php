<?php

namespace App\Console\Commands;

use App\Services\MenuBuilder;
use Illuminate\Console\Command;

class MenuBuild extends Command
{
    protected $signature = 'menu:build {--out= : Dossier de sortie}';

    protected $description = 'Génère food-data.js / desserts-data.js / boisson-data.js depuis la base';

    public function handle(MenuBuilder $builder): int
    {
        $out = $this->option('out') ?: storage_path('app/menu-build');
        if (! is_dir($out)) {
            mkdir($out, 0775, true);
        }

        foreach ($builder->all() as $file => $content) {
            file_put_contents("{$out}/{$file}", $content);
            $this->line("  ✓ {$file} (".strlen($content).' o)');
        }

        $this->info("Fichiers générés dans : {$out}");

        return self::SUCCESS;
    }
}
