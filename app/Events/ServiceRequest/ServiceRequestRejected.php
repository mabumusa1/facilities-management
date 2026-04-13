<?php

namespace App\Events\ServiceRequest;

use App\Models\ServiceRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServiceRequestRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ServiceRequest $serviceRequest) {}
}
