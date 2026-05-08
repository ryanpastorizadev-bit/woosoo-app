<?php

namespace App\Actions\DeviceOrders;

use App\Contracts\PosOrderGateway;
use App\Models\Device;
use App\Models\DeviceOrder;
use App\Services\OrderTotalsCalculator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateOrderAction
{
    public function __construct(
        private readonly OrderTotalsCalculator $totalsCalculator,
        private readonly PosOrderGateway $posOrderGateway,
    ) {}

    /**
     * @param  array{session_key:string, guest_count:int, items:array<int, array<string, mixed>>, metadata?:array<string, mixed>}  $payload
     */
    public function execute(Device $device, array $payload): DeviceOrder
    {
        return DB::transaction(function () use ($device, $payload): DeviceOrder {
            $hasActiveOrder = DeviceOrder::query()
                ->where('device_id', $device->id)
                ->where('session_key', $payload['session_key'])
                ->whereIn('status', [DeviceOrder::STATUS_ACTIVE, DeviceOrder::STATUS_SUBMITTED])
                ->lockForUpdate()
                ->exists();

            if ($hasActiveOrder) {
                throw ValidationException::withMessages([
                    'session_key' => 'This device session already has an active order.',
                ]);
            }

            $totals = $this->totalsCalculator->calculate($payload['items']);

            $order = DeviceOrder::query()->create([
                'device_id' => $device->id,
                'table_id' => $device->table_id,
                'table_name' => $device->table_name,
                'session_key' => $payload['session_key'],
                'type' => DeviceOrder::TYPE_INITIAL,
                'status' => DeviceOrder::STATUS_ACTIVE,
                'guest_count' => $payload['guest_count'],
                ...$totals,
                'metadata' => $payload['metadata'] ?? null,
                'submitted_at' => now(),
            ]);

            foreach ($payload['items'] as $item) {
                $quantity = (int) $item['quantity'];
                $unitPrice = (int) ($item['unit_price_cents'] ?? 0);

                $order->items()->create([
                    'menu_id' => $item['menu_id'],
                    'name' => $item['name'],
                    'quantity' => $quantity,
                    'unit_price_cents' => $unitPrice,
                    'line_total_cents' => $quantity * $unitPrice,
                    'modifiers' => $item['modifiers'] ?? null,
                    'metadata' => $item['metadata'] ?? null,
                ]);
            }

            $order->forceFill([
                'pos_order_reference' => $this->posOrderGateway->submit($order->load('items')),
            ])->save();

            $order->printEvents()->create([
                'target' => 'kitchen',
                'payload' => [
                    'kind' => 'initial_order',
                    'order_id' => $order->id,
                    'table_name' => $order->table_name,
                ],
            ]);

            return $order->load(['items', 'printEvents']);
        });
    }
}
