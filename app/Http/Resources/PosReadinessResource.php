<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PosReadinessResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'ready' => (bool) ($this['ready'] ?? false),
            'session' => $this['session'] ?? null,
            'terminal' => $this['terminal'] ?? null,
            'blockingReason' => $this['blockingReason'] ?? null,
        ];
    }
}
