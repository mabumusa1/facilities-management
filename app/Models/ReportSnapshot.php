<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use App\Enums\ReportType;
use Carbon\CarbonImmutable;
use Database\Factories\ReportSnapshotFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A cached, tenant-scoped report payload.
 *
 * Simple reports (FinancialSummary, Occupancy) are generated synchronously and
 * do not necessarily persist a snapshot row. Complex/historical reports
 * (LeasePipeline, PortfolioHealth) write a snapshot via the async engine (#312)
 * and serve reads from here.
 *
 * @property int $id
 * @property int $account_tenant_id
 * @property string $report_type
 * @property CarbonImmutable|null $period_start
 * @property CarbonImmutable|null $period_end
 * @property CarbonImmutable|null $generated_at
 * @property array<string, mixed>|null $payload
 * @property string $status
 * @property int|null $requested_by_user_id
 * @property array<string, mixed>|null $filters
 * @property string|null $error_message
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 */
class ReportSnapshot extends Model
{
    /** @use HasFactory<ReportSnapshotFactory> */
    use BelongsToAccountTenant, HasFactory;

    protected $table = 'report_snapshots';

    protected $fillable = [
        'account_tenant_id',
        'report_type',
        'period_start',
        'period_end',
        'generated_at',
        'payload',
        'status',
        'requested_by_user_id',
        'filters',
        'error_message',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'immutable_date',
            'period_end' => 'immutable_date',
            'generated_at' => 'immutable_datetime',
            'payload' => 'array',
            'filters' => 'array',
            'report_type' => ReportType::class,
        ];
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    /**
     * Whether this snapshot has been successfully computed.
     */
    public function isReady(): bool
    {
        return $this->status === 'ready';
    }

    /**
     * Whether this snapshot generation has failed.
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Whether this snapshot is still being generated.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
