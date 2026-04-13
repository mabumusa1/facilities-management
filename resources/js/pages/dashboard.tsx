import { Head, Link } from '@inertiajs/react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { dashboard } from '@/routes';
import {
    Building2,
    Home,
    FileText,
    Wrench,
    Store,
    DollarSign,
    Calendar,
    Users,
    AlertTriangle,
    CheckCircle,
    Clock,
    TrendingUp,
    TrendingDown,
} from 'lucide-react';

interface UnitStatistics {
    vacant: number;
    leased: number;
    sold: number;
    sold_and_lease: number;
    maintenance: number;
    reserved: number;
    total: number;
}

interface RequiresAttention {
    requests_approval: number;
    pending_complaints: number;
    expiring_leases: number;
    overdue_receipts: number;
}

interface LeaseStatistics {
    active: number;
    expiring_soon: number;
    expired: number;
    total: number;
}

interface ServiceRequestStatistics {
    open: number;
    in_progress: number;
    pending_approval: number;
    completed: number;
    closed: number;
    total: number;
}

interface MarketplaceStatistics {
    active_listings: number;
    total_listings: number;
    scheduled_visits: number;
    pending_offers: number;
    completed_sales: number;
}

interface FinancialOverview {
    monthly_income: number;
    monthly_expenses: number;
    net_income: number;
    pending_payments: number;
    overdue_payments: number;
}

interface FacilityStatistics {
    today_bookings: number;
    upcoming_bookings: number;
    pending_approval: number;
    total_bookings: number;
}

interface VisitorStatistics {
    expected_today: number;
    checked_in_today: number;
    pending_approval: number;
}

interface DashboardStatistics {
    units: UnitStatistics;
    requires_attention: RequiresAttention;
    leases: LeaseStatistics;
    service_requests: ServiceRequestStatistics;
    marketplace: MarketplaceStatistics;
    financials: FinancialOverview;
    facilities: FacilityStatistics;
    visitors: VisitorStatistics;
}

interface DashboardProps {
    statistics: DashboardStatistics;
}

function StatCard({
    title,
    value,
    description,
    icon: Icon,
    trend,
    className = '',
}: {
    title: string;
    value: number | string;
    description?: string;
    icon: React.ComponentType<{ className?: string }>;
    trend?: 'up' | 'down' | 'neutral';
    className?: string;
}) {
    return (
        <Card className={className}>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">{title}</CardTitle>
                <Icon className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
                <div className="text-2xl font-bold">{value}</div>
                {description && (
                    <p className="text-xs text-muted-foreground flex items-center gap-1">
                        {trend === 'up' && <TrendingUp className="h-3 w-3 text-green-500" />}
                        {trend === 'down' && <TrendingDown className="h-3 w-3 text-red-500" />}
                        {description}
                    </p>
                )}
            </CardContent>
        </Card>
    );
}

function AttentionItem({
    label,
    count,
    variant = 'default',
}: {
    label: string;
    count: number;
    variant?: 'default' | 'warning' | 'destructive';
}) {
    if (count === 0) return null;

    const badgeVariant = variant === 'warning' ? 'secondary' : variant === 'destructive' ? 'destructive' : 'default';

    return (
        <div className="flex items-center justify-between py-2">
            <span className="text-sm">{label}</span>
            <Badge variant={badgeVariant}>{count}</Badge>
        </div>
    );
}

function formatCurrency(amount: number): string {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
}

