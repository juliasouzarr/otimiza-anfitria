<?php

namespace Database\Seeders;

use App\Models\Kit;
use App\Models\KitItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class KitSeeder extends Seeder
{
    public function run(): void
    {
        // Produtos de referência
        $fronha = Product::where('name', 'like', '%Fronha%')->first();
        $lencolSoltElast = Product::where('name', 'like', '%Lençol com elástico – solteiro%')->first();
        $lencolSoltSemElast = Product::where('name', 'like', '%Lençol sem elástico – solteiro%')->first();

        $lencolCasalElast = Product::where('name', 'like', '%Lençol com elástico – casal%')->first();
        $lencolCasalSemElast = Product::where('name', 'like', '%Lençol sem elástico – casal%')->first();

        $lencolQueenElast = Product::where('name', 'like', '%Lençol com elástico – Queen%')->first();
        $lencolQueenSemElast = Product::where('name', 'like', '%Lençol sem elástico – Queen%')->first();

        $lencolKingElast = Product::where('name', 'like', '%Lençol com elástico – King%')->first();
        $lencolKingSemElast = Product::where('name', 'like', '%Lençol sem elástico – King%')->first();

        $toalhaBanho = Product::where('name', 'like', '%Toalha de banho%')->first();
        $toalhaRosto = Product::where('name', 'like', '%Toalha de rosto%')->first();
        $toalhaPiso = Product::where('name', 'like', '%Toalha de piso%')->first();

        // 1. Kit Solteiro – 5 peças (R$ 65,05 - 3 dias)
        $kitSolteiro = Kit::updateOrCreate(
            ['name' => 'Kit Solteiro – 5 peças'],
            [
                'size' => 'Solteiro',
                'pieces_count' => 5,
                'period_days' => 3,
                'price' => 65.05,
                'description' => 'Contém 01 fronha com abas, 01 lençol com elástico, 01 lençol sem elástico, 01 toalha de banho e 01 toalha de rosto. Período: 3 dias.',
                'is_active' => true,
            ]
        );
        $kitSolteiro->items()->delete();
        if ($fronha) KitItem::create(['kit_id' => $kitSolteiro->id, 'product_id' => $fronha->id, 'quantity' => 1, 'unit_price' => 4.95]);
        if ($lencolSoltElast) KitItem::create(['kit_id' => $kitSolteiro->id, 'product_id' => $lencolSoltElast->id, 'quantity' => 1, 'unit_price' => 18.00]);
        if ($lencolSoltSemElast) KitItem::create(['kit_id' => $kitSolteiro->id, 'product_id' => $lencolSoltSemElast->id, 'quantity' => 1, 'unit_price' => 19.00]);
        if ($toalhaBanho) KitItem::create(['kit_id' => $kitSolteiro->id, 'product_id' => $toalhaBanho->id, 'quantity' => 1, 'unit_price' => 16.95]);
        if ($toalhaRosto) KitItem::create(['kit_id' => $kitSolteiro->id, 'product_id' => $toalhaRosto->id, 'quantity' => 1, 'unit_price' => 6.15]);

        // 2. Kit Casal – 9 peças (R$ 113,66 - 3 dias)
        $kitCasal = Kit::updateOrCreate(
            ['name' => 'Kit Casal – 9 peças'],
            [
                'size' => 'Casal',
                'pieces_count' => 9,
                'period_days' => 3,
                'price' => 113.66,
                'description' => 'Contém 02 fronhas com abas, 01 lençol com elástico casal, 01 lençol sem elástico casal, 02 toalhas de banho, 02 toalhas de rosto e 01 toalha de piso. Período: 3 dias.',
                'is_active' => true,
            ]
        );
        $kitCasal->items()->delete();
        if ($fronha) KitItem::create(['kit_id' => $kitCasal->id, 'product_id' => $fronha->id, 'quantity' => 2, 'unit_price' => 4.95]);
        if ($lencolCasalElast) KitItem::create(['kit_id' => $kitCasal->id, 'product_id' => $lencolCasalElast->id, 'quantity' => 1, 'unit_price' => 24.00]);
        if ($lencolCasalSemElast) KitItem::create(['kit_id' => $kitCasal->id, 'product_id' => $lencolCasalSemElast->id, 'quantity' => 1, 'unit_price' => 26.00]);
        if ($toalhaBanho) KitItem::create(['kit_id' => $kitCasal->id, 'product_id' => $toalhaBanho->id, 'quantity' => 2, 'unit_price' => 16.95]);
        if ($toalhaRosto) KitItem::create(['kit_id' => $kitCasal->id, 'product_id' => $toalhaRosto->id, 'quantity' => 2, 'unit_price' => 6.30]);
        if ($toalhaPiso) KitItem::create(['kit_id' => $kitCasal->id, 'product_id' => $toalhaPiso->id, 'quantity' => 1, 'unit_price' => 7.26]);

        // 3. Kit Queen – 9 peças (R$ 128,06 - 3 dias)
        $kitQueen = Kit::updateOrCreate(
            ['name' => 'Kit Queen – 9 peças'],
            [
                'size' => 'Queen',
                'pieces_count' => 9,
                'period_days' => 3,
                'price' => 128.06,
                'description' => 'Contém 02 fronhas com abas, 01 lençol com elástico Queen, 01 lençol sem elástico Queen, 02 toalhas de banho, 02 toalhas de rosto e 01 toalha de piso. Período: 3 dias.',
                'is_active' => true,
            ]
        );
        $kitQueen->items()->delete();
        if ($fronha) KitItem::create(['kit_id' => $kitQueen->id, 'product_id' => $fronha->id, 'quantity' => 2, 'unit_price' => 4.95]);
        if ($lencolQueenElast) KitItem::create(['kit_id' => $kitQueen->id, 'product_id' => $lencolQueenElast->id, 'quantity' => 1, 'unit_price' => 29.90]);
        if ($lencolQueenSemElast) KitItem::create(['kit_id' => $kitQueen->id, 'product_id' => $lencolQueenSemElast->id, 'quantity' => 1, 'unit_price' => 34.50]);
        if ($toalhaBanho) KitItem::create(['kit_id' => $kitQueen->id, 'product_id' => $toalhaBanho->id, 'quantity' => 2, 'unit_price' => 16.95]);
        if ($toalhaRosto) KitItem::create(['kit_id' => $kitQueen->id, 'product_id' => $toalhaRosto->id, 'quantity' => 2, 'unit_price' => 6.30]);
        if ($toalhaPiso) KitItem::create(['kit_id' => $kitQueen->id, 'product_id' => $toalhaPiso->id, 'quantity' => 1, 'unit_price' => 7.26]);

        // 4. Kit King – 9 peças (R$ 134,96 - 3 dias)
        $kitKing = Kit::updateOrCreate(
            ['name' => 'Kit King – 9 peças'],
            [
                'size' => 'King',
                'pieces_count' => 9,
                'period_days' => 3,
                'price' => 134.96,
                'description' => 'Contém 02 fronhas com abas, 01 lençol com elástico King, 01 lençol sem elástico King, 02 toalhas de banho, 02 toalhas de rosto e 01 toalha de piso. Período: 3 dias.',
                'is_active' => true,
            ]
        );
        $kitKing->items()->delete();
        if ($fronha) KitItem::create(['kit_id' => $kitKing->id, 'product_id' => $fronha->id, 'quantity' => 2, 'unit_price' => 4.95]);
        if ($lencolKingElast) KitItem::create(['kit_id' => $kitKing->id, 'product_id' => $lencolKingElast->id, 'quantity' => 1, 'unit_price' => 33.90]);
        if ($lencolKingSemElast) KitItem::create(['kit_id' => $kitKing->id, 'product_id' => $lencolKingSemElast->id, 'quantity' => 1, 'unit_price' => 37.80]);
        if ($toalhaBanho) KitItem::create(['kit_id' => $kitKing->id, 'product_id' => $toalhaBanho->id, 'quantity' => 2, 'unit_price' => 16.95]);
        if ($toalhaRosto) KitItem::create(['kit_id' => $kitKing->id, 'product_id' => $toalhaRosto->id, 'quantity' => 2, 'unit_price' => 6.30]);
        if ($toalhaPiso) KitItem::create(['kit_id' => $kitKing->id, 'product_id' => $toalhaPiso->id, 'quantity' => 1, 'unit_price' => 7.26]);
    }
}

