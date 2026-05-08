<?php

namespace App\Http\Controllers\Api\V1\Device;

use App\Actions\PrintEvents\AckPrintEventAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\PrintEventResource;
use App\Models\PrintEvent;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PrintEventController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return PrintEventResource::collection(
            PrintEvent::query()->latest('id')->limit(50)->get()
        );
    }

    public function ack(PrintEvent $printEvent, AckPrintEventAction $action): PrintEventResource
    {
        return PrintEventResource::make($action->execute($printEvent));
    }
}
