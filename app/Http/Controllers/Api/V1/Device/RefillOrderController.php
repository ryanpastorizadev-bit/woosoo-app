<?php

namespace App\Http\Controllers\Api\V1\Device;

use App\Actions\DeviceOrders\RefillOrderAction;
use App\Contracts\DeviceOrderRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\Device\RefillOrderRequest;
use App\Http\Resources\DeviceOrderResource;
use App\Models\Device;
use Illuminate\Http\Exceptions\HttpResponseException;

class RefillOrderController extends Controller
{
    public function __construct(private readonly DeviceOrderRepository $orders) {}

    public function store(RefillOrderRequest $request, int $order, RefillOrderAction $action): DeviceOrderResource
    {
        /** @var Device $device */
        $device = $request->attributes->get('device');
        $orderModel = $this->orders->findForDevice($order, $device);

        if (! $orderModel) {
            throw new HttpResponseException(response()->json(['message' => 'Order not found.'], 404));
        }

        return DeviceOrderResource::make($action->execute($orderModel, $request->validated()));
    }
}
