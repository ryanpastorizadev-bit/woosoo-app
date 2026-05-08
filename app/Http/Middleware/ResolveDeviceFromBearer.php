<?php

namespace App\Http\Middleware;

use App\Models\Device;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveDeviceFromBearer
{
    public function handle(Request $request, Closure $next): Response
    {
        $presented = $request->bearerToken();

        if (! is_string($presented) || strlen($presented) !== 64) {
            return response()->json(['message' => 'Device credentials are required.'], 401);
        }

        $device = Device::query()
            ->where('token_hash', hash('sha256', $presented))
            ->first();

        if ($device === null) {
            return response()->json(['message' => 'Device credentials are invalid.'], 401);
        }

        $device->forceFill(['last_seen_at' => now()])->save();
        $request->attributes->set('device', $device);

        return $next($request);
    }
}
