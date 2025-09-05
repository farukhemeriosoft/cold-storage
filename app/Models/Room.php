<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
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

    public function floors()
    {
        return $this->hasMany(Floor::class);
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
     * Check if room has capacity for given basket count
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
}
