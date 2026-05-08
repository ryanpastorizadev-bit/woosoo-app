<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceOrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'menuId' => $this->menu_id,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'unitPriceCents' => $this->unit_price_cents,
            'lineTotalCents' => $this->line_total_cents,
            'modifiers' => $this->modifiers,
            'metadata' => $this->metadata,
        ];
    }
}
