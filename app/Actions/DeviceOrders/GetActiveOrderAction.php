<?php

namespace App\Actions\DeviceOrders;

use App\Models\Device;
use App\Models\DeviceOrder;

class GetActiveOrderAction
{
    public function execute(Device $device, ?string $sessionKey = null): ?DeviceOrder
    {
        return DeviceOrder::query()
            ->with(['items', 'printEvents'])
            ->where('device_id', $device->id)
            ->when($sessionKey, fn ($query) => $query->where('session_key', $sessionKey))
            ->whereIn('status', [DeviceOrder::STATUS_ACTIVE, DeviceOrder::STATUS_SUBMITTED])
            ->latest('id')
            ->first();
    }
}
