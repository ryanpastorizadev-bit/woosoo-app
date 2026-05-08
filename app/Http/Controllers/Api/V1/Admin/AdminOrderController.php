<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Contracts\DeviceOrderRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\DeviceOrderResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminOrderController extends Controller
{
    public function __construct(private readonly DeviceOrderRepository $orders) {}

    public function active(): AnonymousResourceCollection
    {
        return DeviceOrderResource::collection($this->orders->listActive());
    }
}