export default function Dashboard({ statistics }: DashboardProps) {
    const { units, requires_attention, leases, service_requests, marketplace, financials, facilities, visitors } =
        statistics;

    const totalAttentionItems =
        requires_attention.requests_approval +
        requires_attention.pending_complaints +
        requires_attention.expiring_leases +
        requires_attention.overdue_receipts;

    return (
        <>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
                {/* Requires Attention Section */}
                {totalAttentionItems > 0 && (
                    <Card className="border-orange-200 bg-orange-50 dark:border-orange-900 dark:bg-orange-950/20">
                        <CardHeader className="pb-3">
                            <div className="flex items-center gap-2">
                                <AlertTriangle className="h-5 w-5 text-orange-500" />
                                <CardTitle className="text-orange-700 dark:text-orange-400">
                                    Requires Attention
                                </CardTitle>
                                <Badge variant="secondary" className="ml-auto">
                                    {totalAttentionItems}
                                </Badge>
                            </div>
                        </CardHeader>
                        <CardContent className="pt-0">
                            <div className="grid gap-1 md:grid-cols-2 lg:grid-cols-4">
                                <AttentionItem
                                    label="Requests Awaiting Approval"
                                    count={requires_attention.requests_approval}
                                    variant="warning"
                                />
                                <AttentionItem
                                    label="Pending Complaints"
                                    count={requires_attention.pending_complaints}
                                    variant="destructive"
                                />
                                <AttentionItem
                                    label="Expiring Leases (30 days)"
                                    count={requires_attention.expiring_leases}
                                    variant="warning"
                                />
                                <AttentionItem
                                    label="Overdue Payments"
                                    count={requires_attention.overdue_receipts}
                                    variant="destructive"
                                />
                            </div>
                        </CardContent>
                    </Card>
                )}

                {/* Main Stats Grid */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <StatCard title="Total Units" value={units.total} icon={Building2} description="All properties" />
                    <StatCard title="Active Leases" value={leases.active} icon={FileText} description="Currently active" />
                    <StatCard
                        title="Open Service Requests"
                        value={service_requests.open + service_requests.in_progress}
                        icon={Wrench}
                        description={`${service_requests.pending_approval} pending approval`}
                    />
                    <StatCard
                        title="Net Income (MTD)"
                        value={formatCurrency(financials.net_income)}
                        icon={DollarSign}
                        trend={financials.net_income >= 0 ? 'up' : 'down'}
                        description="This month"
                    />
                </div>

                {/* Units & Leases Section */}
                <div className="grid gap-4 md:grid-cols-2">
                    {/* Unit Status */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Home className="h-5 w-5" />
                                Unit Status
                            </CardTitle>
                            <CardDescription>Distribution by status</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-3">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Vacant</span>
                                    <div className="flex items-center gap-2">
                                        <div className="h-2 w-20 rounded-full bg-muted">
                                            <div
                                                className="h-2 rounded-full bg-green-500"
                                                style={{
                                                    width: `${units.total > 0 ? (units.vacant / units.total) * 100 : 0}%`,
                                                }}
                                            />
                                        </div>
                                        <span className="text-sm font-medium w-8 text-right">{units.vacant}</span>
                                    </div>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Leased</span>
                                    <div className="flex items-center gap-2">
                                        <div className="h-2 w-20 rounded-full bg-muted">
                                            <div
                                                className="h-2 rounded-full bg-blue-500"
                                                style={{
                                                    width: `${units.total > 0 ? (units.leased / units.total) * 100 : 0}%`,
                                                }}
                                            />
                                        </div>
                                        <span className="text-sm font-medium w-8 text-right">{units.leased}</span>
                                    </div>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Sold</span>
                                    <div className="flex items-center gap-2">
                                        <div className="h-2 w-20 rounded-full bg-muted">
                                            <div
                                                className="h-2 rounded-full bg-purple-500"
                                                style={{
                                                    width: `${units.total > 0 ? (units.sold / units.total) * 100 : 0}%`,
                                                }}
                                            />
                                        </div>
                                        <span className="text-sm font-medium w-8 text-right">{units.sold}</span>
                                    </div>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Maintenance</span>
                                    <div className="flex items-center gap-2">
                                        <div className="h-2 w-20 rounded-full bg-muted">
                                            <div
                                                className="h-2 rounded-full bg-orange-500"
                                                style={{
                                                    width: `${units.total > 0 ? (units.maintenance / units.total) * 100 : 0}%`,
                                                }}
                                            />
                                        </div>
                                        <span className="text-sm font-medium w-8 text-right">{units.maintenance}</span>
                                    </div>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Reserved</span>
                                    <div className="flex items-center gap-2">
                                        <div className="h-2 w-20 rounded-full bg-muted">
                                            <div
                                                className="h-2 rounded-full bg-yellow-500"
                                                style={{
                                                    width: `${units.total > 0 ? (units.reserved / units.total) * 100 : 0}%`,
                                                }}
                                            />
                                        </div>
                                        <span className="text-sm font-medium w-8 text-right">{units.reserved}</span>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Lease Overview */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <FileText className="h-5 w-5" />
                                Lease Overview
                            </CardTitle>
                            <CardDescription>Current lease status</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                <div className="grid grid-cols-2 gap-4">
                                    <div className="rounded-lg border p-3">
                                        <div className="text-2xl font-bold text-green-600">{leases.active}</div>
                                        <div className="text-xs text-muted-foreground">Active</div>
                                    </div>
                                    <div className="rounded-lg border p-3">
                                        <div className="text-2xl font-bold text-orange-600">{leases.expiring_soon}</div>
                                        <div className="text-xs text-muted-foreground">Expiring Soon</div>
                                    </div>
                                    <div className="rounded-lg border p-3">
                                        <div className="text-2xl font-bold text-red-600">{leases.expired}</div>
                                        <div className="text-xs text-muted-foreground">Expired</div>
                                    </div>
                                    <div className="rounded-lg border p-3">
                                        <div className="text-2xl font-bold">{leases.total}</div>
                                        <div className="text-xs text-muted-foreground">Total</div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Service Requests & Financials */}
                <div className="grid gap-4 md:grid-cols-2">
                    {/* Service Requests */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Wrench className="h-5 w-5" />
                                Service Requests
                            </CardTitle>
                            <CardDescription>Request status breakdown</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-3">
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <div className="h-3 w-3 rounded-full bg-blue-500" />
                                        <span className="text-sm">Open</span>
                                    </div>
                                    <span className="font-medium">{service_requests.open}</span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <div className="h-3 w-3 rounded-full bg-yellow-500" />
                                        <span className="text-sm">In Progress</span>
                                    </div>
                                    <span className="font-medium">{service_requests.in_progress}</span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <div className="h-3 w-3 rounded-full bg-orange-500" />
                                        <span className="text-sm">Pending Approval</span>
                                    </div>
                                    <span className="font-medium">{service_requests.pending_approval}</span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <div className="h-3 w-3 rounded-full bg-green-500" />
                                        <span className="text-sm">Completed</span>
                                    </div>
                                    <span className="font-medium">{service_requests.completed}</span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <div className="h-3 w-3 rounded-full bg-gray-500" />
                                        <span className="text-sm">Closed</span>
                                    </div>
                                    <span className="font-medium">{service_requests.closed}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Financial Overview */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <DollarSign className="h-5 w-5" />
                                Financial Overview
                            </CardTitle>
                            <CardDescription>This month's summary</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                <div className="flex items-center justify-between border-b pb-2">
                                    <span className="text-sm">Monthly Income</span>
                                    <span className="font-medium text-green-600">
                                        {formatCurrency(financials.monthly_income)}
                                    </span>
                                </div>
                                <div className="flex items-center justify-between border-b pb-2">
                                    <span className="text-sm">Monthly Expenses</span>
                                    <span className="font-medium text-red-600">
                                        {formatCurrency(financials.monthly_expenses)}
                                    </span>
                                </div>
                                <div className="flex items-center justify-between border-b pb-2">
                                    <span className="text-sm font-medium">Net Income</span>
                                    <span
                                        className={`font-bold ${financials.net_income >= 0 ? 'text-green-600' : 'text-red-600'}`}
                                    >
                                        {formatCurrency(financials.net_income)}
                                    </span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Pending Payments</span>
                                    <span className="font-medium text-orange-600">
                                        {formatCurrency(financials.pending_payments)}
                                    </span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Overdue Payments</span>
                                    <span className="font-medium text-red-600">
                                        {formatCurrency(financials.overdue_payments)}
                                    </span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Marketplace, Facilities & Visitors */}
                <div className="grid gap-4 md:grid-cols-3">
                    {/* Marketplace */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Store className="h-5 w-5" />
                                Marketplace
                            </CardTitle>
                            <CardDescription>Listing activity</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-3">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Active Listings</span>
                                    <Badge variant="secondary">{marketplace.active_listings}</Badge>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Scheduled Visits</span>
                                    <Badge variant="secondary">{marketplace.scheduled_visits}</Badge>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Pending Offers</span>
                                    <Badge variant="secondary">{marketplace.pending_offers}</Badge>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Completed Sales</span>
                                    <Badge className="bg-green-100 text-green-800">{marketplace.completed_sales}</Badge>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Facility Bookings */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Calendar className="h-5 w-5" />
                                Facility Bookings
                            </CardTitle>
                            <CardDescription>Booking summary</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-3">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Today</span>
                                    <Badge variant="secondary">{facilities.today_bookings}</Badge>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Upcoming</span>
                                    <Badge variant="secondary">{facilities.upcoming_bookings}</Badge>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Pending Approval</span>
                                    <Badge variant="outline">{facilities.pending_approval}</Badge>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Total</span>
                                    <Badge>{facilities.total_bookings}</Badge>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Visitors */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Users className="h-5 w-5" />
                                Visitors
                            </CardTitle>
                            <CardDescription>Today's activity</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-3">
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <Clock className="h-4 w-4 text-muted-foreground" />
                                        <span className="text-sm">Expected Today</span>
                                    </div>
                                    <Badge variant="secondary">{visitors.expected_today}</Badge>
                                </div>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <CheckCircle className="h-4 w-4 text-green-500" />
                                        <span className="text-sm">Checked In</span>
                                    </div>
                                    <Badge className="bg-green-100 text-green-800">{visitors.checked_in_today}</Badge>
                                </div>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <AlertTriangle className="h-4 w-4 text-orange-500" />
                                        <span className="text-sm">Pending Approval</span>
                                    </div>
                                    <Badge variant="outline">{visitors.pending_approval}</Badge>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
    ],
};
