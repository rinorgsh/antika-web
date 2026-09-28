<?php

namespace App\Console\Commands;

use App\Models\DrinkOption;
use App\Models\EventMenuFormula;
use App\Models\EventMenuItem;
use App\Models\ExtraItem;
use App\Models\VenueImage;
use App\Services\Thumbnails;
use Illuminate\Console\Command;

class ImagesThumbs extends Command
{
    protected $signature = 'images:thumbs';

    protected $description = 'Prépare les miniatures WebP du simulateur (à lancer après chaque déploiement)';

    public function handle(): int
    {
        $sets = [
            [VenueImage::pluck('image_path'), 800],
            [EventMenuFormula::pluck('image_path'), 800],
            [EventMenuItem::pluck('image_path'), 400],
            [DrinkOption::pluck('image_path'), 160],
            [ExtraItem::pluck('image_path'), 160],
        ];

        $done = 0;
        foreach ($sets as [$paths, $width]) {
            foreach ($paths->filter()->unique() as $path) {
                $url = Thumbnails::url($path, $width);
                if ($url === null) {
                    $this->warn("  image manquante : {$path}");
                } elseif (str_starts_with($url, '/thumbs/') && Thumbnails::make($path, $width)) {
                    $done++;
                }
            }
        }

        $this->info("{$done} miniatures prêtes.");

        return self::SUCCESS;
    }
}
