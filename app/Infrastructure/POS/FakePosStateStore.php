<?php

namespace App\Infrastructure\POS;

use Illuminate\Contracts\Cache\Repository as CacheRepository;

class FakePosStateStore
{
    private const TABLES_CACHE_KEY = 'pos.fake.tables';

    private const TABLES_CONFIG_HASH_CACHE_KEY = 'pos.fake.tables.config_hash';

    public function __construct(
        private readonly CacheRepository $cache,
    ) {}

    /**
     * @return array<int, array{id:string,name:string,rawStatus:?string,isAvailable:?bool,isLocked:?bool}>
     */
    public function tables(): array
    {
        /** @var array<int, array{id:string,name:string,rawStatus:?string,isAvailable:?bool,isLocked:?bool}> $configuredTables */
        $configuredTables = array_values(config('pos.fake.tables', []));
        $configuredHash = md5((string) json_encode($configuredTables));
        $cachedHash = (string) $this->cache->get(self::TABLES_CONFIG_HASH_CACHE_KEY, '');

        if ($cachedHash !== $configuredHash || ! is_array($this->cache->get(self::TABLES_CACHE_KEY))) {
            $this->cache->forever(self::TABLES_CACHE_KEY, $configuredTables);
            $this->cache->forever(self::TABLES_CONFIG_HASH_CACHE_KEY, $configuredHash);
        }

        /** @var array<int, array{id:string,name:string,rawStatus:?string,isAvailable:?bool,isLocked:?bool}> $tables */
        $tables = $this->cache->get(self::TABLES_CACHE_KEY, []);

        return $tables;
    }

    public function markTableAsOrderSent(int|string $tableId): void
    {
        $tables = $this->tables();
        $tableId = (string) $tableId;
        $updated = false;

        foreach ($tables as &$table) {
            if ((string) $table['id'] !== $tableId) {
                continue;
            }

            $table['rawStatus'] = 'ORDER_SENT';
            $table['isAvailable'] = false;
            $table['isLocked'] = true;
            $updated = true;
            break;
        }

        unset($table);

        if (! $updated) {
            $tables[] = [
                'id' => $tableId,
                'name' => 'Table '.$tableId,
                'rawStatus' => 'ORDER_SENT',
                'isAvailable' => false,
                'isLocked' => true,
            ];
        }

        $this->cache->forever(self::TABLES_CACHE_KEY, $tables);
    }
}
