<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Kit;
use App\Models\ReservationItem;
use App\Services\AvailabilityService;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    protected AvailabilityService $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    public function index()
    {
        Reservation::autoCloseExpired();

        $reservations = Reservation::with('items.product')->orderBy('start_date', 'desc')->get();
        return view('reservations.index', compact('reservations'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $kits = Kit::with('items.product')->where('is_active', true)->orderBy('name')->get();
        return view('reservations.create', compact('products', 'kits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'identifier' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        foreach ($request->items as $item) {
            $available = $this->availabilityService->getAvailableQuantity(
                $item['product_id'],
                $request->start_date,
                $request->end_date
            );

            if ($item['quantity'] > $available) {
                $product = Product::find($item['product_id']);
                return back()->withInput()->withErrors([
                    'availability' => "Estoque insuficiente para {$product->name}. Solicitado: {$item['quantity']}, Disponível: {$available} no período selecionado."
                ]);
            }
        }

        DB::transaction(function () use ($request) {
            $reservation = Reservation::create([
                'identifier' => $request->identifier,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'notes' => $request->notes,
                'status' => 'active',
                'total_price' => 0,
            ]);

            $totalPrice = 0;
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $unitPrice = isset($item['unit_price']) && is_numeric($item['unit_price'])
                    ? (float)$item['unit_price']
                    : ($product ? (float)$product->price : 0);

                $totalPrice += ($unitPrice * (int)$item['quantity']);

                ReservationItem::create([
                    'reservation_id' => $reservation->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                ]);
            }

            $reservation->update(['total_price' => $totalPrice]);
        });

        return redirect()->route('reservations.index')->with('success', 'Reserva criada com sucesso!');
    }

    public function show(Reservation $reservation)
    {
        $reservation->load('items.product');
        return view('reservations.show', compact('reservation'));
    }

    public function print(Reservation $reservation)
    {
        $reservation->load('items.product');
        return view('reservations.print', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        $reservation->load('items.product');
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $kits = Kit::with('items.product')->where('is_active', true)->orderBy('name')->get();
        return view('reservations.edit', compact('reservation', 'products', 'kits'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $request->validate([
            'identifier' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,cancelled,completed',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        if ($request->status === 'active') {
            foreach ($request->items as $item) {
                $available = $this->availabilityService->getAvailableQuantity(
                    $item['product_id'],
                    $request->start_date,
                    $request->end_date,
                    $reservation->id
                );

                if ($item['quantity'] > $available) {
                    $product = Product::find($item['product_id']);
                    return back()->withInput()->withErrors([
                        'availability' => "Estoque insuficiente para {$product->name}. Solicitado: {$item['quantity']}, Disponível: {$available} no período selecionado."
                    ]);
                }
            }
        }

        DB::transaction(function () use ($request, $reservation) {
            $totalPrice = 0;
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $unitPrice = isset($item['unit_price']) && is_numeric($item['unit_price'])
                    ? (float)$item['unit_price']
                    : ($product ? (float)$product->price : 0);

                $totalPrice += ($unitPrice * (int)$item['quantity']);
            }

            $reservation->update([
                'identifier' => $request->identifier,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'notes' => $request->notes,
                'status' => $request->status,
                'total_price' => $totalPrice,
            ]);

            $reservation->items()->delete();

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $unitPrice = isset($item['unit_price']) && is_numeric($item['unit_price'])
                    ? (float)$item['unit_price']
                    : ($product ? (float)$product->price : 0);

                ReservationItem::create([
                    'reservation_id' => $reservation->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                ]);
            }
        });

        return redirect()->route('reservations.index')->with('success', 'Reserva atualizada com sucesso!');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->update(['status' => 'cancelled']);
        return redirect()->route('reservations.index')->with('success', 'Reserva cancelada com sucesso!');
    }
}
