<?php

namespace App\Infrastructure\Repositories;

use App\Contracts\DeviceOrderRepository;
use App\Models\Device;
use App\Models\DeviceOrder;
use Illuminate\Support\Collection;

class LocalDeviceOrderRepository implements DeviceOrderRepository
{
    public function create(array $attributes): DeviceOrder
    {
        return DeviceOrder::query()->create($attributes);
    }

    public function createItem(DeviceOrder $order, array $attributes): void
    {
        $order->items()->create($attributes);
    }

    public function findForDevice(int $orderId, Device $device): ?DeviceOrder
    {
        return DeviceOrder::query()
            ->whereKey($orderId)
            ->where('device_id', $device->id)
            ->first();
    }

    public function findActiveForDeviceSession(Device $device, string $sessionKey): ?DeviceOrder
    {
        return DeviceOrder::query()
            ->where('device_id', $device->id)
            ->where('session_key', $sessionKey)
            ->whereIn('status', [DeviceOrder::STATUS_ACTIVE, DeviceOrder::STATUS_SUBMITTED])
            ->lockForUpdate()
            ->first();
    }

    public function getActiveForDevice(Device $device, ?string $sessionKey = null): ?DeviceOrder
    {
        return DeviceOrder::query()
            ->with(['items', 'printEvents'])
            ->where('device_id', $device->id)
            ->when($sessionKey, fn ($query) => $query->where('session_key', $sessionKey))
            ->whereIn('status', [DeviceOrder::STATUS_ACTIVE, DeviceOrder::STATUS_SUBMITTED])
            ->latest('id')
            ->first();
    }

    public function listActive(): Collection
    {
        return DeviceOrder::query()
            ->with(['device', 'items'])
            ->whereIn('status', [DeviceOrder::STATUS_ACTIVE, DeviceOrder::STATUS_SUBMITTED])
            ->latest('id')
            ->limit(100)
            ->get();
    }
}
