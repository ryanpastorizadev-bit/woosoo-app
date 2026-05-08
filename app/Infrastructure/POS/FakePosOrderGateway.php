<?php

namespace App\Infrastructure\POS;

use App\Contracts\PosOrderGateway;
use App\Models\DeviceOrder;

class FakePosOrderGateway implements PosOrderGateway
{
    public function submit(DeviceOrder $order): string
    {
        return 'FAKE-POS-'.$order->id;
    }
}
