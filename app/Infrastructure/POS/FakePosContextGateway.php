<?php

namespace App\Infrastructure\POS;

use App\Contracts\PosContextGateway;
use App\Models\Device;

class FakePosContextGateway implements PosContextGateway
{
    public function resolveForDevice(Device $device): array
    {
        return [
            'table_id' => $device->table_id,
            'table_name' => $device->table_name,
        ];
    }
}
