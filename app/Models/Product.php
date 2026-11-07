<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'category',
        'size',
        'total_quantity',
        'min_stock',
        'price',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_quantity' => 'integer',
        'min_stock' => 'integer',
        'price' => 'decimal:2',
    ];

    public function reservationItems()
    {
        return $this->hasMany(ReservationItem::class);
    }

    public function kitItems()
    {
        return $this->hasMany(KitItem::class);
    }
}
