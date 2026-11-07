<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Fronha com abas',
                'category' => 'Cama',
                'size' => 'Único',
                'price' => 4.95,
                'total_quantity' => 20,
                'min_stock' => 5,
            ],
            [
                'name' => 'Lençol com elástico – solteiro',
                'category' => 'Cama',
                'size' => 'Solteiro',
                'price' => 18.00,
                'total_quantity' => 10,
                'min_stock' => 2,
            ],
            [
                'name' => 'Lençol sem elástico – solteiro',
                'category' => 'Cama',
                'size' => 'Solteiro',
                'price' => 19.00,
                'total_quantity' => 10,
                'min_stock' => 2,
            ],
            [
                'name' => 'Lençol com elástico – casal',
                'category' => 'Cama',
                'size' => 'Casal',
                'price' => 24.00,
                'total_quantity' => 10,
                'min_stock' => 2,
            ],
            [
                'name' => 'Lençol sem elástico – casal',
                'category' => 'Cama',
                'size' => 'Casal',
                'price' => 26.00,
                'total_quantity' => 10,
                'min_stock' => 2,
            ],
            [
                'name' => 'Lençol com elástico – Queen',
                'category' => 'Cama',
                'size' => 'Queen',
                'price' => 29.90,
                'total_quantity' => 10,
                'min_stock' => 2,
            ],
            [
                'name' => 'Lençol sem elástico – Queen',
                'category' => 'Cama',
                'size' => 'Queen',
                'price' => 34.50,
                'total_quantity' => 10,
                'min_stock' => 2,
            ],
            [
                'name' => 'Lençol com elástico – King',
                'category' => 'Cama',
                'size' => 'King',
                'price' => 33.90,
                'total_quantity' => 10,
                'min_stock' => 2,
            ],
            [
                'name' => 'Lençol sem elástico – King',
                'category' => 'Cama',
                'size' => 'King',
                'price' => 37.80,
                'total_quantity' => 10,
                'min_stock' => 2,
            ],
            [
                'name' => 'Toalha de banho',
                'category' => 'Banho',
                'size' => 'Banho',
                'price' => 16.95,
                'total_quantity' => 10,
                'min_stock' => 3,
            ],
            [
                'name' => 'Toalha de rosto',
                'category' => 'Banho',
                'size' => 'Rosto',
                'price' => 6.15,
                'total_quantity' => 10,
                'min_stock' => 3,
            ],
            [
                'name' => 'Toalha de piso',
                'category' => 'Banho',
                'size' => 'Piso',
                'price' => 7.26,
                'total_quantity' => 5,
                'min_stock' => 2,
            ],
        ];

        foreach ($products as $data) {
            Product::updateOrCreate(
                ['name' => $data['name']],
                $data
            );
        }
    }
}
