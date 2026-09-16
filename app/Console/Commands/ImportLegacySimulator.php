<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Reprise des données de l'ancien simulateur baba-event (baba-event.on-forge.com).
 *
 * Lit la base de l'ancienne application (connexion « legacy_simulator ») et
 * recopie tout dans les tables du site : salles, formules, boissons, extras,
 * clients, devis, réservations, dates bloquées, réglages. Les identifiants sont
 * conservés, les textes rangés en français dans les champs traduits, les
 * images recopiées dans storage/app/public/evenements.
 *
 * Ne modifie jamais l'ancienne base (lecture seule).
 *
 * Sur le serveur :
 *   php artisan events:import-legacy \
 *     --images=/home/forge/baba-event.on-forge.com/current/public \
 *     --storage=/home/forge/baba-event.on-forge.com/current/storage/app/public
 */
class ImportLegacySimulator extends Command
{
    protected $signature = 'events:import-legacy
        {--images= : Dossier public/ de l\'ancienne application (images livrées et téléversées)}
        {--storage= : Dossier storage/app/public de l\'ancienne application}
        {--with-users : Recopier aussi les comptes admin (même e-mail = ignoré)}
        {--force : Vider d\'abord les tables du simulateur si elles contiennent déjà des données}';

    protected $description = 'Importe les données de l\'ancien simulateur baba-event';

    private Connection $legacy;

    /** @var array<string,int> */
    private array $counts = [];

    /** @var string[] */
    private array $missingImages = [];

    /** Tables cibles, dans l'ordre de vidage (enfants d'abord). */
    private const TARGET_TABLES = [
        'reservations', 'blocked_dates', 'quote_extras', 'quote_drinks', 'quote_menu_choices',
        'quote_menus', 'quote_venues', 'quotes', 'customers', 'extra_items', 'extra_categories',
        'drink_options', 'drink_categories', 'event_menu_items', 'event_menu_categories',
        'event_menu_formulas', 'venue_images', 'venues', 'event_types', 'event_settings',
    ];

    public function handle(): int
    {
        $this->legacy = DB::connection('legacy_simulator');

        try {
            $this->legacy->getPdo();
        } catch (\Throwable $e) {
            $this->error('Connexion à l\'ancienne base impossible : '.$e->getMessage());
            $this->line('Vérifiez LEGACY_SIM_DB_* dans le .env (base par défaut : babaevent).');

            return self::FAILURE;
        }

        if (DB::table('quotes')->exists() || DB::table('venues')->exists()) {
            if (! $this->option('force')) {
                $this->error('Les tables du simulateur contiennent déjà des données. Relancez avec --force pour les remplacer.');

                return self::FAILURE;
            }
        }

        DB::transaction(function () {
            if ($this->option('force')) {
                foreach (self::TARGET_TABLES as $table) {
                    DB::table($table)->delete();
                }
            }

            $this->importCatalogue();
            $this->importCustomersAndQuotes();
            $this->importSettings();

            if ($this->option('with-users')) {
                $this->importUsers();
            }
        });

        \App\Models\EventSetting::flushCache();

        $this->table(['Table', 'Lignes'], collect($this->counts)->map(fn ($n, $t) => [$t, $n])->values()->all());

        if ($this->missingImages) {
            $this->warn(count($this->missingImages).' image(s) introuvable(s) — lien conservé tel quel :');
            foreach (array_unique($this->missingImages) as $path) {
                $this->line("  - {$path}");
            }
        }

        $this->info('Import terminé. Vérifiez l\'admin (Événements) puis traduisez les textes en NL / EN.');

        return self::SUCCESS;
    }

    /* ------------------------------------------------------------------ */

    private function importCatalogue(): void
    {
        $this->copy('event_types', 'event_types', fn ($r) => [
            'id' => $r->id,
            'name' => $this->fr($r->name),
            'slug' => $r->slug,
            'description' => $this->fr($r->description ?? null),
            'icon' => $r->icon ?? null,
            'is_active' => $r->is_active ?? true,
            'sort_order' => $r->sort_order ?? 0,
        ] + $this->stamps($r));

        $this->copy('venues', 'venues', fn ($r) => [
            'id' => $r->id,
            'name' => $this->fr($r->name),
            'slug' => $r->slug,
            'description' => $this->fr($r->description ?? null),
            'short_description' => $this->fr($r->short_description ?? null),
            'price' => $r->price ?? 0,
            'capacity_seated' => $r->capacity_seated ?? null,
            'capacity_standing' => $r->capacity_standing ?? null,
            'has_parking' => $r->has_parking ?? true,
            'has_vestiaire' => $r->has_vestiaire ?? false,
            'has_private_toilets' => $r->has_private_toilets ?? true,
            'has_kitchen' => $r->has_kitchen ?? false,
            'surface_area' => $r->surface_area ?? null,
            'is_active' => $r->is_active ?? true,
            'sort_order' => $r->sort_order ?? 0,
        ] + $this->stamps($r));

        $this->copy('venue_images', 'venue_images', fn ($r) => [
            'id' => $r->id,
            'venue_id' => $r->venue_id,
            'image_path' => $this->image($r->image_path),
            'alt_text' => $r->alt_text ?? null,
            'is_primary' => $r->is_primary ?? false,
            'sort_order' => $r->sort_order ?? 0,
        ] + $this->stamps($r));

        $this->copy('menu_formulas', 'event_menu_formulas', fn ($r) => [
            'id' => $r->id,
            'name' => $this->fr($r->name),
            'slug' => $r->slug,
            'description' => $this->fr($r->description ?? null),
            'short_description' => $this->fr($r->short_description ?? null),
            'price_per_person' => $r->price_per_person ?? 0,
            'type' => $r->type ?? 'seated',
            'image_path' => $this->image($r->image_path ?? null),
            'is_active' => $r->is_active ?? true,
            'sort_order' => $r->sort_order ?? 0,
        ] + $this->stamps($r));

        $this->copy('menu_categories', 'event_menu_categories', fn ($r) => [
            'id' => $r->id,
            'menu_formula_id' => $r->menu_formula_id,
            'name' => $this->fr($r->name),
            'sort_order' => $r->sort_order ?? 0,
        ] + $this->stamps($r));

        $this->copy('menu_items', 'event_menu_items', fn ($r) => [
            'id' => $r->id,
            'menu_category_id' => $r->menu_category_id,
            'name' => $this->fr($r->name),
            'description' => $this->fr($r->description ?? null),
            'supplement_price' => $r->supplement_price ?? 0,
            'image_path' => $this->image($r->image_path ?? null),
            'is_active' => $r->is_active ?? true,
            'sort_order' => $r->sort_order ?? 0,
        ] + $this->stamps($r));

        // Rôle déduit du nom : l'ancien écran reconnaissait l'apéritif et le
        // champagne par mots-clés. On le fige une fois pour toutes.
        $roles = [];
        $this->copy('drink_categories', 'drink_categories', function ($r) use (&$roles) {
            $key = mb_strtolower(($r->name ?? '').' '.($r->slug ?? ''));
            $role = match (true) {
                str_contains($key, 'aperit') => 'aperitif',
                str_contains($key, 'champagne') || str_contains($key, 'cava') || str_contains($key, 'bulle') => 'bubbles',
                default => null,
            };
            $roles[$r->id] = $role;

            return [
                'id' => $r->id,
                'name' => $this->fr($r->name),
                'slug' => $r->slug,
                'role' => $role,
                'sort_order' => $r->sort_order ?? 0,
            ] + $this->stamps($r);
        });

        $this->copy('drink_options', 'drink_options', function ($r) use (&$roles) {
            $description = null;
            if (($roles[$r->drink_category_id] ?? null) === 'aperitif') {
                // Textes qui étaient écrits en dur dans l'ancien écran.
                $description = str_contains(mb_strtolower($r->name), 'hapjes')
                    ? ['nl' => 'Aperitief met hapjes en amuse-bouches', 'fr' => 'Apéritif avec bouchées et amuse-bouches', 'en' => 'Drinks reception with bites and amuse-bouches']
                    : ['nl' => 'Klassieke aperitiefreceptie', 'fr' => 'Réception apéritive classique', 'en' => 'Classic drinks reception'];
            }

            return [
                'id' => $r->id,
                'drink_category_id' => $r->drink_category_id,
                'name' => $this->fr($r->name),
                'description' => $description ? json_encode($description, JSON_UNESCAPED_UNICODE) : null,
                'image_path' => $this->image($r->image_path ?? null),
                'price_per_unit' => $r->price_per_unit ?? null,
                'price_all_in' => $r->price_all_in ?? null,
                'unit_type' => $r->unit_type ?? 'glass',
                'is_active' => $r->is_active ?? true,
                'sort_order' => $r->sort_order ?? 0,
            ] + $this->stamps($r);
        });

        $this->copy('extra_categories', 'extra_categories', fn ($r) => [
            'id' => $r->id,
            'name' => $this->fr($r->name),
            'slug' => $r->slug,
            'description' => $this->fr($r->description ?? null),
            'icon' => $r->icon ?? null,
            'sort_order' => $r->sort_order ?? 0,
        ] + $this->stamps($r));

        $this->copy('extra_items', 'extra_items', fn ($r) => [
            'id' => $r->id,
            'extra_category_id' => $r->extra_category_id,
            'name' => $this->fr($r->name),
            'description' => $this->fr($r->description ?? null),
            'price' => $r->price ?? 0,
            'price_type' => $r->price_type ?? 'fixed',
            'exclusive_group' => $r->exclusive_group ?? null,
            'is_default' => $r->is_default ?? false,
            'image_path' => $this->image($r->image_path ?? null),
            'is_active' => $r->is_active ?? true,
            'sort_order' => $r->sort_order ?? 0,
        ] + $this->stamps($r));
    }

    private function importCustomersAndQuotes(): void
    {
        $this->copy('customers', 'customers', fn ($r) => [
            'id' => $r->id,
            'first_name' => $r->first_name,
            'last_name' => $r->last_name,
            'email' => $r->email,
            'phone' => $r->phone ?? null,
            'company' => $r->company ?? null,
            'address' => $r->address ?? null,
            'city' => $r->city ?? null,
            'postal_code' => $r->postal_code ?? null,
            'notes' => $r->notes ?? null,
        ] + $this->stamps($r));

        $this->copy('quotes', 'quotes', fn ($r) => [
            'id' => $r->id,
            'quote_number' => $r->quote_number,
            'customer_id' => $r->customer_id,
            'event_type_id' => $r->event_type_id ?? null,
            'event_date' => $r->event_date ?? null,
            'event_time_slot' => $r->event_time_slot ?? null,
            'guest_count_adults' => $r->guest_count_adults ?? 0,
            'guest_count_children' => $r->guest_count_children ?? 0,
            'dietary_requirements' => $r->dietary_requirements ?? null,
            'special_requests' => $r->special_requests ?? null,
            'status' => match ($r->status ?? 'draft') {
                'draft', 'pending' => 'new',
                'sent', 'viewed' => 'sent',
                'rejected', 'declined' => 'declined',
                'accepted', 'expired', 'cancelled' => $r->status,
                default => 'new',
            },
            'subtotal' => $r->subtotal ?? 0,
            'tax_rate' => $r->tax_rate ?? 21,
            'tax_amount' => $r->tax_amount ?? 0,
            'total' => $r->total ?? 0,
            'deposit_amount' => $r->deposit_amount ?? 0,
            'deposit_percentage' => $r->deposit_percentage ?? 30,
            'valid_until' => $r->valid_until ?? null,
            'admin_notes' => $r->admin_notes ?? null,
            'locale' => 'fr',   // l'ancien simulateur n'existait qu'en français
            'created_at' => $r->created_at ?? now(),
            'updated_at' => $r->updated_at ?? now(),
            'deleted_at' => $r->deleted_at ?? null,
        ]);

        foreach (['quote_venues', 'quote_menus', 'quote_menu_choices', 'quote_drinks', 'quote_extras'] as $table) {
            $this->copy($table, $table, fn ($r) => (array) $r);
        }

        $this->copy('blocked_dates', 'blocked_dates', fn ($r) => (array) $r);

        $this->copy('reservations', 'reservations', fn ($r) => (array) $r);
    }

    private function importSettings(): void
    {
        if (! $this->legacy->getSchemaBuilder()->hasTable('site_settings')) {
            return;
        }

        // Anciennes clés => nouvelles (les deux orthographes ont existé).
        $map = [
            'company_name' => 'company_name', 'email' => 'email', 'company_email' => 'email',
            'phone' => 'phone', 'company_phone' => 'phone', 'address' => 'address',
            'company_address' => 'address', 'tax_rate' => 'tax_rate',
            'deposit_percentage' => 'deposit_percentage', 'quote_validity_days' => 'quote_validity_days',
        ];

        $n = 0;
        // Les clés sans préfixe « company_ » (saisies dans l'ancien admin) passent en dernier et l'emportent.
        $rows = $this->legacy->table('site_settings')->get()
            ->sortBy(fn ($r) => str_starts_with($r->key, 'company_') && $r->key !== 'company_name' ? 0 : 1);

        foreach ($rows as $r) {
            $key = $map[$r->key] ?? null;
            // Valeurs d'exemple du jeu de démo : on garde les coordonnées réelles par défaut.
            if (! $key || blank($r->value) || str_contains((string) $r->value, 'XX XXX')) {
                continue;
            }
            DB::table('event_settings')->updateOrInsert(['key' => $key], ['value' => $r->value, 'created_at' => now(), 'updated_at' => now()]);
            $n++;
        }
        $this->counts['event_settings'] = $n;
    }

    private function importUsers(): void
    {
        $n = 0;
        foreach ($this->legacy->table('users')->get() as $r) {
            if (DB::table('users')->where('email', $r->email)->exists()) {
                continue;
            }
            DB::table('users')->insert([
                'name' => $r->name,
                'email' => $r->email,
                'password' => $r->password,   // déjà haché (bcrypt), compatible
                'email_verified_at' => $r->email_verified_at ?? null,
                'created_at' => $r->created_at ?? now(),
                'updated_at' => now(),
            ]);
            $n++;
        }
        $this->counts['users'] = $n;
    }

    /* ------------------------------------------------------------------ */

    /** Copie une table ligne à ligne via $map (ignorée si absente de l'ancienne base). */
    private function copy(string $from, string $to, callable $map): void
    {
        if (! $this->legacy->getSchemaBuilder()->hasTable($from)) {
            $this->counts[$to] = 0;

            return;
        }

        $columns = Schema::getColumnListing($to);
        $n = 0;

        $this->legacy->table($from)->orderBy('id')->chunk(200, function ($rows) use ($to, $map, $columns, &$n) {
            $batch = [];
            foreach ($rows as $row) {
                // Ne garde que les colonnes existant dans la table cible.
                $batch[] = array_intersect_key($map($row), array_flip($columns));
            }
            DB::table($to)->insert($batch);
            $n += count($batch);
        });

        $this->counts[$to] = $n;
    }

    /** Texte existant => {"fr": "..."} (les autres langues sont à traduire dans l'admin). */
    private function fr(?string $text): ?string
    {
        return $text === null || $text === '' ? null : json_encode(['fr' => $text], JSON_UNESCAPED_UNICODE);
    }

    private function stamps(object $r): array
    {
        return ['created_at' => $r->created_at ?? now(), 'updated_at' => $r->updated_at ?? now()];
    }

    /**
     * Recopie une image dans storage/app/public/evenements et renvoie son
     * nouveau chemin. Cherche d'abord dans public/ puis dans storage/app/public.
     */
    private function image(?string $path): ?string
    {
        if (! $path) {
            return null;
        }
        $path = ltrim($path, '/');
        $relative = preg_replace('#^(storage/|images/)#', '', $path);
        $target = 'evenements/'.$relative;

        if (Storage::disk('public')->exists($target)) {
            return $target;
        }

        foreach ([$this->option('images'), $this->option('storage')] as $root) {
            if (! $root) {
                continue;
            }
            foreach ([$path, $relative, 'images/'.$relative] as $candidate) {
                $source = rtrim($root, '/').'/'.$candidate;
                if (is_file($source)) {
                    Storage::disk('public')->put($target, file_get_contents($source));

                    return $target;
                }
            }
        }

        $this->missingImages[] = $path;

        return $path;
    }
}
