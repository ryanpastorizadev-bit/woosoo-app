<?php

namespace App\Infrastructure\Repositories;

use App\Contracts\PrintEventRepository;
use App\Models\Device;
use App\Models\DeviceOrder;
use App\Models\PrintEvent;
use Illuminate\Support\Collection;

class LocalPrintEventRepository implements PrintEventRepository
{
    public function create(DeviceOrder $order, array $attributes): PrintEvent
    {
        return $order->printEvents()->create($attributes);
    }

    public function acknowledge(PrintEvent $printEvent): PrintEvent
    {
        if ($printEvent->acknowledged_at !== null) {
            return $printEvent;
        }

        $printEvent->forceFill([
            'status' => PrintEvent::STATUS_ACKNOWLEDGED,
            'acknowledged_at' => now(),
        ])->save();

        return $printEvent->refresh();
    }

    public function findForDevice(int $printEventId, Device $device): ?PrintEvent
    {
        return PrintEvent::query()
            ->whereKey($printEventId)
            ->whereHas('order', fn ($query) => $query->where('device_id', $device->id))
            ->first();
    }

    public function listForDevice(Device $device, int $limit = 50): Collection
    {
        return PrintEvent::query()
            ->whereHas('order', fn ($query) => $query->where('device_id', $device->id))
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    public function listRecent(int $limit = 100): Collection
    {
        return PrintEvent::query()
            ->with('order.device')
            ->latest('id')
            ->limit($limit)
            ->get();
    }
}
