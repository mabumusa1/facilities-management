import { Head, Link } from '@inertiajs/react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    FileText,
    Wrench,
    Building2,
    TrendingUp,
    TrendingDown,
    ArrowRight,
    DollarSign,
    Home,
    PieChart,
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

interface MaintenanceStatistics {
    total_requests: number;
    open_requests: number;
    in_progress_requests: number;
    completed_requests: number;
    closed_requests: number;
    requests_this_month: number;
    requests_this_year: number;
    high_priority_count: number;
    average_resolution_days: number;
}

interface OccupancyReport {
    total_units: number;
    occupied_units: number;
    vacant_units: number;
    maintenance_units: number;
    occupancy_rate: number;
    vacancy_rate: number;
}

interface ReportsIndexProps {
    leaseStatistics: LeaseStatistics;
    maintenanceStatistics: MaintenanceStatistics;
    occupancyReport: OccupancyReport;
}

function formatCurrency(amount: number): string {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
}

function StatCard({
    title,
    value,
    subValue,
    icon: Icon,
    trend,
    href,
}: {
    title: string;
    value: string | number;
    subValue?: string;
    icon: React.ComponentType<{ className?: string }>;
    trend?: 'up' | 'down';
    href?: string;
}) {
    const content = (
        <Card className="transition-shadow hover:shadow-md">
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">{title}</CardTitle>
                <Icon className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
                <div className="flex items-center gap-2">
                    <div className="text-2xl font-bold">{value}</div>
                    {trend && (
                        <>
                            {trend === 'up' && <TrendingUp className="h-4 w-4 text-green-500" />}
                            {trend === 'down' && <TrendingDown className="h-4 w-4 text-red-500" />}
                        </>
                    )}
                </div>
                {subValue && <p className="text-xs text-muted-foreground">{subValue}</p>}
            </CardContent>
        </Card>
    );

    if (href) {
        return <Link href={href}>{content}</Link>;
    }

    return content;
}

