<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_order_id',
        'menu_id',
        'name',
        'quantity',
        'unit_price_cents',
        'line_total_cents',
        'modifiers',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'modifiers' => 'array',
            'metadata' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(DeviceOrder::class, 'device_order_id');
    }
}
