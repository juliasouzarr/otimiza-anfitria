<?php

namespace App\Http\Controllers;

use App\Models\Kit;
use App\Models\KitItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KitController extends Controller
{
    public function index()
    {
        $kits = Kit::with('items.product')->orderBy('name')->get();
        return view('kits.index', compact('kits'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('kits.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'size' => 'nullable|string|max:255',
            'period_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $totalPieces = 0;
            foreach ($request->items as $item) {
                $totalPieces += (int)$item['quantity'];
            }

            $kit = Kit::create([
                'name' => $request->name,
                'size' => $request->size,
                'period_days' => $request->period_days,
                'price' => $request->price,
                'pieces_count' => $totalPieces,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ]);

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $unitPrice = !empty($item['unit_price']) ? $item['unit_price'] : ($product ? $product->price : 0);

                KitItem::create([
                    'kit_id' => $kit->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                ]);
            }
        });

        return redirect()->route('kits.index')->with('success', 'Kit de enxovais cadastrado com sucesso!');
    }

    public function show(Kit $kit)
    {
        return redirect()->route('kits.edit', $kit);
    }

    public function edit(Kit $kit)
    {
        $kit->load('items.product');
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('kits.edit', compact('kit', 'products'));
    }

    public function update(Request $request, Kit $kit)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'size' => 'nullable|string|max:255',
            'period_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $kit) {
            $totalPieces = 0;
            foreach ($request->items as $item) {
                $totalPieces += (int)$item['quantity'];
            }

            $kit->update([
                'name' => $request->name,
                'size' => $request->size,
                'period_days' => $request->period_days,
                'price' => $request->price,
                'pieces_count' => $totalPieces,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ]);

            $kit->items()->delete();

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $unitPrice = !empty($item['unit_price']) ? $item['unit_price'] : ($product ? $product->price : 0);

                KitItem::create([
                    'kit_id' => $kit->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                ]);
            }
        });

        return redirect()->route('kits.index')->with('success', 'Kit de enxovais atualizado com sucesso!');
    }

    public function destroy(Kit $kit)
    {
        $kit->update(['is_active' => !$kit->is_active]);
        $status = $kit->is_active ? 'ativado' : 'inativado';
        return redirect()->route('kits.index')->with('success', "Kit {$status} com sucesso!");
    }
}

