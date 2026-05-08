<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Contracts\PrintEventRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\PrintEventResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminPrintEventController extends Controller
{
    public function __construct(private readonly PrintEventRepository $printEvents) {}

    public function index(): AnonymousResourceCollection
    {
        return PrintEventResource::collection($this->printEvents->listRecent());
    }
}
