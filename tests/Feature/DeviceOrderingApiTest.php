<?php

use App\Models\Device;
use App\Models\DeviceOrder;
use App\Models\PrintEvent;

it('starts a device session', function (): void {
    $response = $this->postJson('/api/v1/device/session/start', [
        'device_name' => 'Galaxy Tab A9 01',
        'table_id' => 12,
        'table_name' => 'Table 12',
    ]);

    $response->assertCreated()
        ->assertJsonPath('tableId', 12)
        ->assertJsonStructure([
            'deviceId',
            'deviceName',
            'tableId',
            'tableName',
            'token',
        ]);

    $this->assertDatabaseHas('devices', [
        'device_name' => 'Galaxy Tab A9 01',
        'table_id' => 12,
        'table_name' => 'Table 12',
    ]);
});

it('creates an initial order and print event', function (): void {
    $device = Device::query()->create([
        'table_id' => 7,
        'table_name' => 'Table 7',
        'token_hash' => hash('sha256', 'test-token'),
    ]);

    $response = $this->postJson('/api/v1/device/orders', [
        'device_id' => $device->id,
        'session_key' => 'session-001',
        'guest_count' => 3,
        'items' => [
            [
                'menu_id' => 101,
                'name' => 'Samgyeopsal',
                'quantity' => 2,
                'unit_price_cents' => 12000,
            ],
        ],
    ]);

    $response->assertOk()
        ->assertJsonPath('data.type', DeviceOrder::TYPE_INITIAL)
        ->assertJsonPath('data.status', DeviceOrder::STATUS_ACTIVE)
        ->assertJsonPath('data.totalCents', 24000)
        ->assertJsonPath('data.posOrderReference', 'FAKE-POS-1');

    $this->assertDatabaseHas('device_order_items', [
        'menu_id' => 101,
        'quantity' => 2,
        'line_total_cents' => 24000,
    ]);

    $this->assertDatabaseHas('print_events', [
        'status' => PrintEvent::STATUS_PENDING,
        'target' => 'kitchen',
    ]);
});

it('blocks duplicate active orders for the same device session', function (): void {
    $device = Device::query()->create([
        'table_id' => 7,
        'table_name' => 'Table 7',
        'token_hash' => hash('sha256', 'test-token'),
    ]);

    $payload = [
        'device_id' => $device->id,
        'session_key' => 'session-duplicate',
        'guest_count' => 2,
        'items' => [
            [
                'menu_id' => 101,
                'name' => 'Samgyeopsal',
                'quantity' => 1,
                'unit_price_cents' => 12000,
            ],
        ],
    ];

    $this->postJson('/api/v1/device/orders', $payload)->assertOk();
    $this->postJson('/api/v1/device/orders', $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('session_key');
});

it('submits a refill order for an active order', function (): void {
    $device = Device::query()->create([
        'table_id' => 8,
        'table_name' => 'Table 8',
        'token_hash' => hash('sha256', 'test-token'),
    ]);

    $order = DeviceOrder::query()->create([
        'device_id' => $device->id,
        'table_id' => 8,
        'table_name' => 'Table 8',
        'session_key' => 'session-refill',
        'type' => DeviceOrder::TYPE_INITIAL,
        'status' => DeviceOrder::STATUS_ACTIVE,
        'guest_count' => 2,
    ]);

    $response = $this->postJson("/api/v1/device/orders/{$order->id}/refills", [
        'items' => [
            [
                'menu_id' => 202,
                'name' => 'Kimchi Refill',
                'quantity' => 1,
                'unit_price_cents' => 0,
            ],
        ],
    ]);

    $response->assertOk()
        ->assertJsonPath('data.type', DeviceOrder::TYPE_REFILL)
        ->assertJsonPath('data.parentOrderId', $order->id);
});

it('acknowledges a print event once', function (): void {
    $device = Device::query()->create([
        'table_id' => 9,
        'table_name' => 'Table 9',
        'token_hash' => hash('sha256', 'test-token'),
    ]);

    $order = DeviceOrder::query()->create([
        'device_id' => $device->id,
        'table_id' => 9,
        'table_name' => 'Table 9',
        'session_key' => 'session-print',
        'type' => DeviceOrder::TYPE_INITIAL,
        'status' => DeviceOrder::STATUS_ACTIVE,
        'guest_count' => 2,
    ]);

    $printEvent = PrintEvent::query()->create([
        'device_order_id' => $order->id,
        'target' => 'kitchen',
        'payload' => ['kind' => 'initial_order'],
    ]);

    $this->postJson("/api/v1/device/print-events/{$printEvent->id}/ack")
        ->assertOk()
        ->assertJsonPath('data.status', PrintEvent::STATUS_ACKNOWLEDGED);

    expect($printEvent->refresh()->acknowledged_at)->not->toBeNull();
});
