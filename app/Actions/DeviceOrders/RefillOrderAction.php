<?php

namespace App\Actions\DeviceOrders;

use App\Contracts\DeviceOrderRepository;
use App\Contracts\PosOrderGateway;
use App\Contracts\PrintEventRepository;
use App\Events\OrderRefilled;
use App\Events\PrintEventCreated;
use App\Models\DeviceOrder;
use App\Services\OrderTotalsCalculator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RefillOrderAction
{
    public function __construct(
        private readonly OrderTotalsCalculator $totalsCalculator,
        private readonly PosOrderGateway $posOrderGateway,
        private readonly DeviceOrderRepository $orders,
        private readonly PrintEventRepository $printEvents,
    ) {}

    /**
     * @param  array{items:array<int, array<string, mixed>>, metadata?:array<string, mixed>}  $payload
     */
    public function execute(DeviceOrder $parentOrder, array $payload): DeviceOrder
    {
        if (! in_array($parentOrder->status, [DeviceOrder::STATUS_ACTIVE, DeviceOrder::STATUS_SUBMITTED], true)) {
            throw ValidationException::withMessages([
                'order' => 'Refills can only be submitted for active orders.',
            ]);
        }

        return DB::transaction(function () use ($parentOrder, $payload): DeviceOrder {
            $parentOrder->refresh();

            $totals = $this->totalsCalculator->calculate($payload['items']);

            $refill = $this->orders->create([
                'device_id' => $parentOrder->device_id,
                'parent_order_id' => $parentOrder->id,
                'table_id' => $parentOrder->table_id,
                'table_name' => $parentOrder->table_name,
                'session_key' => $parentOrder->session_key,
                'type' => DeviceOrder::TYPE_REFILL,
                'status' => DeviceOrder::STATUS_SUBMITTED,
                'guest_count' => $parentOrder->guest_count,
                ...$totals,
                'metadata' => $payload['metadata'] ?? null,
                'submitted_at' => now(),
            ]);

            foreach ($payload['items'] as $item) {
                $quantity = (int) $item['quantity'];
                $unitPrice = (int) ($item['unit_price_cents'] ?? 0);

                $this->orders->createItem($refill, [
                    'menu_id' => $item['menu_id'],
                    'name' => $item['name'],
                    'quantity' => $quantity,
                    'unit_price_cents' => $unitPrice,
                    'line_total_cents' => $quantity * $unitPrice,
                    'modifiers' => $item['modifiers'] ?? null,
                    'metadata' => $item['metadata'] ?? null,
                ]);
            }

            $refill->forceFill([
                'pos_order_reference' => $this->posOrderGateway->submit($refill->load('items')),
            ])->save();

            $printEvent = $this->printEvents->create($refill, [
                'target' => 'kitchen',
                'payload' => [
                    'kind' => 'refill_order',
                    'order_id' => $refill->id,
                    'parent_order_id' => $parentOrder->id,
                    'table_name' => $refill->table_name,
                ],
            ]);

            event(new OrderRefilled($refill->withoutRelations()->fresh()));
            event(new PrintEventCreated($printEvent->withoutRelations()->fresh()));

            return $refill->load(['items', 'printEvents']);
        });
    }
}
