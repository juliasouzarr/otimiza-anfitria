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
        User::factory()->create([
            'name' => 'Administradora',
            'email' => 'admin@otimizaanfitria.com',
            'password' => bcrypt('Leona2107$'),
        ]);

        $this->call([
            ProductSeeder::class,
            KitSeeder::class,
        ]);
    }
}
