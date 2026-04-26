<?php

namespace App\Support;

use App\Models\Status;
use Illuminate\Validation\ValidationException;

class StatusWorkflow
{
    /**
     * @var array<string, array<string, array<int, string>>>
     */
    private const WORKFLOWS = [
        'request' => [
            'new' => ['assigned', 'approved', 'request_accepted', 'in_progress', 'rejected', 'canceled', 'rescheduled'],
            'assigned' => ['request_accepted', 'in_progress', 'rejected', 'canceled', 'rescheduled'],
            'approved' => ['request_accepted', 'in_progress', 'rejected', 'canceled', 'rescheduled'],
            'request_accepted' => ['in_progress', 'quote_sent', 'rejected', 'canceled', 'rescheduled'],
            'quote_sent' => ['accepted_by_client', 'rejected_by_client', 'canceled'],
            'accepted_by_client' => ['invoice_created', 'in_progress'],
            'in_progress' => ['invoice_created', 'resolved', 'canceled', 'rescheduled'],
            'rescheduled' => ['in_progress', 'request_accepted', 'canceled'],
            'invoice_created' => ['invoice_accepted', 'invoice_rejected', 'canceled'],
            'invoice_rejected' => ['invoice_created', 'canceled'],
            'invoice_accepted' => ['resolved'],
        ],
        'lease' => [
            'new_contract' => ['active_contract', 'canceled_contract'],
            'active_contract' => ['expired_contract', 'canceled_contract', 'closed_contract'],
            'expired_contract' => ['active_contract', 'closed_contract'],
        ],
        'property_visit' => [
            'new' => ['pending', 'approved', 'rejected', 'canceled'],
            'pending' => ['approved', 'rejected', 'canceled'],
            'approved' => ['checked_in', 'canceled'],
            'checked_in' => ['checked_out'],
        ],
        'invoice' => [
            'new' => ['pending', 'approved', 'rejected', 'paid', 'canceled'],
            'pending' => ['approved', 'rejected', 'paid', 'canceled'],
            'approved' => ['paid', 'canceled'],
            'rejected' => ['pending', 'canceled'],
        ],
        'lease_quote' => [
            'draft' => ['sent', 'expired'],
            'sent' => ['viewed', 'rejected', 'expired'],
            'viewed' => ['accepted', 'rejected', 'expired'],
        ],
    ];

    /**
     * @var array<string, array<string, string>>
     */
    private const ALIASES = [
        'request' => [
            'new' => 'new',
            'assigned' => 'assigned',
            'approved' => 'approved',
            'request-accepted' => 'request_accepted',
            'accepted-request' => 'request_accepted',
            'in-progress' => 'in_progress',
            'quote-sent' => 'quote_sent',
            'accepted-by-client' => 'accepted_by_client',
            'rejected-by-client' => 'rejected_by_client',
            'invoice-created' => 'invoice_created',
            'invoice-accepted' => 'invoice_accepted',
            'invoice-rejected' => 'invoice_rejected',
            'resolved' => 'resolved',
            'rejected' => 'rejected',
            'request-rejected' => 'rejected',
            'canceled' => 'canceled',
            'cancelled' => 'canceled',
            'canceled-by-admin' => 'canceled',
            'rescheduled' => 'rescheduled',
        ],
        'lease' => [
            'new' => 'new_contract',
            'new-contract' => 'new_contract',
            'active' => 'active_contract',
            'active-contract' => 'active_contract',
            'expired' => 'expired_contract',
            'expired-contract' => 'expired_contract',
            'canceled' => 'canceled_contract',
            'cancelled' => 'canceled_contract',
            'canceled-contract' => 'canceled_contract',
            'cancelled-contract' => 'canceled_contract',
            'closed' => 'closed_contract',
            'closed-contract' => 'closed_contract',
        ],
        'property_visit' => [
            'new' => 'new',
            'pending' => 'pending',
            'approved' => 'approved',
            'rejected' => 'rejected',
            'canceled' => 'canceled',
            'cancelled' => 'canceled',
            'checked-in' => 'checked_in',
            'checked-out' => 'checked_out',
        ],
        'invoice' => [
            'new' => 'new',
            'pending' => 'pending',
            'approved' => 'approved',
            'rejected' => 'rejected',
            'paid' => 'paid',
            'canceled' => 'canceled',
            'cancelled' => 'canceled',
        ],
        'lease_quote' => [
            'draft' => 'draft',
            'sent' => 'sent',
            'viewed' => 'viewed',
            'accepted' => 'accepted',
            'rejected' => 'rejected',
            'expired' => 'expired',
        ],
    ];

    public function ensureTransition(string $type, ?int $fromStatusId, int $toStatusId): void
    {
        if ($fromStatusId === null || $fromStatusId === $toStatusId) {
            return;
        }

        $statuses = Status::query()
            ->whereIn('id', [$fromStatusId, $toStatusId])
            ->get()
            ->keyBy('id');

        $fromStatus = $statuses->get($fromStatusId);
        $toStatus = $statuses->get($toStatusId);

        if (! $fromStatus instanceof Status || ! $toStatus instanceof Status) {
            return;
        }

        if ($this->isAllowedTransition($type, $fromStatus, $toStatus)) {
            return;
        }

        throw ValidationException::withMessages([
            'status_id' => __(
                'Invalid status transition from :from to :to.',
                [
                    'from' => $fromStatus->name_en ?? $fromStatus->name ?? (string) $fromStatus->id,
                    'to' => $toStatus->name_en ?? $toStatus->name ?? (string) $toStatus->id,
                ],
            ),
        ]);
    }

    private function isAllowedTransition(string $type, Status $fromStatus, Status $toStatus): bool
    {
        $workflow = self::WORKFLOWS[$type] ?? null;

        if ($workflow === null) {
            return $this->isForwardByPriority($fromStatus, $toStatus);
        }

        $fromKey = $this->statusKey($type, $fromStatus);
        $toKey = $this->statusKey($type, $toStatus);

        if ($fromKey === null || $toKey === null) {
            return $this->isForwardByPriority($fromStatus, $toStatus);
        }

        if ($fromKey === $toKey) {
            return true;
        }

        $allowedTransitions = $workflow[$fromKey] ?? [];

        return in_array($toKey, $allowedTransitions, true);
    }

    private function isForwardByPriority(Status $fromStatus, Status $toStatus): bool
    {
        if (is_numeric($fromStatus->priority) && is_numeric($toStatus->priority)) {
            return (int) $toStatus->priority >= (int) $fromStatus->priority;
        }

        return true;
    }

    private function statusKey(string $type, Status $status): ?string
    {
        $name = $status->name_en ?? $status->name;

        if (! is_string($name) || trim($name) === '') {
            return null;
        }

        $normalized = $this->normalize($name);

        return self::ALIASES[$type][$normalized] ?? null;
    }

    private function normalize(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/i', '-', $value) ?? $value;

        return trim($value, '-');
    }
}
