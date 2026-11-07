<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kit extends Model
{
    protected $fillable = [
        'name',
        'size',
        'pieces_count',
        'period_days',
        'price',
        'description',
        'is_active',
    ];

    protected $casts = [
        'pieces_count' => 'integer',
        'period_days' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(KitItem::class);
    }
}

