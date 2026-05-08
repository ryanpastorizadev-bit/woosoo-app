<?php

namespace App\Events;

use App\Models\PrintEvent;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrintEventUpdated implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public PrintEvent $printEvent) {}

    public function broadcastOn(): array
    {
        $this->printEvent->loadMissing('order');

        return [
            new PrivateChannel('device.'.$this->printEvent->order->device_id),
            new PrivateChannel('table.'.$this->printEvent->order->table_id),
            new PrivateChannel('admin.print-events'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'print-event.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->printEvent->id,
            'orderId' => $this->printEvent->device_order_id,
            'status' => $this->printEvent->status,
            'target' => $this->printEvent->target,
            'acknowledgedAt' => $this->printEvent->acknowledged_at?->toISOString(),
            'updatedAt' => $this->printEvent->updated_at?->toISOString(),
        ];
    }
}
