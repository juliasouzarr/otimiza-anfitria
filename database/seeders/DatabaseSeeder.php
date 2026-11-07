<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@otimizaanfitria.com'],
            [
                'name' => 'Administradora',
                'password' => bcrypt('Leona2107$'),
            ]
        );

        $this->call([
            ProductSeeder::class,
            KitSeeder::class,
        ]);
    }
}
