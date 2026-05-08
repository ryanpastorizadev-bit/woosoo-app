<?php

namespace App\Contracts;

use App\Models\Device;

interface PosContextGateway
{
    /**
     * @return array{
     *     ready:bool,
     *     session:array{id:string,openedAt:?string,status:string}|null,
     *     terminal:array{id:string,name:string,status:string}|null,
     *     blockingReason:?string
     * }
     */
    public function readiness(): array;

    /**
     * @return array<int, array{id:string,name:string,rawStatus:string,status:string,color:string,isOrderable:bool}>
     */
    public function tables(): array;

    /**
     * @return array<string, mixed>
     */
    public function resolveForDevice(Device $device): array;
}
