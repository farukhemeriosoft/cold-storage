<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class BasketHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'basket_id',
        'customer_id',
        'batch_id',
        'barcode',
        'unit_price',
        'dispatched_at',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'dispatched_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function basket()
    {
        return $this->belongsTo(Basket::class);
    }
}


