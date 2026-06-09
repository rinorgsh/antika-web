<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Site vitrine Antika
|--------------------------------------------------------------------------
| Pages publiques. La réservation et la commande à emporter pointent vers
| des plateformes externes (ZenChef / Takeaway), gérées via config/antika.php.
*/

Route::get('/', fn () => Inertia::render('Home'))->name('home');
Route::get('/menu', fn () => Inertia::render('Menu'))->name('menu');
Route::get('/events', fn () => Inertia::render('Events'))->name('events');
Route::get('/contact', fn () => Inertia::render('Contact'))->name('contact');

// Changement de langue : on mémorise le choix en session puis on revient en arrière.
Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, SetLocale::SUPPORTED, true)) {
        session(['locale' => $locale]);
    }

    return back();
})->name('locale.switch');
