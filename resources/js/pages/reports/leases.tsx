import { Head, Link } from '@inertiajs/react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import {
    FileText,
    TrendingUp,
    TrendingDown,
    DollarSign,
    Calendar,
    AlertTriangle,
    Building2,
    Home,
} from 'lucide-react';

interface LeaseStatistics {
    total_leases: number;
    new_leases: number;
    active_leases: number;
    expired_leases: number;
    terminated_leases: number;
    percent_new_leases: number;
    percent_active_leases: number;
    percent_expired_leases: number;
    percent_terminated_leases: number;
    active_commercial_leases: number;
    active_residential_leases: number;
    current_month_collection: number;
    current_year_collection: number;
    paid_collection_current_month: number;
    paid_collection_current_year: number;
}

interface StatusItem {
    name: string;
    slug: string;
    count: number;
}

interface StatusReport {
    statuses: StatusItem[];
    total: number;
}

interface ExpiringLease {
    id: number;
    contract_number: string;
    end_date: string;
    unit?: {
        name: string;
    };
    contact?: {
        name: string;
    };
}

interface RentCollection {
    total_due: number;
    total_collected: number;
    total_pending: number;
    total_overdue: number;
    collection_rate: number;
    period: {
        start: string;
        end: string;
    };
}

interface LeaseReportsProps {
    statistics: LeaseStatistics;
    statusReport: StatusReport;
    expiringLeases: ExpiringLease[];
    rentCollection: RentCollection;
}

