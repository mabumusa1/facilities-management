<?php

namespace App\Services;

use App\Models\Request as ServiceRequest;

class SlaService
{
    public function calculate(ServiceRequest $request): void
    {
        $category = $request->category;

        if ($category?->response_hours) {
            $request->sla_response_due_at = $request->created_at->addHours($category->response_hours);
        }
        if ($category?->resolution_hours) {
            $request->sla_resolution_due_at = $request->created_at->addHours($category->resolution_hours);
        }
    }

    /**
     * Check SLA breaches for a specific request.
     */
    public function check(ServiceRequest $request): void
    {
        if ($request->sla_response_due_at && ! $request->sla_breach_response && now()->gt($request->sla_response_due_at)) {
            $request->sla_breach_response = true;
        }

        if ($request->sla_resolution_due_at && ! $request->sla_breach_resolution && now()->gt($request->sla_resolution_due_at)) {
            $request->sla_breach_resolution = true;
        }

        if ($request->isDirty(['sla_breach_response', 'sla_breach_resolution'])) {
            $request->save();
        }
    }

    /**
     * Check all open requests for SLA breaches (scheduled job).
     */
    public function checkAll(): int
    {
        $requests = ServiceRequest::query()
            ->whereNull('completed_date')
            ->where(function ($q): void {
                $q->where(function ($sq): void {
                    $sq->whereNotNull('sla_response_due_at')
                        ->where('sla_breach_response', false)
                        ->where('sla_response_due_at', '<', now());
                })->orWhere(function ($sq): void {
                    $sq->whereNotNull('sla_resolution_due_at')
                        ->where('sla_breach_resolution', false)
                        ->where('sla_resolution_due_at', '<', now());
                });
            })
            ->get();

        foreach ($requests as $request) {
            $this->check($request);
        }

        return $requests->count();
    }
}
