<?php

namespace App\Contracts;

use App\Models\Device;
use App\Models\DeviceOrder;
use Illuminate\Support\Collection;

interface DeviceOrderRepository
{
    public function create(array $attributes): DeviceOrder;

    public function createItem(DeviceOrder $order, array $attributes): void;

    public function findForDevice(int $orderId, Device $device): ?DeviceOrder;

    public function findActiveForDeviceSession(Device $device, string $sessionKey): ?DeviceOrder;

    public function getActiveForDevice(Device $device, ?string $sessionKey = null): ?DeviceOrder;

    /**
     * @return Collection<int, DeviceOrder>
     */
    public function listActive(): Collection;
}