function formatCurrency(amount: number): string {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

export default function LeaseReports({ statistics, statusReport, expiringLeases, rentCollection }: LeaseReportsProps) {
    const collectionProgress = rentCollection.total_due > 0
        ? (rentCollection.total_collected / rentCollection.total_due) * 100
        : 0;

    return (
        <>
            <Head title="Lease Reports" />
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Lease Reports</h1>
                        <p className="text-muted-foreground">Lease statistics, status, and rent collection</p>
                    </div>
                </div>

                {/* Key Statistics */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Leases</CardTitle>
                            <FileText className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{statistics.total_leases}</div>
                            <p className="text-xs text-muted-foreground">
                                {statistics.new_leases} new this month ({statistics.percent_new_leases}%)
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Active Leases</CardTitle>
                            <TrendingUp className="h-4 w-4 text-green-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-green-600">{statistics.active_leases}</div>
                            <p className="text-xs text-muted-foreground">{statistics.percent_active_leases}% of total</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Expired Leases</CardTitle>
                            <TrendingDown className="h-4 w-4 text-red-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-red-600">{statistics.expired_leases}</div>
                            <p className="text-xs text-muted-foreground">{statistics.percent_expired_leases}% of total</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Terminated</CardTitle>
                            <AlertTriangle className="h-4 w-4 text-orange-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-orange-600">{statistics.terminated_leases}</div>
                            <p className="text-xs text-muted-foreground">{statistics.percent_terminated_leases}% of total</p>
                        </CardContent>
                    </Card>
                </div>

                {/* Lease Types & Status Distribution */}
                <div className="grid gap-6 md:grid-cols-2">
                    {/* Lease Types */}
                    <Card>
                        <CardHeader>
                            <div className="flex items-center gap-2">
                                <Building2 className="h-5 w-5" />
                                <CardTitle>Active Leases by Type</CardTitle>
                            </div>
                            <CardDescription>Distribution of commercial vs residential</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <Building2 className="h-4 w-4 text-blue-500" />
                                        <span className="text-sm">Commercial</span>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <div className="h-2 w-32 rounded-full bg-muted">
                                            <div
                                                className="h-2 rounded-full bg-blue-500"
                                                style={{
                                                    width: `${statistics.active_leases > 0 ? (statistics.active_commercial_leases / statistics.active_leases) * 100 : 0}%`,
                                                }}
                                            />
                                        </div>
                                        <span className="text-sm font-medium">{statistics.active_commercial_leases}</span>
                                    </div>
                                </div>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <Home className="h-4 w-4 text-green-500" />
                                        <span className="text-sm">Residential</span>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <div className="h-2 w-32 rounded-full bg-muted">
                                            <div
                                                className="h-2 rounded-full bg-green-500"
                                                style={{
                                                    width: `${statistics.active_leases > 0 ? (statistics.active_residential_leases / statistics.active_leases) * 100 : 0}%`,
                                                }}
                                            />
                                        </div>
                                        <span className="text-sm font-medium">{statistics.active_residential_leases}</span>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Status Distribution */}
                    <Card>
                        <CardHeader>
                            <div className="flex items-center gap-2">
                                <FileText className="h-5 w-5" />
                                <CardTitle>Leases by Status</CardTitle>
                            </div>
                            <CardDescription>Status distribution breakdown</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-3">
                                {statusReport.statuses.length > 0 ? (
                                    statusReport.statuses.map((status) => (
                                        <div key={status.slug} className="flex items-center justify-between">
                                            <span className="text-sm">{status.name}</span>
                                            <Badge variant="secondary">{status.count}</Badge>
                                        </div>
                                    ))
                                ) : (
                                    <p className="text-sm text-muted-foreground">No status data available</p>
                                )}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Rent Collection */}
                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-2">
                            <DollarSign className="h-5 w-5 text-green-500" />
                            <CardTitle>Rent Collection</CardTitle>
                        </div>
                        <CardDescription>
                            Collection status for {formatDate(rentCollection.period.start)} -{' '}
                            {formatDate(rentCollection.period.end)}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="space-y-6">
                            {/* Progress Bar */}
                            <div className="space-y-2">
                                <div className="flex items-center justify-between text-sm">
                                    <span>Collection Progress</span>
                                    <span className="font-medium">{rentCollection.collection_rate}%</span>
                                </div>
                                <div className="h-4 w-full rounded-full bg-muted">
                                    <div
                                        className="h-4 rounded-full bg-green-500 transition-all"
                                        style={{ width: `${collectionProgress}%` }}
                                    />
                                </div>
                            </div>

                            {/* Collection Details */}
                            <div className="grid gap-4 md:grid-cols-4">
                                <div className="rounded-lg border p-4">
                                    <div className="text-sm text-muted-foreground">Total Due</div>
                                    <div className="text-xl font-bold">{formatCurrency(rentCollection.total_due)}</div>
                                </div>
                                <div className="rounded-lg border p-4">
                                    <div className="text-sm text-muted-foreground">Collected</div>
                                    <div className="text-xl font-bold text-green-600">
                                        {formatCurrency(rentCollection.total_collected)}
                                    </div>
                                </div>
                                <div className="rounded-lg border p-4">
                                    <div className="text-sm text-muted-foreground">Pending</div>
                                    <div className="text-xl font-bold text-orange-600">
                                        {formatCurrency(rentCollection.total_pending)}
                                    </div>
                                </div>
                                <div className="rounded-lg border p-4">
                                    <div className="text-sm text-muted-foreground">Overdue</div>
                                    <div className="text-xl font-bold text-red-600">
                                        {formatCurrency(rentCollection.total_overdue)}
                                    </div>
                                </div>
                            </div>

                            {/* Year to Date */}
                            <div className="grid gap-4 md:grid-cols-2">
                                <div className="rounded-lg border p-4">
                                    <div className="text-sm text-muted-foreground">Year to Date Collection</div>
                                    <div className="text-2xl font-bold">
                                        {formatCurrency(statistics.current_year_collection)}
                                    </div>
                                    <div className="text-sm text-green-600">
                                        {formatCurrency(statistics.paid_collection_current_year)} paid
                                    </div>
                                </div>
                                <div className="rounded-lg border p-4">
                                    <div className="text-sm text-muted-foreground">Month to Date Collection</div>
                                    <div className="text-2xl font-bold">
                                        {formatCurrency(statistics.current_month_collection)}
                                    </div>
                                    <div className="text-sm text-green-600">
                                        {formatCurrency(statistics.paid_collection_current_month)} paid
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Expiring Leases */}
                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-2">
                            <Calendar className="h-5 w-5 text-orange-500" />
                            <CardTitle>Expiring Leases (Next 30 Days)</CardTitle>
                        </div>
                        <CardDescription>Leases requiring attention for renewal</CardDescription>
                    </CardHeader>
                    <CardContent>
                        {expiringLeases.length > 0 ? (
                            <div className="space-y-3">
                                {expiringLeases.map((lease) => (
                                    <div
                                        key={lease.id}
                                        className="flex items-center justify-between rounded-lg border p-3"
                                    >
                                        <div>
                                            <div className="font-medium">{lease.contract_number}</div>
                                            <div className="text-sm text-muted-foreground">
                                                {lease.unit?.name || 'N/A'} - {lease.contact?.name || 'N/A'}
                                            </div>
                                        </div>
                                        <div className="text-right">
                                            <Badge variant="outline">{formatDate(lease.end_date)}</Badge>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="py-8 text-center text-muted-foreground">
                                No leases expiring in the next 30 days
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

LeaseReports.layout = {
    breadcrumbs: [
        {
            title: 'Reports',
            href: '/reports',
        },
        {
            title: 'Lease Reports',
            href: '/reports/leases',
        },
    ],
};
