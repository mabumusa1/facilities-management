<?php

namespace App\Http\Controllers\Leasing;

use App\Enums\DeductionReason;
use App\Enums\InspectionCondition;
use App\Enums\MoveOutReason;
use App\Enums\UnitStatus;
use App\Http\Controllers\Controller;
use App\Models\Lease;
use App\Models\MoveOut;
use App\Models\MoveOutDeduction;
use App\Models\MoveOutRoom;
use App\Models\Status;
use App\Models\Transaction;
use App\Models\UnitStatusHistory;
use App\Support\MoveOutStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class MoveOutController extends Controller
{
    /**
     * Show the initiate move-out form.
     */
    public function initiate(Lease $lease): Response
    {
        $this->authorize('create', [MoveOut::class, $lease]);

        $lease->load(['status', 'tenant', 'units']);

        return Inertia::render('leasing/move-outs/Initiate', [
            'lease' => [
                'id' => $lease->id,
                'contract_number' => $lease->contract_number,
                'end_date' => $lease->end_date?->toDateString(),
                'security_deposit_amount' => $lease->security_deposit_amount,
                'status' => $lease->status ? [
                    'id' => $lease->status->id,
                    'name' => $lease->status->name,
                    'name_en' => $lease->status->name_en,
                ] : null,
                'tenant' => $lease->tenant ? [
                    'id' => $lease->tenant->id,
                    'name' => trim(($lease->tenant->first_name ?? '').' '.($lease->tenant->last_name ?? '')),
                ] : null,
                'units' => $lease->units->map(fn ($u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                ]),
            ],
            'reasons' => collect(MoveOutReason::cases())->map(fn ($r) => [
                'value' => $r->value,
                'label' => $r->label(),
            ]),
        ]);
    }

    /**
     * Store a new move-out record and transition the lease/unit statuses.
     */
    public function store(Request $request, Lease $lease): RedirectResponse
    {
        $this->authorize('create', [MoveOut::class, $lease]);

        $validated = $request->validate([
            'move_out_date' => ['required', 'date'],
            'reason' => ['required', Rule::enum(MoveOutReason::class)],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        /** @var MoveOut $moveOut */
        $moveOut = DB::transaction(function () use ($validated, $request, $lease): MoveOut {
            $moveOut = MoveOut::create([
                'lease_id' => $lease->id,
                'move_out_date' => $validated['move_out_date'],
                'reason' => $validated['reason'],
                'status_id' => MoveOutStatus::IN_PROGRESS,
                'initiated_by' => $request->user()->id,
                'account_tenant_id' => $lease->account_tenant_id,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Transition lease to move-out-in-progress status.
            $lease->update([
                'status_id' => MoveOutStatus::LEASE_MOVE_OUT_IN_PROGRESS,
                'is_move_out' => true,
            ]);

            // Set associated units to under-maintenance.
            $lease->units()->each(function ($unit): void {
                $unit->update(['status' => UnitStatus::UnderMaintenance->value]);
            });

            return $moveOut;
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Move-out initiated.')]);

        return redirect()->route('rf.leases.move-out.inspection', [
            'lease' => $lease->id,
            'moveOut' => $moveOut->id,
        ]);
    }

    /**
     * Show the room-by-room inspection form.
     */
    public function inspection(Lease $lease, MoveOut $moveOut): Response
    {
        $this->authorize('view', $moveOut);

        $moveOut->load(['rooms.photos', 'status']);

        $inspected = $moveOut->rooms->filter(fn ($r) => $r->condition !== null)->count();

        return Inertia::render('leasing/move-outs/Inspection', [
            'lease' => [
                'id' => $lease->id,
                'contract_number' => $lease->contract_number,
            ],
            'moveOut' => [
                'id' => $moveOut->id,
                'status' => $moveOut->status ? [
                    'id' => $moveOut->status->id,
                    'name_en' => $moveOut->status->name_en,
                ] : null,
                'rooms' => $moveOut->rooms->map(fn ($room) => [
                    'id' => $room->id,
                    'name' => $room->name,
                    'condition' => $room->condition?->value,
                    'notes' => $room->notes,
                    'sort_order' => $room->sort_order,
                    'photos' => $room->photos->map(fn ($p) => [
                        'id' => $p->id,
                        'url' => $p->url,
                        'name' => $p->name,
                    ]),
                ]),
                'progress' => [
                    'inspected' => $inspected,
                    'total' => $moveOut->rooms->count(),
                ],
            ],
            'conditions' => collect(InspectionCondition::cases())->map(fn ($c) => [
                'value' => $c->value,
                'label' => $c->label(),
            ]),
        ]);
    }

    /**
     * Save (upsert) room inspection data.
     */
    public function saveInspection(Request $request, Lease $lease, MoveOut $moveOut): RedirectResponse
    {
        $this->authorize('update', $moveOut);

        $validated = $request->validate([
            'rooms' => ['required', 'array'],
            'rooms.*.id' => ['nullable', 'integer'],
            'rooms.*.name' => ['required', 'string', 'max:255'],
            'rooms.*.condition' => ['required', Rule::enum(InspectionCondition::class)],
            'rooms.*.notes' => ['nullable', 'string', 'max:2000'],
            'rooms.*.sort_order' => ['nullable', 'integer'],
        ]);

        DB::transaction(function () use ($validated, $moveOut): void {
            $submittedIds = [];

            foreach ($validated['rooms'] as $index => $roomData) {
                $room = isset($roomData['id'])
                    ? MoveOutRoom::query()->where('move_out_id', $moveOut->id)->find((int) $roomData['id'])
                    : null;

                if ($room) {
                    $room->update([
                        'name' => $roomData['name'],
                        'condition' => $roomData['condition'],
                        'notes' => $roomData['notes'] ?? null,
                        'sort_order' => $roomData['sort_order'] ?? $index,
                    ]);
                } else {
                    $room = MoveOutRoom::create([
                        'move_out_id' => $moveOut->id,
                        'name' => $roomData['name'],
                        'condition' => $roomData['condition'],
                        'notes' => $roomData['notes'] ?? null,
                        'sort_order' => $roomData['sort_order'] ?? $index,
                    ]);
                }

                $submittedIds[] = $room->id;
            }

            // Remove rooms that were deleted by the user.
            MoveOutRoom::query()
                ->where('move_out_id', $moveOut->id)
                ->whereNotIn('id', $submittedIds)
                ->each(function (MoveOutRoom $room): void {
                    $room->photos()->each(fn ($p) => Storage::disk('public')->delete($p->url));
                    $room->photos()->delete();
                    $room->delete();
                });
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Inspection saved.')]);

        return redirect()->route('rf.leases.move-out.inspection', [
            'lease' => $lease->id,
            'moveOut' => $moveOut->id,
        ]);
    }

    /**
     * Upload a photo for a room.
     */
    public function uploadRoomPhoto(Request $request, Lease $lease, MoveOut $moveOut, MoveOutRoom $room): JsonResponse
    {
        $this->authorize('update', $moveOut);

        abort_unless($room->move_out_id === $moveOut->id, 403);

        $request->validate([
            'photo' => ['required', 'file', 'image', 'max:10240'],
        ]);

        $path = $request->file('photo')->store('move-out-photos', 'public');

        $media = $room->photos()->create([
            'url' => $path,
            'name' => $request->file('photo')->getClientOriginalName(),
            'mediable_type' => MoveOutRoom::class,
            'mediable_id' => $room->id,
            'collection' => 'inspection_photos',
        ]);

        return response()->json([
            'id' => $media->id,
            'url' => $media->url,
            'name' => $media->name,
        ]);
    }

    /**
     * Delete a room photo.
     */
    public function deleteRoomPhoto(Request $request, Lease $lease, MoveOut $moveOut, MoveOutRoom $room, int $photoId): JsonResponse
    {
        $this->authorize('update', $moveOut);

        abort_unless($room->move_out_id === $moveOut->id, 403);

        $photo = $room->photos()->findOrFail($photoId);
        Storage::disk('public')->delete($photo->url);
        $photo->delete();

        return response()->json(['deleted' => true]);
    }

    /**
     * Show the deposit deductions form.
     */
    public function deductions(Lease $lease, MoveOut $moveOut): Response
    {
        $this->authorize('view', $moveOut);

        $lease->load('status');
        $moveOut->load('deductions');

        $totalDeductions = (float) $moveOut->deductions()->sum('amount');
        $securityDeposit = (float) ($lease->security_deposit_amount ?? 0);
        $refundAmount = $securityDeposit - $totalDeductions;

        return Inertia::render('leasing/move-outs/Deductions', [
            'lease' => [
                'id' => $lease->id,
                'contract_number' => $lease->contract_number,
                'security_deposit_amount' => $lease->security_deposit_amount,
            ],
            'moveOut' => [
                'id' => $moveOut->id,
                'deductions' => $moveOut->deductions->map(fn ($d) => [
                    'id' => $d->id,
                    'label_en' => $d->label_en,
                    'label_ar' => $d->label_ar,
                    'amount' => $d->amount,
                    'reason' => $d->reason?->value,
                ]),
                'summary' => [
                    'security_deposit' => $securityDeposit,
                    'total_deductions' => $totalDeductions,
                    'refund_amount' => $refundAmount,
                    'exceeds_deposit' => $totalDeductions > $securityDeposit,
                ],
            ],
            'reasons' => collect(DeductionReason::cases())->map(fn ($r) => [
                'value' => $r->value,
                'label' => $r->label(),
            ]),
        ]);
    }

    /**
     * Save (sync) deposit deductions.
     */
    public function saveDeductions(Request $request, Lease $lease, MoveOut $moveOut): RedirectResponse
    {
        $this->authorize('update', $moveOut);

        $validated = $request->validate([
            'deductions' => ['present', 'array'],
            'deductions.*.id' => ['nullable', 'integer'],
            'deductions.*.label_en' => ['required', 'string', 'max:255'],
            'deductions.*.label_ar' => ['required', 'string', 'max:255'],
            'deductions.*.amount' => ['required', 'numeric', 'min:0'],
            'deductions.*.reason' => ['required', Rule::enum(DeductionReason::class)],
        ]);

        DB::transaction(function () use ($validated, $moveOut): void {
            $submittedIds = [];

            foreach ($validated['deductions'] as $deductionData) {
                $deduction = isset($deductionData['id'])
                    ? MoveOutDeduction::query()->where('move_out_id', $moveOut->id)->find((int) $deductionData['id'])
                    : null;

                if ($deduction) {
                    $deduction->update([
                        'label_en' => $deductionData['label_en'],
                        'label_ar' => $deductionData['label_ar'],
                        'amount' => $deductionData['amount'],
                        'reason' => $deductionData['reason'],
                    ]);
                } else {
                    $deduction = MoveOutDeduction::create([
                        'move_out_id' => $moveOut->id,
                        'label_en' => $deductionData['label_en'],
                        'label_ar' => $deductionData['label_ar'],
                        'amount' => $deductionData['amount'],
                        'reason' => $deductionData['reason'],
                    ]);
                }

                $submittedIds[] = $deduction->id;
            }

            // Remove deductions that were deleted by the user.
            MoveOutDeduction::query()
                ->where('move_out_id', $moveOut->id)
                ->whereNotIn('id', $submittedIds)
                ->delete();
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Deductions saved.')]);

        return redirect()->route('rf.leases.move-out.deductions', [
            'lease' => $lease->id,
            'moveOut' => $moveOut->id,
        ]);
    }

    /**
     * Show the settlement review page.
     */
    public function settlement(Lease $lease, MoveOut $moveOut): Response
    {
        $this->authorize('view', $moveOut);

        $lease->load(['tenant', 'units', 'status']);
        $moveOut->load(['deductions', 'status', 'rooms']);

        $totalDeductions = (float) $moveOut->deductions()->sum('amount');
        $securityDeposit = (float) ($lease->security_deposit_amount ?? 0);
        $netAmount = $securityDeposit - $totalDeductions;

        return Inertia::render('leasing/move-outs/Settlement', [
            'lease' => [
                'id' => $lease->id,
                'contract_number' => $lease->contract_number,
                'security_deposit_amount' => $lease->security_deposit_amount,
                'tenant' => $lease->tenant ? [
                    'id' => $lease->tenant->id,
                    'name' => trim(($lease->tenant->first_name ?? '').' '.($lease->tenant->last_name ?? '')),
                ] : null,
                'units' => $lease->units->map(fn ($u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                ]),
            ],
            'moveOut' => [
                'id' => $moveOut->id,
                'move_out_date' => $moveOut->move_out_date?->toDateString(),
                'status' => $moveOut->status ? [
                    'id' => $moveOut->status->id,
                    'name_en' => $moveOut->status->name_en,
                ] : null,
                'deductions' => $moveOut->deductions->map(fn ($d) => [
                    'id' => $d->id,
                    'label_en' => $d->label_en,
                    'label_ar' => $d->label_ar,
                    'amount' => $d->amount,
                    'reason' => $d->reason?->value,
                ]),
                'summary' => [
                    'security_deposit' => $securityDeposit,
                    'total_deductions' => $totalDeductions,
                    'net_amount' => $netAmount,
                    'is_refund' => $netAmount > 0,
                    'is_charge' => $netAmount < 0,
                    'is_zero' => $netAmount === 0.0,
                ],
            ],
        ]);
    }

    /**
     * Finalize the move-out settlement.
     */
    public function finalize(Request $request, Lease $lease, MoveOut $moveOut): RedirectResponse
    {
        $this->authorize('finalize', $moveOut);

        $validated = $request->validate([
            'generate_statement' => ['nullable', 'boolean'],
        ]);

        $generateStatement = (bool) ($validated['generate_statement'] ?? false);

        $totalDeductions = (float) $moveOut->deductions()->sum('amount');
        $securityDeposit = (float) ($lease->security_deposit_amount ?? 0);
        $netAmount = round($securityDeposit - $totalDeductions, 2);

        $unitIds = $lease->units()->pluck('rf_units.id')->toArray();

        DB::transaction(function () use ($lease, $moveOut, $netAmount, $unitIds, $request): void {
            $userId = $request->user()->id;
            $tenantId = $lease->tenant_id;
            $settlementDate = now();

            // Create refund or charge transaction.
            if ($netAmount > 0) {
                Transaction::create([
                    'lease_id' => $lease->id,
                    'unit_id' => $unitIds[0] ?? null,
                    'amount' => $netAmount,
                    'direction' => 'money_out',
                    'assignee_type' => 'App\\Models\\Resident',
                    'assignee_id' => $tenantId,
                    'account_tenant_id' => $lease->account_tenant_id,
                    'details' => __('Deposit refund for move-out #:id', ['id' => $moveOut->id]),
                    'due_on' => $settlementDate,
                ]);
            } elseif ($netAmount < 0) {
                Transaction::create([
                    'lease_id' => $lease->id,
                    'unit_id' => $unitIds[0] ?? null,
                    'amount' => abs($netAmount),
                    'direction' => 'money_in',
                    'assignee_type' => 'App\\Models\\Resident',
                    'assignee_id' => $tenantId,
                    'account_tenant_id' => $lease->account_tenant_id,
                    'details' => __('Damage charge for move-out #:id', ['id' => $moveOut->id]),
                    'due_on' => $settlementDate,
                ]);
            }

            // Void remaining future unpaid transactions for this lease.
            Transaction::query()
                ->where('lease_id', $lease->id)
                ->where('is_paid', false)
                ->where('due_on', '>', $settlementDate)
                ->update(['is_paid' => true]);

            // Transition lease to terminated/closed status.
            $terminatedId = $this->statusIdByNames('lease', [
                'terminated', 'terminated contract', 'terminated_contract',
                'closed', 'closed contract', 'closed_contract',
                'cancelled', 'cancelled contract', 'canceled', 'canceled contract',
            ]);
            $lease->update([
                'status_id' => $terminatedId ?? $lease->status_id,
                'actual_end_at' => $moveOut->move_out_date ?? $settlementDate,
                'is_move_out' => true,
            ]);

            // Release all lease units back to available.
            foreach ($lease->units as $unit) {
                $fromStatus = $unit->status;
                $unit->update(['status' => UnitStatus::Available->value]);

                UnitStatusHistory::create([
                    'unit_id' => $unit->id,
                    'from_status' => $fromStatus,
                    'to_status' => UnitStatus::Available->value,
                    'changed_by' => $userId,
                    'account_tenant_id' => $lease->account_tenant_id,
                    'reason' => __('Move-out #:id settled. Unit released.', ['id' => $moveOut->id]),
                ]);
            }

            // Mark move-out as completed.
            $moveOut->update([
                'status_id' => MoveOutStatus::COMPLETED,
                'settled_at' => $settlementDate,
            ]);
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Move-out finalized.')]);

        if ($generateStatement) {
            return redirect()->route('rf.leases.move-out.statement', [
                'lease' => $lease->id,
                'moveOut' => $moveOut->id,
            ]);
        }

        return redirect()->route('rf.leases.show', ['lease' => $lease->id]);
    }

    /**
     * Show the settlement statement.
     */
    public function statement(Lease $lease, MoveOut $moveOut): Response
    {
        $this->authorize('view', $moveOut);

        $lease->load(['tenant', 'units']);
        $moveOut->load(['deductions']);

        $totalDeductions = (float) $moveOut->deductions()->sum('amount');
        $securityDeposit = (float) ($lease->security_deposit_amount ?? 0);
        $netAmount = $securityDeposit - $totalDeductions;

        return Inertia::render('leasing/move-outs/Statement', [
            'lease' => [
                'id' => $lease->id,
                'contract_number' => $lease->contract_number,
                'tenant' => $lease->tenant ? [
                    'id' => $lease->tenant->id,
                    'name' => trim(($lease->tenant->first_name ?? '').' '.($lease->tenant->last_name ?? '')),
                ] : null,
                'units' => $lease->units->map(fn ($u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                ]),
            ],
            'moveOut' => [
                'id' => $moveOut->id,
                'move_out_date' => $moveOut->move_out_date?->toDateString(),
                'settled_at' => $moveOut->settled_at?->toDateString(),
                'deductions' => $moveOut->deductions->map(fn ($d) => [
                    'id' => $d->id,
                    'label_en' => $d->label_en,
                    'label_ar' => $d->label_ar,
                    'amount' => $d->amount,
                ]),
                'summary' => [
                    'security_deposit' => $securityDeposit,
                    'total_deductions' => $totalDeductions,
                    'net_amount' => $netAmount,
                    'is_refund' => $netAmount > 0,
                    'is_charge' => $netAmount < 0,
                    'abs_net_amount' => abs($netAmount),
                ],
            ],
        ]);
    }

    /**
     * Look up a status ID by list of possible name values within a given type.
     */
    private function statusIdByNames(string $type, array $names): ?int
    {
        $normalized = array_values(array_filter(array_map(
            fn (string $n): string => strtolower(trim($n)),
            $names,
        )));

        if ($normalized === []) {
            return null;
        }

        return Status::query()
            ->where('type', $type)
            ->where(function (Builder $query) use ($normalized): void {
                foreach ($normalized as $i => $n) {
                    if ($i === 0) {
                        $query->whereRaw('LOWER(COALESCE(name_en, name)) = ?', [$n]);
                    } else {
                        $query->orWhereRaw('LOWER(COALESCE(name_en, name)) = ?', [$n]);
                    }
                }
            })
            ->value('id');
    }
}
