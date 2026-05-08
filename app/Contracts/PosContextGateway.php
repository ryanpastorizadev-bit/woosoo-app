<?php

namespace App\Contracts;

use App\Models\Device;

interface PosContextGateway
{
    /**
     * @return array<string, mixed>
     */
    public function resolveForDevice(Device $device): array;
}
