<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Reservation;
use Illuminate\Support\Carbon;

class AvailabilityService
{
    /**
     * Calcula a quantidade disponível de um produto em um determinado período
     */
    public function getAvailableQuantity(int $productId, $startDate, $endDate, $excludeReservationId = null): int
    {
        Reservation::autoCloseExpired();

        $product = Product::findOrFail($productId);
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Busca reservas ativas que sobrepõem o período solicitado
        // Uma reserva sobrepõe se: começa ANTES do período terminar E termina DEPOIS do período começar
        $query = Reservation::where('status', 'active')
            ->where('start_date', '<', $end->toDateString())
            ->where('end_date', '>', $start->toDateString())
            ->with(['items' => function($q) use ($productId) {
                $q->where('product_id', $productId);
            }]);

        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        $reservations = $query->get();

        if ($reservations->isEmpty()) {
            return $product->total_quantity;
        }

        $maxUsed = 0;
        $currentDate = $start->copy();
        
        // Se start_date == end_date (ex: locação pro mesmo dia devolvendo mesmo dia à tarde)
        // o diffInDays é 0. Nesse caso iteramos pelo menos o dia atual.
        $daysToIterate = $start->diffInDays($end) > 0 ? $start->diffInDays($end) : 1;

        for ($i = 0; $i < $daysToIterate; $i++) {
            $checkDate = $currentDate->toDateString();
            $usedToday = 0;

            foreach ($reservations as $reservation) {
                // Se a reserva está ativa neste dia específico (data_fim é devolução, não conta como uso neste dia)
                if ($reservation->start_date->toDateString() <= $checkDate && $reservation->end_date->toDateString() > $checkDate) {
                    $item = $reservation->items->first();
                    if ($item) {
                        $usedToday += $item->quantity;
                    }
                }
            }

            if ($usedToday > $maxUsed) {
                $maxUsed = $usedToday;
            }
            
            $currentDate->addDay();
        }

        $available = $product->total_quantity - $maxUsed;
        return $available > 0 ? $available : 0;
    }
}
