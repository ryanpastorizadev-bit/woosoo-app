<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parentOrderId' => $this->parent_order_id,
            'type' => $this->type,
            'status' => $this->status,
            'tableId' => $this->table_id,
            'tableName' => $this->table_name,
            'sessionKey' => $this->session_key,
            'posOrderReference' => $this->pos_order_reference,
            'guestCount' => $this->guest_count,
            'subtotalCents' => $this->subtotal_cents,
            'taxCents' => $this->tax_cents,
            'totalCents' => $this->total_cents,
            'items' => DeviceOrderItemResource::collection($this->whenLoaded('items')),
            'printEvents' => PrintEventResource::collection($this->whenLoaded('printEvents')),
            'submittedAt' => $this->submitted_at?->toISOString(),
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
