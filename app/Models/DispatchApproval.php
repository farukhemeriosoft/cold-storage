<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispatchApproval extends Model
{
    protected $fillable = [
        'dispatch_record_id',
        'admin_id',
        'status',
        'admin_notes',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function dispatchRecord(): BelongsTo
    {
        return $this->belongsTo(DispatchRecord::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
