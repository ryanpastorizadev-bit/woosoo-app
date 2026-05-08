<?php

namespace App\Events;

use App\Models\DeviceOrder;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public DeviceOrder $order) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('device.'.$this->order->device_id),
            new PrivateChannel('table.'.$this->order->table_id),
            new PrivateChannel('admin.orders'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->order->id,
            'deviceId' => $this->order->device_id,
            'tableId' => $this->order->table_id,
            'sessionKey' => $this->order->session_key,
            'type' => $this->order->type,
            'status' => $this->order->status,
            'totalCents' => $this->order->total_cents,
        ];
    }
}
