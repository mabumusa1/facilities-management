<?php

namespace App\Enums;

enum ReportType: string
{
    case FinancialSummary = 'financial_summary';
    case Occupancy = 'occupancy';
    case LeasePipeline = 'lease_pipeline';
    case VatReturn = 'vat_return';
    case ReceivablesAging = 'receivables_aging';
    case PortfolioHealth = 'portfolio_health';

    /**
     * Human-readable label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::FinancialSummary => 'Financial Summary',
            self::Occupancy => 'Occupancy',
            self::LeasePipeline => 'Lease Pipeline',
            self::VatReturn => 'VAT Return',
            self::ReceivablesAging => 'Receivables Aging',
            self::PortfolioHealth => 'Portfolio Health',
        };
    }

    /**
     * Whether this report type uses the snapshot (async) pattern vs. live query.
     *
     * Simple reports (FinancialSummary, Occupancy) run live for immediate freshness.
     * Complex/historical reports write snapshots via the async engine (#312).
     */
    public function isSnapshot(): bool
    {
        return match ($this) {
            self::LeasePipeline,
            self::PortfolioHealth => true,
            default => false,
        };
    }
}
