<?php

namespace App\Infrastructure\POS;

class PosTableStatusNormalizer
{
    /**
     * @return array{status:string,color:string,isOrderable:bool}
     */
    public static function normalize(?string $rawStatus, ?bool $isAvailable = null, ?bool $isLocked = null): array
    {
        $status = strtoupper((string) $rawStatus);

        if ($status === '' && $isLocked === true) {
            $status = 'LOCKED';
        } elseif ($status === '' && $isAvailable === true) {
            $status = 'AVAILABLE';
        }

        return match ($status) {
            'AVAILABLE' => ['status' => 'available', 'color' => 'green', 'isOrderable' => true],
            'OPEN', 'OCCUPIED', 'ACTIVE', 'ORDER_SENT' => ['status' => 'occupied', 'color' => 'red', 'isOrderable' => false],
            'LOCKED' => ['status' => 'locked', 'color' => 'gray', 'isOrderable' => false],
            'DIRTY' => ['status' => 'dirty', 'color' => 'orange', 'isOrderable' => false],
            default => ['status' => 'unknown', 'color' => 'gray', 'isOrderable' => false],
        };
    }
}
