<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeviceOrder extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_COMPLETED = 'completed';
    public const TYPE_INITIAL = 'initial';
    public const TYPE_REFILL = 'refill';

    protected $fillable = [
        'device_id',
        'parent_order_id',
        'table_id',
        'table_name',
        'session_key',
        'type',
        'status',
        'pos_order_reference',
        'guest_count',
        'subtotal_cents',
        'tax_cents',
        'total_cents',
        'metadata',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'submitted_at' => 'immutable_datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function parentOrder(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_order_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DeviceOrderItem::class);
    }

    public function printEvents(): HasMany
    {
        return $this->hasMany(PrintEvent::class);
    }
}
