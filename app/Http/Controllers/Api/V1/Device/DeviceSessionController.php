<?php

namespace App\Http\Controllers\Api\V1\Device;

use App\Http\Controllers\Controller;
use App\Http\Requests\Device\StartSessionRequest;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeviceSessionController extends Controller
{
    public function start(StartSessionRequest $request): JsonResponse
    {
        $plainToken = Str::random(64);

        $device = Device::query()->create([
            'device_name' => $request->string('device_name')->toString() ?: null,
            'table_id' => $request->integer('table_id'),
            'table_name' => $request->string('table_name')->toString(),
            'token_hash' => hash('sha256', $plainToken),
            'last_seen_at' => now(),
        ]);

        return response()->json([
            'deviceId' => $device->id,
            'deviceName' => $device->device_name,
            'tableId' => $device->table_id,
            'tableName' => $device->table_name,
            'token' => $plainToken,
        ], 201);
    }

    public function restore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'size:64'],
        ]);

        $device = Device::query()
            ->where('token_hash', hash('sha256', $validated['token']))
            ->firstOrFail();

        $device->forceFill(['last_seen_at' => now()])->save();

        return response()->json([
            'deviceId' => $device->id,
            'deviceName' => $device->device_name,
            'tableId' => $device->table_id,
            'tableName' => $device->table_name,
        ]);
    }
}
