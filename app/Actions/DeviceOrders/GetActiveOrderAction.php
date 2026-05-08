<?php

namespace App\Actions\DeviceOrders;

use App\Contracts\DeviceOrderRepository;
use App\Models\Device;
use App\Models\DeviceOrder;

class GetActiveOrderAction
{
    public function __construct(private readonly DeviceOrderRepository $orders) {}

    public function execute(Device $device, ?string $sessionKey = null): ?DeviceOrder
    {
        return $this->orders->getActiveForDevice($device, $sessionKey);
    }
}
