<?php

namespace App\Actions\PrintEvents;

use App\Contracts\PrintEventRepository;
use App\Events\PrintEventUpdated;
use App\Models\PrintEvent;

class AckPrintEventAction
{
    public function __construct(private readonly PrintEventRepository $printEvents) {}

    public function execute(PrintEvent $printEvent): PrintEvent
    {
        $printEvent = $this->printEvents->acknowledge($printEvent);

        event(new PrintEventUpdated($printEvent->withoutRelations()->fresh()));

        return $printEvent;
    }
}
