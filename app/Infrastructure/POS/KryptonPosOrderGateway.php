<?php

namespace App\Infrastructure\POS;

use App\Contracts\PosOrderGateway;
use App\Models\DeviceOrder;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class KryptonPosOrderGateway implements PosOrderGateway
{
    public function submit(DeviceOrder $order): string
    {
        try {
            /** @var object|null $result */
            $result = DB::connection(config('services.krypton.connection', 'sqlsrv'))
                ->selectOne(
                    'EXEC dbo.sp_WoosooSubmitOrder @table_id = ?, @session_key = ?, @order_type = ?, @order_payload = ?',
                    [
                        $order->table_id,
                        $order->session_key,
                        $order->type,
                        json_encode($order->loadMissing('items')->toArray(), JSON_THROW_ON_ERROR),
                    ],
                );
        } catch (Throwable $exception) {
            report($exception);

            throw new RuntimeException('Unable to submit order to POS.');
        }

        $reference = data_get((array) $result, 'pos_order_reference');

        if (! is_string($reference) || $reference === '') {
            throw new RuntimeException('Unable to submit order to POS.');
        }

        return $reference;
    }
}
