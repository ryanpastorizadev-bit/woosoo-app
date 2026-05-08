<?php

namespace App\Http\Controllers\Api\V1\Device;

use App\Actions\PrintEvents\AckPrintEventAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\PrintEventResource;
use App\Models\Device;
use App\Models\PrintEvent;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PrintEventController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var Device $device */
        $device = $request->attributes->get('device');

        return PrintEventResource::collection(
            PrintEvent::query()
                ->whereHas('order', fn ($query) => $query->where('device_id', $device->id))
                ->latest('id')
                ->limit(50)
                ->get()
        );
    }

    public function ack(Request $request, PrintEvent $printEvent, AckPrintEventAction $action): PrintEventResource
    {
        /** @var Device $device */
        $device = $request->attributes->get('device');
        $printEvent->loadMissing('order');

        if ((int) $printEvent->order->device_id !== (int) $device->id) {
            throw new HttpResponseException(response()->json(['message' => 'Print event not found.'], 404));
        }

        return PrintEventResource::make($action->execute($printEvent));
    }
}
