<?php

namespace App\Http\Controllers\Api\V1\Device;

use App\Actions\Pos\GetPosReadinessAction;
use App\Actions\Pos\ListPosTablesAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\PosReadinessResource;
use App\Http\Resources\PosTableStatusCollectionResource;

class PosContextController extends Controller
{
    public function readiness(GetPosReadinessAction $action): PosReadinessResource
    {
        return PosReadinessResource::make($action->execute());
    }

    public function tables(ListPosTablesAction $action): PosTableStatusCollectionResource
    {
        return PosTableStatusCollectionResource::make([
            'tables' => $action->execute(),
        ]);
    }
}