export default function ReportsIndex({ leaseStatistics, maintenanceStatistics, occupancyReport }: ReportsIndexProps) {
    return (
        <>
            <Head title="Reports" />
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Reports</h1>
                        <p className="text-muted-foreground">System reports and analytics overview</p>
                    </div>
                </div>

                {/* Quick Stats Grid */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <StatCard
                        title="Total Leases"
                        value={leaseStatistics.total_leases}
                        subValue={`${leaseStatistics.active_leases} active`}
                        icon={FileText}
                    />
                    <StatCard
                        title="Service Requests"
                        value={maintenanceStatistics.total_requests}
                        subValue={`${maintenanceStatistics.open_requests} open`}
                        icon={Wrench}
                    />
                    <StatCard
                        title="Occupancy Rate"
                        value={`${occupancyReport.occupancy_rate}%`}
                        subValue={`${occupancyReport.occupied_units} of ${occupancyReport.total_units} units`}
                        icon={Home}
                        trend={occupancyReport.occupancy_rate >= 80 ? 'up' : 'down'}
                    />
                    <StatCard
                        title="Monthly Collection"
                        value={formatCurrency(leaseStatistics.current_month_collection)}
                        subValue={`${formatCurrency(leaseStatistics.paid_collection_current_month)} collected`}
                        icon={DollarSign}
                    />
                </div>

                {/* Report Categories */}
                <div className="grid gap-6 md:grid-cols-2">
                    {/* Lease Reports Card */}
                    <Card>
                        <CardHeader>
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                    <FileText className="h-5 w-5 text-blue-500" />
                                    <CardTitle>Lease Reports</CardTitle>
                                </div>
                                <Link href="/reports/leases">
                                    <Button variant="ghost" size="sm">
                                        View All
                                        <ArrowRight className="ml-1 h-4 w-4" />
                                    </Button>
                                </Link>
                            </div>
                            <CardDescription>Lease status, expirations, and rent collection</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                <div className="grid grid-cols-2 gap-4">
                                    <div className="rounded-lg border p-3">
                                        <div className="text-xl font-bold text-green-600">
                                            {leaseStatistics.active_leases}
                                        </div>
                                        <div className="text-xs text-muted-foreground">Active Leases</div>
                                    </div>
                                    <div className="rounded-lg border p-3">
                                        <div className="text-xl font-bold text-orange-600">
                                            {leaseStatistics.expired_leases}
                                        </div>
                                        <div className="text-xs text-muted-foreground">Expired</div>
                                    </div>
                                </div>
                                <div className="space-y-2">
                                    <div className="flex items-center justify-between text-sm">
                                        <span>Commercial</span>
                                        <Badge variant="secondary">{leaseStatistics.active_commercial_leases}</Badge>
                                    </div>
                                    <div className="flex items-center justify-between text-sm">
                                        <span>Residential</span>
                                        <Badge variant="secondary">{leaseStatistics.active_residential_leases}</Badge>
                                    </div>
                                    <div className="flex items-center justify-between text-sm">
                                        <span>New This Month</span>
                                        <Badge className="bg-green-100 text-green-800">
                                            {leaseStatistics.new_leases}
                                        </Badge>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Maintenance Reports Card */}
                    <Card>
                        <CardHeader>
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                    <Wrench className="h-5 w-5 text-orange-500" />
                                    <CardTitle>Maintenance Reports</CardTitle>
                                </div>
                                <Link href="/reports/maintenance">
                                    <Button variant="ghost" size="sm">
                                        View All
                                        <ArrowRight className="ml-1 h-4 w-4" />
                                    </Button>
                                </Link>
                            </div>
                            <CardDescription>Service requests, categories, and trends</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                <div className="grid grid-cols-2 gap-4">
                                    <div className="rounded-lg border p-3">
                                        <div className="text-xl font-bold text-blue-600">
                                            {maintenanceStatistics.open_requests}
                                        </div>
                                        <div className="text-xs text-muted-foreground">Open Requests</div>
                                    </div>
                                    <div className="rounded-lg border p-3">
                                        <div className="text-xl font-bold text-yellow-600">
                                            {maintenanceStatistics.in_progress_requests}
                                        </div>
                                        <div className="text-xs text-muted-foreground">In Progress</div>
                                    </div>
                                </div>
                                <div className="space-y-2">
                                    <div className="flex items-center justify-between text-sm">
                                        <span>High Priority</span>
                                        <Badge variant="destructive">{maintenanceStatistics.high_priority_count}</Badge>
                                    </div>
                                    <div className="flex items-center justify-between text-sm">
                                        <span>Completed</span>
                                        <Badge className="bg-green-100 text-green-800">
                                            {maintenanceStatistics.completed_requests}
                                        </Badge>
                                    </div>
                                    <div className="flex items-center justify-between text-sm">
                                        <span>Avg Resolution</span>
                                        <Badge variant="outline">{maintenanceStatistics.average_resolution_days} days</Badge>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Occupancy Overview */}
                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-2">
                            <Building2 className="h-5 w-5 text-purple-500" />
                            <CardTitle>Occupancy Overview</CardTitle>
                        </div>
                        <CardDescription>Unit occupancy status and rates</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="grid gap-6 md:grid-cols-4">
                            <div className="space-y-2">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm font-medium">Total Units</span>
                                    <span className="text-2xl font-bold">{occupancyReport.total_units}</span>
                                </div>
                                <div className="h-2 w-full rounded-full bg-muted">
                                    <div className="h-2 rounded-full bg-primary" style={{ width: '100%' }} />
                                </div>
                            </div>
                            <div className="space-y-2">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm font-medium text-green-600">Occupied</span>
                                    <span className="text-2xl font-bold text-green-600">
                                        {occupancyReport.occupied_units}
                                    </span>
                                </div>
                                <div className="h-2 w-full rounded-full bg-muted">
                                    <div
                                        className="h-2 rounded-full bg-green-500"
                                        style={{ width: `${occupancyReport.occupancy_rate}%` }}
                                    />
                                </div>
                            </div>
                            <div className="space-y-2">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm font-medium text-blue-600">Vacant</span>
                                    <span className="text-2xl font-bold text-blue-600">{occupancyReport.vacant_units}</span>
                                </div>
                                <div className="h-2 w-full rounded-full bg-muted">
                                    <div
                                        className="h-2 rounded-full bg-blue-500"
                                        style={{ width: `${occupancyReport.vacancy_rate}%` }}
                                    />
                                </div>
                            </div>
                            <div className="space-y-2">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm font-medium text-orange-600">Maintenance</span>
                                    <span className="text-2xl font-bold text-orange-600">
                                        {occupancyReport.maintenance_units}
                                    </span>
                                </div>
                                <div className="h-2 w-full rounded-full bg-muted">
                                    <div
                                        className="h-2 rounded-full bg-orange-500"
                                        style={{
                                            width: `${occupancyReport.total_units > 0 ? (occupancyReport.maintenance_units / occupancyReport.total_units) * 100 : 0}%`,
                                        }}
                                    />
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

ReportsIndex.layout = {
    breadcrumbs: [
        {
            title: 'Reports',
            href: '/reports',
        },
    ],
};
