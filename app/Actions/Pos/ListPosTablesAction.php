<?php

namespace App\Actions\Pos;

use App\Contracts\PosContextGateway;

class ListPosTablesAction
{
    public function __construct(
        private readonly PosContextGateway $posContextGateway,
    ) {}

    public function execute(): array
    {
        return $this->posContextGateway->tables();
    }
}
