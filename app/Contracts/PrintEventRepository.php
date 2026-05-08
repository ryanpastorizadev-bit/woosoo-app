<?php

namespace App\Contracts;

use App\Models\Device;
use App\Models\DeviceOrder;
use App\Models\PrintEvent;
use Illuminate\Support\Collection;

interface PrintEventRepository
{
    public function create(DeviceOrder $order, array $attributes): PrintEvent;

    public function acknowledge(PrintEvent $printEvent): PrintEvent;

    public function findForDevice(int $printEventId, Device $device): ?PrintEvent;

    /**
     * @return Collection<int, PrintEvent>
     */
    public function listForDevice(Device $device, int $limit = 50): Collection;

    /**
     * @return Collection<int, PrintEvent>
     */
    public function listRecent(int $limit = 100): Collection;
}
