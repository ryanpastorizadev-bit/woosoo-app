<?php

namespace App\Infrastructure\POS;

use App\Contracts\PosOrderGateway;
use App\Models\DeviceOrder;

class FakePosOrderGateway implements PosOrderGateway
{
    public function __construct(
        private readonly FakePosStateStore $stateStore,
    ) {}

    public function submit(DeviceOrder $order): string
    {
        $this->stateStore->markTableAsOrderSent($order->table_id);

        return 'FAKE-POS-'.$order->id;
    }
}
