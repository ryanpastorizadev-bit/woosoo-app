<?php

namespace App\Services;

class OrderTotalsCalculator
{
    /**
     * @param  array<int, array{quantity:int, unit_price_cents?:int}>  $items
     * @return array{subtotal_cents:int, tax_cents:int, total_cents:int}
     */
    public function calculate(array $items): array
    {
        $subtotal = collect($items)->sum(fn (array $item): int => (int) $item['quantity'] * (int) ($item['unit_price_cents'] ?? 0)
        );

        return [
            'subtotal_cents' => $subtotal,
            'tax_cents' => 0,
            'total_cents' => $subtotal,
        ];
    }
}
