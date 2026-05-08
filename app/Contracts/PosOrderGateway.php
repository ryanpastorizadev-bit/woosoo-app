<?php

namespace App\Contracts;

use App\Models\DeviceOrder;

interface PosOrderGateway
{
    /**
     * Submit an order to the POS and return the external POS order reference.
     */
    public function submit(DeviceOrder $order): string;
}
