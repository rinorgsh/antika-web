<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Comptes admin (accès au back-office /admin)
        User::updateOrCreate(
            ['email' => 'rinorgsh123@gmail.com'],
            ['name' => 'Rinor', 'password' => Hash::make('antika2026')],
        );
        User::updateOrCreate(
            ['email' => 'responsable@antika-resto.ovh'],
            ['name' => 'Responsable Antika', 'password' => Hash::make('antika2026')],
        );

        $this->call(MenuSeeder::class);
    }
}
