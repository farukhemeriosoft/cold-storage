<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Basket extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'batch_id',
        'customer_id',
        'barcode',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // No casts needed without weight
    ];

    /**
     * Get the customer that owns the basket.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Scope a query to only include active baskets.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    /**
     * Scope a query to only include inactive baskets.
     */
    public function scopeDispatched($query)
    {
        return $query->where('status', 'dispatched');
    }
}
