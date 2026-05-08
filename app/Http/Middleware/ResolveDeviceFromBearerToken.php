<?php

namespace App\Http\Middleware;

use App\Models\Device;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveDeviceFromBearerToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! is_string($token) || strlen($token) !== 64) {
            return response()->json(['message' => 'Unauthenticated.'], Response::HTTP_UNAUTHORIZED);
        }

        $device = Device::query()
            ->where('token_hash', hash('sha256', $token))
            ->first();

        if (! $device) {
            return response()->json(['message' => 'Unauthenticated.'], Response::HTTP_UNAUTHORIZED);
        }

        $device->forceFill(['last_seen_at' => now()])->save();
        $request->attributes->set('device', $device);

        return $next($request);
    }
}
