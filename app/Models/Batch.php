<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'room_id',
        'floor_id',
        'zone_id',
        'unit_price',
        'total_baskets',
        'total_weight',
        'total_value',
        'expiry_date',
        'can_dispatch',
        'status',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_weight' => 'decimal:2',
        'total_value' => 'decimal:2',
        'expiry_date' => 'date',
        'can_dispatch' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function baskets()
    {
        return $this->hasMany(Basket::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * Get full storage location string
     */
    public function getStorageLocation()
    {
        if (!$this->room || !$this->floor || !$this->zone) {
            return 'Not Assigned';
        }
        return $this->room->name . ' - ' . $this->floor->name . ' - ' . $this->zone->name;
    }

    /**
     * Check if batch is expiring soon (within 30 days)
     */
    public function isExpiringSoon($days = 30)
    {
        return $this->expiry_date <= now()->addDays($days);
    }

    /**
     * Check if batch has expired
     */
    public function isExpired()
    {
        return $this->expiry_date < now();
    }

    /**
     * Get days until expiry
     */
    public function getDaysUntilExpiry()
    {
        return now()->diffInDays($this->expiry_date, false);
    }

    /**
     * Get expiry status
     */
    public function getExpiryStatus()
    {
        if ($this->isExpired()) {
            return 'expired';
        } elseif ($this->isExpiringSoon()) {
            return 'expiring_soon';
        }
        return 'active';
    }

    /**
     * Scope for expiring batches
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                    ->where('expiry_date', '>', now());
    }

    /**
     * Scope for expired batches
     */
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }
}


