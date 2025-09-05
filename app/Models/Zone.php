<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = [
        'floor_id',
        'zone_number',
        'name',
        'code',
        'capacity',
        'current_usage',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    public function room()
    {
        return $this->hasOneThrough(Room::class, Floor::class, 'id', 'id', 'floor_id', 'room_id');
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    /**
     * Get available capacity
     */
    public function getAvailableCapacity()
    {
        return $this->capacity - $this->current_usage;
    }

    /**
     * Get capacity percentage
     */
    public function getCapacityPercentage()
    {
        return ($this->current_usage / $this->capacity) * 100;
    }

    /**
     * Check if zone has capacity for given basket count
     */
    public function hasCapacity($basketCount)
    {
        return $this->getAvailableCapacity() >= $basketCount;
    }

    /**
     * Update usage when batch is added/removed
     */
    public function updateUsage($basketCount, $operation = 'add')
    {
        if ($operation === 'add') {
            $this->current_usage += $basketCount;
        } else {
            $this->current_usage = max(0, $this->current_usage - $basketCount);
        }
        $this->save();
    }

    /**
     * Get full location string
     */
    public function getFullLocation()
    {
        return $this->room->name . ' - ' . $this->floor->name . ' - ' . $this->name;
    }
}
