<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitItem extends Model
{
    protected $fillable = [
        'kit_id',
        'product_id',
        'quantity',
        'unit_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
    ];

    public function kit()
    {
        return $this->belongsTo(Kit::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

