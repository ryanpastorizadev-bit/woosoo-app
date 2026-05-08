<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintEvent extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACKNOWLEDGED = 'acknowledged';

    protected $fillable = [
        'device_order_id',
        'status',
        'target',
        'payload',
        'acknowledged_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'acknowledged_at' => 'immutable_datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(DeviceOrder::class, 'device_order_id');
    }
}
