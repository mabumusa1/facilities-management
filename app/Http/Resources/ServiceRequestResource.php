<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'request_number' => $this->request_number,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => [
                'id' => $this->status?->id,
                'name' => $this->status?->name,
                'slug' => $this->status?->slug,
                'domain' => $this->status?->domain,
            ],
            'category' => $this->when($this->relationLoaded('category'), [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
            ]),
            'subcategory' => $this->when($this->relationLoaded('subcategory'), [
                'id' => $this->subcategory?->id,
                'name' => $this->subcategory?->name,
            ]),
            'requester' => $this->when($this->relationLoaded('requester') && $this->requester, [
                'id' => $this->requester?->id,
                'name' => $this->requester?->full_name ?? $this->requester?->first_name.' '.$this->requester?->last_name,
                'email' => $this->requester?->email,
                'phone' => $this->requester?->phone_number,
            ]),
            'professional' => $this->when($this->relationLoaded('professional') && $this->professional, [
                'id' => $this->professional?->id,
                'name' => $this->professional?->full_name ?? $this->professional?->first_name.' '.$this->professional?->last_name,
                'email' => $this->professional?->email,
                'phone' => $this->professional?->phone_number,
            ]),
            'community' => $this->when($this->relationLoaded('community') && $this->community, [
                'id' => $this->community?->id,
                'name' => $this->community?->name,
            ]),
            'building' => $this->when($this->relationLoaded('building') && $this->building, [
                'id' => $this->building?->id,
                'name' => $this->building?->name,
            ]),
            'unit' => $this->when($this->relationLoaded('unit') && $this->unit, [
                'id' => $this->unit?->id,
                'unit_number' => $this->unit?->unit_number,
            ]),
            'scheduled_date' => $this->scheduled_date?->format('Y-m-d'),
            'scheduled_time' => $this->scheduled_time?->format('H:i'),
            'is_all_day' => $this->is_all_day,
            'accepted_at' => $this->accepted_at?->format('Y-m-d H:i'),
            'started_at' => $this->started_at?->format('Y-m-d H:i'),
            'completed_at' => $this->completed_at?->format('Y-m-d H:i'),
            'canceled_at' => $this->canceled_at?->format('Y-m-d H:i'),
            'estimated_cost' => $this->estimated_cost,
            'actual_cost' => $this->actual_cost,
            'currency' => $this->currency,
            'attachments' => $this->attachments,
            'notes' => $this->notes,
            'admin_notes' => $this->admin_notes,
            'professional_notes' => $this->professional_notes,
            'rejection_reason' => $this->rejection_reason,
            'cancellation_reason' => $this->cancellation_reason,
            'rating' => $this->rating,
            'feedback' => $this->feedback,
            'state_history' => $this->when($this->relationLoaded('stateHistory'),
                $this->stateHistory->map(fn ($history) => [
                    'id' => $history->id,
                    'from_status' => [
                        'id' => $history->fromStatus?->id,
                        'name' => $history->fromStatus?->name,
                    ],
                    'to_status' => [
                        'id' => $history->toStatus->id,
                        'name' => $history->toStatus->name,
                    ],
                    'changed_by' => $history->changedBy ? [
                        'id' => $history->changedBy->id,
                        'name' => $history->changedBy->full_name ?? $history->changedBy->first_name.' '.$history->changedBy->last_name,
                    ] : null,
                    'notes' => $history->notes,
                    'metadata' => $history->metadata,
                    'created_at' => $history->created_at->format('Y-m-d H:i'),
                ])
            ),
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i'),
        ];
    }
}
