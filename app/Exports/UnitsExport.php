<?php

namespace App\Exports;

use App\Models\Unit;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UnitsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    use Exportable;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(private array $filters = []) {}

    public function query()
    {
        return Unit::query()
            ->with(['community', 'building', 'category', 'type', 'status', 'owner', 'tenant', 'city', 'district'])
            ->when(! empty($this->filters['status']), fn ($q) => $q->where('status', $this->filters['status']))
            ->when(! empty($this->filters['community_id']), fn ($q) => $q->where('rf_community_id', $this->filters['community_id']))
            ->when(! empty($this->filters['building_id']), fn ($q) => $q->where('rf_building_id', $this->filters['building_id']))
            ->when(! empty($this->filters['category_id']), fn ($q) => $q->where('category_id', $this->filters['category_id']))
            ->when(! empty($this->filters['search']), fn ($q) => $q->where('name', 'like', "%{$this->filters['search']}%"))
            ->latest();
    }

    public function headings(): array
    {
        return [
            'ID', 'Name', 'Community', 'Building', 'Category', 'Type',
            'Status', 'Owner', 'Tenant', 'City', 'District',
            'Net Area (sqm)', 'Floor', 'Marketplace', 'Off Plan',
        ];
    }

    /** @param  Unit  $unit */
    public function map($unit): array
    {
        return [
            $unit->id,
            $unit->name,
            $unit->community?->name,
            $unit->building?->name,
            $unit->category?->name_en ?? $unit->category?->name,
            $unit->type?->name_en ?? $unit->type?->name,
            $unit->status,
            $unit->owner ? $unit->owner->first_name.' '.$unit->owner->last_name : null,
            $unit->tenant ? $unit->tenant->first_name.' '.$unit->tenant->last_name : null,
            $unit->city?->name,
            $unit->district?->name,
            $unit->net_area,
            $unit->floor_no,
            $unit->is_market_place ? 'Yes' : 'No',
            $unit->is_off_plan_sale ? 'Yes' : 'No',
        ];
    }
}
