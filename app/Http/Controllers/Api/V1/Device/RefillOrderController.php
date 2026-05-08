<?php

namespace App\Http\Controllers\Api\V1\Device;

use App\Actions\DeviceOrders\RefillOrderAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Device\RefillOrderRequest;
use App\Http\Resources\DeviceOrderResource;
use App\Models\Device;
use App\Models\DeviceOrder;
use Illuminate\Http\Exceptions\HttpResponseException;

class RefillOrderController extends Controller
{
    public function store(RefillOrderRequest $request, DeviceOrder $order, RefillOrderAction $action): DeviceOrderResource
    {
        /** @var Device $device */
        $device = $request->attributes->get('device');

        if ((int) $order->device_id !== (int) $device->id) {
            throw new HttpResponseException(response()->json(['message' => 'Order not found.'], 404));
        }

        return DeviceOrderResource::make($action->execute($order, $request->validated()));
    }
}
