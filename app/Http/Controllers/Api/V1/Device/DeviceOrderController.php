<?php

namespace App\Http\Controllers\Api\V1\Device;

use App\Actions\DeviceOrders\CreateOrderAction;
use App\Actions\DeviceOrders\GetActiveOrderAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Device\OrderRequest;
use App\Http\Resources\DeviceOrderResource;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceOrderController extends Controller
{
    public function store(OrderRequest $request, CreateOrderAction $action): DeviceOrderResource
    {
        /** @var Device $device */
        $device = $request->attributes->get('device');

        return DeviceOrderResource::make($action->execute($device, $request->validated()));
    }

    public function active(Request $request, GetActiveOrderAction $action): JsonResponse|DeviceOrderResource
    {
        $validated = $request->validate([
            'session_key' => ['nullable', 'string', 'max:120'],
        ]);

        /** @var Device $device */
        $device = $request->attributes->get('device');
        $order = $action->execute($device, $validated['session_key'] ?? null);

        if (! $order) {
            return response()->json(['data' => null]);
        }

        return DeviceOrderResource::make($order);
    }
}
