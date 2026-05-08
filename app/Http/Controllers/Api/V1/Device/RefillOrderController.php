<?php

namespace App\Http\Controllers\Api\V1\Device;

use App\Actions\DeviceOrders\RefillOrderAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Device\RefillOrderRequest;
use App\Http\Resources\DeviceOrderResource;
use App\Models\DeviceOrder;

class RefillOrderController extends Controller
{
    public function store(RefillOrderRequest $request, DeviceOrder $order, RefillOrderAction $action): DeviceOrderResource
    {
        return DeviceOrderResource::make($action->execute($order, $request->validated()));
    }
}
