<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DispatchRecord extends Model
{
    protected $fillable = [
        'dispatch_number',
        'basket_id',
        'batch_id',
        'customer_id',
        'barcode',
        'unit_price',
        'total_amount',
        'status',
        'approval_status',
        'approved_by',
        'approved_at',
        'dispatched_at',
        'dispatch_notes',
        'admin_notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'dispatched_at' => 'datetime',
    ];

    // Relationships
    public function basket(): BelongsTo
    {
        return $this->belongsTo(Basket::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approval(): HasOne
    {
        return $this->hasOne(DispatchApproval::class);
    }

    // Generate unique dispatch number
    public static function generateDispatchNumber(): string
    {
        do {
            $number = 'DISP-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('dispatch_number', $number)->exists());

        return $number;
    }

    // Check if dispatch is allowed (invoice paid or admin approved)
    public function canDispatch(): bool
    {
        return $this->status === 'approved' &&
               ($this->approval_status === 'auto_approved' || $this->approval_status === 'admin_approved');
    }
}
