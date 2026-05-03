<?php

namespace App\Exports;

use App\Models\Lease;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeasePipelineExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    use Exportable;

    /**
     * @param  array{expiry_window?: int|null, status_id?: int|null, community_id?: int|null, search?: string|null}  $filters
     * @param  int[]  $statusIds  Active/Expiring Soon status IDs to include
     */
    public function __construct(
        private readonly array $filters = [],
        private readonly array $statusIds = [],
    ) {}

    public function query(): Builder
    {
        return Lease::query()
            ->with(['tenant', 'status', 'paymentSchedule', 'units.community', 'units.building'])
            ->when(
                ! empty($this->statusIds),
                fn ($q) => $q->whereIn('rf_leases.status_id', $this->statusIds),
            )
            ->when(
                ! empty($this->filters['status_id']),
                fn ($q) => $q->where('rf_leases.status_id', (int) $this->filters['status_id']),
            )
            ->when(
                ! empty($this->filters['community_id']),
                fn ($q) => $q->whereIn(
                    'rf_leases.id',
                    fn ($sub) => $sub
                        ->select('lease_units.lease_id')
                        ->from('lease_units')
                        ->join('rf_units', 'rf_units.id', '=', 'lease_units.unit_id')
                        ->where('rf_units.rf_community_id', (int) $this->filters['community_id'])
                )
            )
            ->when(
                ! empty($this->filters['search']),
                fn ($q) => $q->where(function ($inner) {
                    $search = $this->filters['search'];
                    $inner->where('contract_number', 'like', "%{$search}%")
                        ->orWhereHas('tenant', fn ($tq) => $tq
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                        );
                })
            )
            ->orderByDesc('rf_leases.created_at');
    }

    /** @return string[] */
    public function headings(): array
    {
        return [
            'Lease ID',
            'Contract #',
            'Unit',
            'Building',
            'Community',
            'Tenant Name',
            'Start Date',
            'End Date',
            'Rent Amount',
            'Payment Frequency',
            'Status',
            'Days Until Expiry',
        ];
    }

    /** @param  Lease  $lease */
    public function map($lease): array
    {
        $firstUnit = $lease->units->first();
        $daysUntilExpiry = $lease->end_date
            ? (int) Carbon::parse($lease->end_date)->startOfDay()->diffInDays(now()->startOfDay(), false) * -1
            : null;

        return [
            $lease->id,
            $lease->contract_number,
            $firstUnit?->name,
            $firstUnit?->building?->name,
            $firstUnit?->community?->name,
            trim(($lease->tenant?->first_name ?? '').' '.($lease->tenant?->last_name ?? '')),
            $lease->start_date?->format('Y-m-d'),
            $lease->end_date?->format('Y-m-d'),
            $lease->rental_total_amount,
            $lease->paymentSchedule?->name ?? $lease->paymentSchedule?->name_en,
            $lease->status?->name_en ?? $lease->status?->name,
            $daysUntilExpiry,
        ];
    }
}
