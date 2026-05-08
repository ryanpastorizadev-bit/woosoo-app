<?php

namespace App\Events;

use App\Models\DeviceOrder;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderRefilled implements ShouldBroadcast, ShouldDispatchAfterCommit
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
        return 'order.refilled';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->order->id,
            'parentOrderId' => $this->order->parent_order_id,
            'deviceId' => $this->order->device_id,
            'tableId' => $this->order->table_id,
            'sessionKey' => $this->order->session_key,
            'status' => $this->order->status,
            'totalCents' => $this->order->total_cents,
        ];
    }
}
