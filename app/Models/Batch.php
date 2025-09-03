<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'unit_price',
        'total_baskets',
        'total_weight',
        'total_value',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_weight' => 'decimal:2',
        'total_value' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function baskets()
    {
        return $this->hasMany(Basket::class);
    }
}


