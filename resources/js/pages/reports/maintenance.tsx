import { Head } from "@inertiajs/react";
import {
    Wrench,
    Clock,
    AlertTriangle,
    CheckCircle,
    TrendingUp,
    BarChart3,
    PieChart,
} from "lucide-react";
import { Badge } from "@/components/ui/badge";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";

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

interface CategoryItem {
    name: string;
    slug: string;
    count: number;
}

interface CategoryReport {
    categories: CategoryItem[];
    total: number;
}

interface PriorityReport {
    low: number;
    medium: number;
    high: number;
    urgent: number;
}

interface TrendItem {
    year: number;
    month: number;
    month_name: string;
    total: number;
    high_priority: number;
}

interface MaintenanceReportsProps {
    statistics: MaintenanceStatistics;
    categoryReport: CategoryReport;
    priorityReport: PriorityReport;
    trendReport: TrendItem[];
}

export default function MaintenanceReports({
    statistics,
    categoryReport,
    priorityReport,
    trendReport,
}: MaintenanceReportsProps) {
    const totalPriority =
        priorityReport.low +
        priorityReport.medium +
        priorityReport.high +
        priorityReport.urgent;

    const getPriorityPercentage = (value: number) => {
        return totalPriority > 0
            ? ((value / totalPriority) * 100).toFixed(1)
            : "0";
    };

    const maxTrendValue = Math.max(...trendReport.map((t) => t.total), 1);

    return (
        <>
            <Head title="Maintenance Reports" />
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">
                            Maintenance Reports
                        </h1>
                        <p className="text-muted-foreground">
                            Service request analytics and trends
                        </p>
                    </div>
                </div>

                {/* Key Statistics */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Total Requests
                            </CardTitle>
                            <Wrench className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {statistics.total_requests}
                            </div>
                            <p className="text-xs text-muted-foreground">
                                {statistics.requests_this_month} this month
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Open Requests
                            </CardTitle>
                            <Clock className="h-4 w-4 text-blue-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-blue-600">
                                {statistics.open_requests}
                            </div>
                            <p className="text-xs text-muted-foreground">
                                {statistics.in_progress_requests} in progress
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                High Priority
                            </CardTitle>
                            <AlertTriangle className="h-4 w-4 text-red-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-red-600">
                                {statistics.high_priority_count}
                            </div>
                            <p className="text-xs text-muted-foreground">
                                Requiring immediate attention
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Avg Resolution
                            </CardTitle>
                            <CheckCircle className="h-4 w-4 text-green-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {statistics.average_resolution_days}
                            </div>
                            <p className="text-xs text-muted-foreground">
                                Days to complete
                            </p>
                        </CardContent>
                    </Card>
                </div>

                {/* Status Breakdown & Category Distribution */}
                <div className="grid gap-6 md:grid-cols-2">
                    {/* Status Breakdown */}
                    <Card>
                        <CardHeader>
                            <div className="flex items-center gap-2">
                                <BarChart3 className="h-5 w-5" />
                                <CardTitle>Request Status Breakdown</CardTitle>
                            </div>
                            <CardDescription>
                                Current status of all service requests
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <div className="h-3 w-3 rounded-full bg-blue-500" />
                                        <span className="text-sm">Open</span>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <div className="h-2 w-32 rounded-full bg-muted">
                                            <div
                                                className="h-2 rounded-full bg-blue-500"
                                                style={{
                                                    width: `${statistics.total_requests > 0 ? (statistics.open_requests / statistics.total_requests) * 100 : 0}%`,
                                                }}
                                            />
                                        </div>
                                        <span className="w-8 text-right text-sm font-medium">
                                            {statistics.open_requests}
                                        </span>
                                    </div>
                                </div>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <div className="h-3 w-3 rounded-full bg-yellow-500" />
                                        <span className="text-sm">
                                            In Progress
                                        </span>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <div className="h-2 w-32 rounded-full bg-muted">
                                            <div
                                                className="h-2 rounded-full bg-yellow-500"
                                                style={{
                                                    width: `${statistics.total_requests > 0 ? (statistics.in_progress_requests / statistics.total_requests) * 100 : 0}%`,
                                                }}
                                            />
                                        </div>
                                        <span className="w-8 text-right text-sm font-medium">
                                            {statistics.in_progress_requests}
                                        </span>
                                    </div>
                                </div>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <div className="h-3 w-3 rounded-full bg-green-500" />
                                        <span className="text-sm">
                                            Completed
                                        </span>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <div className="h-2 w-32 rounded-full bg-muted">
                                            <div
                                                className="h-2 rounded-full bg-green-500"
                                                style={{
                                                    width: `${statistics.total_requests > 0 ? (statistics.completed_requests / statistics.total_requests) * 100 : 0}%`,
                                                }}
                                            />
                                        </div>
                                        <span className="w-8 text-right text-sm font-medium">
                                            {statistics.completed_requests}
                                        </span>
                                    </div>
                                </div>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <div className="h-3 w-3 rounded-full bg-gray-500" />
                                        <span className="text-sm">Closed</span>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <div className="h-2 w-32 rounded-full bg-muted">
                                            <div
                                                className="h-2 rounded-full bg-gray-500"
                                                style={{
                                                    width: `${statistics.total_requests > 0 ? (statistics.closed_requests / statistics.total_requests) * 100 : 0}%`,
                                                }}
                                            />
                                        </div>
                                        <span className="w-8 text-right text-sm font-medium">
                                            {statistics.closed_requests}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Priority Distribution */}
                    <Card>
                        <CardHeader>
                            <div className="flex items-center gap-2">
                                <PieChart className="h-5 w-5" />
                                <CardTitle>Priority Distribution</CardTitle>
                            </div>
                            <CardDescription>
                                Requests by priority level
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <Badge className="bg-red-100 text-red-800">
                                            Urgent
                                        </Badge>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <span className="text-sm font-medium">
                                            {priorityReport.urgent}
                                        </span>
                                        <span className="text-xs text-muted-foreground">
                                            (
                                            {getPriorityPercentage(
                                                priorityReport.urgent,
                                            )}
                                            %)
                                        </span>
                                    </div>
                                </div>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <Badge variant="destructive">
                                            High
                                        </Badge>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <span className="text-sm font-medium">
                                            {priorityReport.high}
                                        </span>
                                        <span className="text-xs text-muted-foreground">
                                            (
                                            {getPriorityPercentage(
                                                priorityReport.high,
                                            )}
                                            %)
                                        </span>
                                    </div>
                                </div>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <Badge className="bg-yellow-100 text-yellow-800">
                                            Medium
                                        </Badge>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <span className="text-sm font-medium">
                                            {priorityReport.medium}
                                        </span>
                                        <span className="text-xs text-muted-foreground">
                                            (
                                            {getPriorityPercentage(
                                                priorityReport.medium,
                                            )}
                                            %)
                                        </span>
                                    </div>
                                </div>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <Badge variant="secondary">Low</Badge>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <span className="text-sm font-medium">
                                            {priorityReport.low}
                                        </span>
                                        <span className="text-xs text-muted-foreground">
                                            (
                                            {getPriorityPercentage(
                                                priorityReport.low,
                                            )}
                                            %)
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Category Distribution */}
                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-2">
                            <Wrench className="h-5 w-5" />
                            <CardTitle>Requests by Category</CardTitle>
                        </div>
                        <CardDescription>
                            Distribution of service requests across categories
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {categoryReport.categories.length > 0 ? (
                            <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                                {categoryReport.categories.map((category) => (
                                    <div
                                        key={category.slug}
                                        className="flex items-center justify-between rounded-lg border p-4"
                                    >
                                        <span className="text-sm font-medium">
                                            {category.name}
                                        </span>
                                        <Badge variant="secondary">
                                            {category.count}
                                        </Badge>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="py-8 text-center text-muted-foreground">
                                No category data available
                            </div>
                        )}
                    </CardContent>
                </Card>

                {/* Monthly Trend */}
                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-2">
                            <TrendingUp className="h-5 w-5" />
                            <CardTitle>Monthly Request Trend</CardTitle>
                        </div>
                        <CardDescription>
                            Service request volume over the last 12 months
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {trendReport.length > 0 ? (
                            <div className="space-y-4">
                                <div className="flex items-end gap-2 h-48">
                                    {trendReport.map((item) => (
                                        <div
                                            key={`${item.year}-${item.month}`}
                                            className="flex-1 flex flex-col items-center gap-1"
                                        >
                                            <div
                                                className="w-full flex flex-col items-center gap-1"
                                                style={{ height: "180px" }}
                                            >
                                                <div
                                                    className="w-full rounded-t bg-blue-500 transition-all"
                                                    style={{
                                                        height: `${(item.total / maxTrendValue) * 100}%`,
                                                        minHeight:
                                                            item.total > 0
                                                                ? "4px"
                                                                : "0",
                                                    }}
                                                    title={`Total: ${item.total}`}
                                                />
                                            </div>
                                            <span className="text-xs text-muted-foreground rotate-45 origin-left whitespace-nowrap">
                                                {item.month_name}
                                            </span>
                                        </div>
                                    ))}
                                </div>
                                <div className="flex items-center justify-center gap-6 pt-8">
                                    <div className="flex items-center gap-2">
                                        <div className="h-3 w-3 rounded bg-blue-500" />
                                        <span className="text-sm">
                                            Total Requests
                                        </span>
                                    </div>
                                </div>
                            </div>
                        ) : (
                            <div className="py-8 text-center text-muted-foreground">
                                No trend data available
                            </div>
                        )}
                    </CardContent>
                </Card>

                {/* Summary Stats */}
                <div className="grid gap-4 md:grid-cols-3">
                    <Card>
                        <CardContent className="pt-6">
                            <div className="text-center">
                                <div className="text-3xl font-bold">
                                    {statistics.requests_this_month}
                                </div>
                                <div className="text-sm text-muted-foreground">
                                    Requests This Month
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent className="pt-6">
                            <div className="text-center">
                                <div className="text-3xl font-bold">
                                    {statistics.requests_this_year}
                                </div>
                                <div className="text-sm text-muted-foreground">
                                    Requests This Year
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent className="pt-6">
                            <div className="text-center">
                                <div className="text-3xl font-bold">
                                    {statistics.completed_requests +
                                        statistics.closed_requests}
                                </div>
                                <div className="text-sm text-muted-foreground">
                                    Resolved Requests
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}

MaintenanceReports.layout = {
    breadcrumbs: [
        {
            title: "Reports",
            href: "/reports",
        },
        {
            title: "Maintenance Reports",
            href: "/reports/maintenance",
        },
    ],
};
