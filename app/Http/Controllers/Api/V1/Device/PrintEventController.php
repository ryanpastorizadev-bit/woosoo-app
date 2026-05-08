<?php

namespace App\Http\Controllers\Api\V1\Device;

use App\Actions\PrintEvents\AckPrintEventAction;
use App\Contracts\PrintEventRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\Device\AckPrintEventRequest;
use App\Http\Resources\PrintEventResource;
use App\Models\Device;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PrintEventController extends Controller
{
    public function __construct(private readonly PrintEventRepository $printEvents) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var Device $device */
        $device = $request->attributes->get('device');

        return PrintEventResource::collection($this->printEvents->listForDevice($device));
    }

    public function ack(AckPrintEventRequest $request, int $printEvent, AckPrintEventAction $action): PrintEventResource
    {
        /** @var Device $device */
        $device = $request->attributes->get('device');
        $event = $this->printEvents->findForDevice($printEvent, $device);

        if (! $event) {
            throw new HttpResponseException(response()->json(['message' => 'Print event not found.'], 404));
        }

        return PrintEventResource::make($action->execute($event));
    }
}
