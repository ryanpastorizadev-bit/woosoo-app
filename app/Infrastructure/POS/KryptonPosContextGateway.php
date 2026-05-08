<?php

namespace App\Infrastructure\POS;

use App\Contracts\PosContextGateway;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class KryptonPosContextGateway implements PosContextGateway
{
    public function readiness(): array
    {
        return Cache::remember(
            config('pos.cache.context_key', 'krypton.context'),
            now()->addSeconds((int) config('pos.cache.context_ttl_seconds', 30)),
            function (): array {
                try {
                    $session = $this->findOpenSession();
                    $terminal = $this->findTerminal();
                    $ready = $session !== null && $terminal !== null;

                    return [
                        'ready' => $ready,
                        'session' => $session,
                        'terminal' => $terminal,
                        'blockingReason' => $ready ? null : 'No open POS session or terminal found.',
                    ];
                } catch (Throwable) {
                    return [
                        'ready' => false,
                        'session' => null,
                        'terminal' => null,
                        'blockingReason' => 'Unable to read POS context.',
                    ];
                }
            },
        );
    }

    public function tables(): array
    {
        try {
            if (! $this->hasPosTable('tables')) {
                return [];
            }

            return DB::connection('pos')
                ->table('tables')
                ->orderBy('id')
                ->get()
                ->map(function (object $row): array {
                    $rawStatus = $this->stringValue($row, ['status', 'table_status']);
                    $normalized = PosTableStatusNormalizer::normalize(
                        $rawStatus,
                        $this->boolValue($row, ['is_available', 'available']),
                        $this->boolValue($row, ['is_locked', 'locked']),
                    );

                    return [
                        'id' => (string) ($this->value($row, ['id', 'table_id']) ?? ''),
                        'name' => (string) ($this->value($row, ['name', 'table_name']) ?? 'Table'),
                        'rawStatus' => $rawStatus ?? 'UNKNOWN',
                        ...$normalized,
                    ];
                })
                ->filter(fn (array $table): bool => $table['id'] !== '')
                ->values()
                ->all();
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * @return array{id:string,openedAt:?string,status:string}|null
     */
    private function findOpenSession(): ?array
    {
        $table = $this->resolveFirstExistingTable(['sessions', 'pos_sessions', 'session']);

        if ($table === null) {
            return null;
        }

        $query = DB::connection('pos')->table($table);

        if ($this->hasPosColumn($table, 'date_time_closed')) {
            $query->whereNull('date_time_closed');
        } elseif ($this->hasPosColumn($table, 'closed_at')) {
            $query->whereNull('closed_at');
        }

        if ($this->hasPosColumn($table, 'id')) {
            $query->orderByDesc('id');
        }

        $row = $query->first();

        if ($row === null) {
            return null;
        }

        return [
            'id' => (string) ($this->value($row, ['id', 'session_id']) ?? ''),
            'openedAt' => $this->stringValue($row, ['date_time_opened', 'opened_at', 'created_at']),
            'status' => 'open',
        ];
    }

    /**
     * @return array{id:string,name:string,status:string}|null
     */
    private function findTerminal(): ?array
    {
        $table = $this->resolveFirstExistingTable(['terminals', 'pos_terminals', 'terminal']);

        if ($table === null) {
            return null;
        }

        $query = DB::connection('pos')->table($table);
        $terminalId = config('pos.terminal_id');

        if (is_numeric($terminalId)) {
            $query->where('id', (int) $terminalId);
        }

        if ($this->hasPosColumn($table, 'id')) {
            $query->orderByDesc('id');
        }

        $row = $query->first();

        if ($row === null) {
            return null;
        }

        return [
            'id' => (string) ($this->value($row, ['id', 'terminal_id']) ?? ''),
            'name' => (string) ($this->value($row, ['name', 'terminal_name']) ?? 'Terminal'),
            'status' => strtolower((string) ($this->value($row, ['status']) ?? 'open')),
        ];
    }

    private function resolveFirstExistingTable(array $tables): ?string
    {
        foreach ($tables as $table) {
            if ($this->hasPosTable($table)) {
                return $table;
            }
        }

        return null;
    }

    private function hasPosTable(string $table): bool
    {
        try {
            return Schema::connection('pos')->hasTable($table);
        } catch (Throwable) {
            return false;
        }
    }

    private function hasPosColumn(string $table, string $column): bool
    {
        try {
            return Schema::connection('pos')->hasColumn($table, $column);
        } catch (Throwable) {
            return false;
        }
    }

    private function value(object $row, array $keys): mixed
    {
        foreach ($keys as $key) {
            if (property_exists($row, $key)) {
                return $row->{$key};
            }
        }

        return null;
    }

    private function stringValue(object $row, array $keys): ?string
    {
        $value = $this->value($row, $keys);

        return $value === null ? null : (string) $value;
    }

    private function boolValue(object $row, array $keys): ?bool
    {
        $value = $this->value($row, $keys);

        if ($value === null) {
            return null;
        }

        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? (bool) $value;
    }
}
