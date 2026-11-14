<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'identifier',
        'start_date',
        'end_date',
        'notes',
        'status',
        'total_price',
        'payment_status',
        'paid_amount',
        'payment_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_price' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'pending' => 'Pagamento Pendente',
            'signal' => 'Sinal Pago (50%)',
            'paid' => 'Totalmente Pago',
            'other' => 'Outro Valor',
            default => 'Pagamento Pendente',
        };
    }

    public function getPaymentRemainingAttribute(): float
    {
        $remaining = (float)($this->total_price ?? 0) - (float)($this->paid_amount ?? 0);
        return max(0, $remaining);
    }

    /**
     * Encerra automaticamente as reservas ativas cuja data de fim já passou.
     */
    public static function autoCloseExpired(): int
    {
        return static::where('status', 'active')
            ->where('end_date', '<', now()->toDateString())
            ->update(['status' => 'completed']);
    }

    protected static function booted()
    {
        static::retrieved(function (Reservation $reservation) {
            if ($reservation->status === 'active' && $reservation->end_date && $reservation->end_date->toDateString() < now()->toDateString()) {
                $reservation->status = 'completed';
                $reservation->saveQuietly();
            }
        });
    }

    public function items()
    {
        return $this->hasMany(ReservationItem::class);
    }
}
