<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Journalise les requêtes lentes, avec leur durée et le nombre de requêtes SQL.
 * Posé pour diagnostiquer les enregistrements lents dans le back-office :
 * sans mesure, on ne peut que supposer. À retirer une fois la cause trouvée.
 */
class LogSlowRequests
{
    /** Seuil en secondes au-delà duquel on journalise. */
    private const SEUIL = 1.5;

    public function handle(Request $request, Closure $next)
    {
        $debut = microtime(true);
        \Illuminate\Support\Facades\DB::enableQueryLog();

        $response = $next($request);

        $duree = microtime(true) - $debut;
        if ($duree >= self::SEUIL) {
            $requetes = \Illuminate\Support\Facades\DB::getQueryLog();
            $sql = round(array_sum(array_column($requetes, 'time')) / 1000, 3);

            Log::warning('Requête lente', [
                'duree' => round($duree, 2).'s',
                'uri' => $request->method().' '.$request->path(),
                'livewire' => $request->header('X-Livewire') ? 'oui' : 'non',
                'sql' => count($requetes).' requêtes en '.$sql.'s',
                'memoire' => round(memory_get_peak_usage(true) / 1048576).' Mo',
            ]);
        }

        return $response;
    }
}
