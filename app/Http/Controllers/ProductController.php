<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request, \App\Services\AvailabilityService $availabilityService)
    {
        $products = Product::orderBy('name')->get();
        
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        if ($startDate && $endDate) {
            foreach ($products as $product) {
                $product->available_quantity = $availabilityService->getAvailableQuantity($product->id, $startDate, $endDate);
            }
        }

        return view('products.index', compact('products', 'startDate', 'endDate'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'total_quantity' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Enxoval cadastrado com sucesso!');
    }

    public function show(Product $product)
    {
        return redirect()->route('products.edit', $product);
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'total_quantity' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Enxoval atualizado com sucesso!');
    }

    public function destroy(Product $product)
    {
        $product->update(['is_active' => false]);
        return redirect()->route('products.index')->with('success', 'Enxoval inativado com sucesso!');
    }
}
