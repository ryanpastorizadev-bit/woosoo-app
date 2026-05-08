<?php

namespace App\Actions\PrintEvents;

use App\Models\PrintEvent;

class AckPrintEventAction
{
    public function execute(PrintEvent $printEvent): PrintEvent
    {
        if ($printEvent->acknowledged_at !== null) {
            return $printEvent;
        }

        $printEvent->forceFill([
            'status' => PrintEvent::STATUS_ACKNOWLEDGED,
            'acknowledged_at' => now(),
        ])->save();

        return $printEvent->refresh();
    }
}
