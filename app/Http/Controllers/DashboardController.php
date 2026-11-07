<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        Reservation::autoCloseExpired();

        $reservations = Reservation::with('items.product')->where('status', 'active')->get();
        
        $events = [];
        foreach ($reservations as $res) {
            $title = 'R#' . $res->id;
            if ($res->identifier) {
                $title .= ' (' . $res->identifier . ')';
            }
            
            // Calculando a quantidade de itens totais
            $totalItems = $res->items->sum('quantity');
            $title .= " - $totalItems peças";

            $events[] = [
                'id' => $res->id,
                'title' => $title,
                'start' => $res->start_date->format('Y-m-d'),
                'end' => $res->end_date->format('Y-m-d'), // FullCalendar end date is exclusive, which matches our logic perfectly.
                'url' => route('reservations.show', $res->id),
                'backgroundColor' => '#2298b6',
                'borderColor' => '#2890a8',
                'textColor' => '#ffffff',
            ];
        }

        $itemsInUse = Reservation::where('status', 'active')
            ->where('start_date', '<=', now()->toDateString())
            ->where('end_date', '>', now()->toDateString())
            ->get()
            ->sum(function($res) {
                return $res->items->sum('quantity');
            });

        return view('dashboard', [
            'events' => $events,
            'stats' => [
                'active_reservations' => $reservations->count(),
                'items_in_use' => $itemsInUse,
            ]
        ]);
    }
}
