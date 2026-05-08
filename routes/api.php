<?php

use App\Http\Controllers\Api\V1\Device\DeviceOrderController;
use App\Http\Controllers\Api\V1\Device\DeviceSessionController;
use App\Http\Controllers\Api\V1\Device\PrintEventController;
use App\Http\Controllers\Api\V1\Device\RefillOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/device')->group(function (): void {
    Route::post('session/start', [DeviceSessionController::class, 'start']);
    Route::post('session/restore', [DeviceSessionController::class, 'restore']);

    Route::middleware('device')->group(function (): void {
        Route::get('orders/active', [DeviceOrderController::class, 'active']);
        Route::post('orders', [DeviceOrderController::class, 'store']);
        Route::post('orders/{order}/refills', [RefillOrderController::class, 'store']);

        Route::get('print-events', [PrintEventController::class, 'index']);
        Route::post('print-events/{printEvent}/ack', [PrintEventController::class, 'ack']);
    });
});
