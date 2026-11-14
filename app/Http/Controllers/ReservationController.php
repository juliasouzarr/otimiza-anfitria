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
            'payment_status' => 'nullable|in:pending,signal,paid,other',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
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
            $totalPrice = 0;
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $unitPrice = isset($item['unit_price']) && is_numeric($item['unit_price'])
                    ? (float)$item['unit_price']
                    : ($product ? (float)$product->price : 0);

                $totalPrice += ($unitPrice * (int)$item['quantity']);
            }

            $paymentStatus = $request->input('payment_status', 'pending');
            $paidAmount = 0.00;
            if ($paymentStatus === 'signal') {
                $paidAmount = round($totalPrice * 0.5, 2);
            } elseif ($paymentStatus === 'paid') {
                $paidAmount = $totalPrice;
            } elseif ($paymentStatus === 'other') {
                $paidAmount = (float)$request->input('paid_amount', 0);
            }

            $paymentDate = ($paymentStatus !== 'pending' && $request->filled('payment_date'))
                ? $request->payment_date
                : null;

            if ($paymentStatus === 'pending') {
                $paidAmount = 0.00;
            }

            $reservation = Reservation::create([
                'identifier' => $request->identifier,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'notes' => $request->notes,
                'status' => 'active',
                'total_price' => $totalPrice,
                'payment_status' => $paymentStatus,
                'paid_amount' => $paidAmount,
                'payment_date' => $paymentDate,
            ]);

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
            'payment_status' => 'nullable|in:pending,signal,paid,other',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
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

            $paymentStatus = $request->input('payment_status', 'pending');
            $paidAmount = 0.00;
            if ($paymentStatus === 'signal') {
                $paidAmount = round($totalPrice * 0.5, 2);
            } elseif ($paymentStatus === 'paid') {
                $paidAmount = $totalPrice;
            } elseif ($paymentStatus === 'other') {
                $paidAmount = (float)$request->input('paid_amount', 0);
            }

            $paymentDate = ($paymentStatus !== 'pending' && $request->filled('payment_date'))
                ? $request->payment_date
                : null;

            if ($paymentStatus === 'pending') {
                $paidAmount = 0.00;
            }

            $reservation->update([
                'identifier' => $request->identifier,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'notes' => $request->notes,
                'status' => $request->status,
                'total_price' => $totalPrice,
                'payment_status' => $paymentStatus,
                'paid_amount' => $paidAmount,
                'payment_date' => $paymentDate,
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

    /**
     * Atualização rápida apenas do status de pagamento, valor e data.
     */
    public function updatePayment(Request $request, Reservation $reservation)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,signal,paid,other',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
        ]);

        $paymentStatus = $request->payment_status;
        $totalPrice = (float)($reservation->total_price ?? 0);
        $paidAmount = 0.00;

        if ($paymentStatus === 'signal') {
            $paidAmount = round($totalPrice * 0.5, 2);
        } elseif ($paymentStatus === 'paid') {
            $paidAmount = $totalPrice;
        } elseif ($paymentStatus === 'other') {
            $paidAmount = (float)($request->paid_amount ?? 0);
        }

        $paymentDate = ($paymentStatus !== 'pending' && $request->filled('payment_date'))
            ? $request->payment_date
            : null;

        if ($paymentStatus === 'pending') {
            $paidAmount = 0.00;
        }

        $reservation->update([
            'payment_status' => $paymentStatus,
            'paid_amount' => $paidAmount,
            'payment_date' => $paymentDate,
        ]);

        return back()->with('success', "Pagamento da reserva #{$reservation->id} atualizado com sucesso!");
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->update(['status' => 'cancelled']);
        return redirect()->route('reservations.index')->with('success', 'Reserva cancelada com sucesso!');
    }
}
