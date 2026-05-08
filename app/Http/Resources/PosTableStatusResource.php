<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PosTableStatusResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id' => (string) ($this['id'] ?? ''),
            'name' => (string) ($this['name'] ?? ''),
            'rawStatus' => (string) ($this['rawStatus'] ?? 'UNKNOWN'),
            'status' => (string) ($this['status'] ?? 'unknown'),
            'color' => (string) ($this['color'] ?? 'gray'),
            'isOrderable' => (bool) ($this['isOrderable'] ?? false),
        ];
    }
}
