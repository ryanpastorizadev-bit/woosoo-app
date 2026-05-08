<?php

namespace App\Infrastructure\POS;

use App\Contracts\PosContextGateway;
use App\Models\Device;

class FakePosContextGateway implements PosContextGateway
{
    public function __construct(
        private readonly FakePosStateStore $stateStore,
    ) {}

    public function readiness(): array
    {
        /** @var array{
         *     session:array{id:string,openedAt:?string,status:string}|null,
         *     terminal:array{id:string,name:string,status:string}|null,
         *     blockingReason:?string
         * } $configuredReadiness
         */
        $configuredReadiness = config('pos.fake.readiness', []);
        $session = $configuredReadiness['session'] ?? null;
        $terminal = $configuredReadiness['terminal'] ?? null;
        $ready = $session !== null && $terminal !== null;

        return [
            'ready' => $ready,
            'session' => $session,
            'terminal' => $terminal,
            'blockingReason' => $ready
                ? null
                : ($configuredReadiness['blockingReason'] ?? 'No open POS session or terminal found.'),
        ];
    }

    public function tables(): array
    {
        return array_map(function (array $table): array {
            $rawStatus = $table['rawStatus'] ?? null;
            $normalized = PosTableStatusNormalizer::normalize(
                $rawStatus,
                $table['isAvailable'] ?? null,
                $table['isLocked'] ?? null,
            );

            return [
                'id' => (string) $table['id'],
                'name' => $table['name'] ?? ('Table '.(string) $table['id']),
                'rawStatus' => (string) ($rawStatus ?? 'UNKNOWN'),
                ...$normalized,
            ];
        }, $this->stateStore->tables());
    }

    public function resolveForDevice(Device $device): array
    {
        return [
            'table_id' => $device->table_id,
            'table_name' => $device->table_name,
        ];
    }
}
