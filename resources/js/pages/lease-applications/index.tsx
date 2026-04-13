import { Head, Link, router } from "@inertiajs/react";
import {
    FileText,
    Plus,
    Search,
    Calendar,
    DollarSign,
    AlertCircle,
    CheckCircle,
    Clock,
    XCircle,
    Eye,
    Pause,
    Send,
} from "lucide-react";
import { useState } from "react";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";

interface Contact {
    id: number;
    name: string;
    email?: string;
}

interface Community {
    id: number;
    name: string;
}

interface Building {
    id: number;
    name: string;
}

interface Unit {
    id: number;
    name: string;
}

interface Application {
    id: number;
    application_number: string;
    status: string;
    applicant: Contact | null;
    applicant_name: string;
    applicant_email: string;
    community: Community | null;
    building: Building | null;
    units: Unit[];
    quoted_rental_amount: string | null;
    proposed_start_date: string | null;
    proposed_end_date: string | null;
    assigned_to: Contact | null;
    created_at: string;
}

interface ApplicationStatistics {
    total: number;
    draft: number;
    in_progress: number;
    review: number;
    approved: number;
    rejected: number;
    cancelled: number;
    pending: number;
    awaiting_conversion: number;
}

interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface StatusLabels {
    [key: string]: string;
}

interface ApplicationsIndexProps {
    applications: PaginatedData<Application>;
    statistics: ApplicationStatistics;
    filters: {
        status: string | null;
        search: string | null;
    };
    statuses: StatusLabels;
}

function getStatusBadge(status: string, statuses: StatusLabels) {
    const statusConfig: Record<
        string,
        {
            variant: "default" | "secondary" | "destructive" | "outline";
            className: string;
            icon: React.ReactNode;
        }
    > = {
        draft: {
            variant: "outline",
            className: "border-gray-400 text-gray-600",
            icon: <FileText className="h-3 w-3" />,
        },
        in_progress: {
            variant: "outline",
            className: "border-blue-500 text-blue-600",
            icon: <Clock className="h-3 w-3" />,
        },
        review: {
            variant: "secondary",
            className: "bg-yellow-100 text-yellow-800",
            icon: <AlertCircle className="h-3 w-3" />,
        },
        approved: {
            variant: "default",
            className: "bg-green-100 text-green-800",
            icon: <CheckCircle className="h-3 w-3" />,
        },
        rejected: {
            variant: "destructive",
            className: "bg-red-100 text-red-800",
            icon: <XCircle className="h-3 w-3" />,
        },
        cancelled: {
            variant: "secondary",
            className: "bg-gray-100 text-gray-800",
            icon: <XCircle className="h-3 w-3" />,
        },
        on_hold: {
            variant: "secondary",
            className: "bg-orange-100 text-orange-800",
            icon: <Pause className="h-3 w-3" />,
        },
    };

    const config = statusConfig[status] || {
        variant: "secondary" as const,
        className: "",
        icon: null,
    };
    const label = statuses[status] || status;

    return (
        <Badge variant={config.variant} className={config.className}>
            {config.icon}
            <span className="ml-1">{label}</span>
        </Badge>
    );
}

export default function ApplicationsIndex({
    applications,
    statistics,
    filters,
    statuses,
}: ApplicationsIndexProps) {
    const [search, setSearch] = useState(filters.search || "");

    const handleStatusFilter = (status: string) => {
        router.get(
            "/lease-applications",
            {
                status: status === "all" ? null : status,
                search: filters.search,
            },
            { preserveState: true },
        );
    };

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        router.get(
            "/lease-applications",
            { status: filters.status, search: search || null },
            { preserveState: true },
        );
    };

    return (
        <>
            <Head title="Lease Applications" />
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">
                            Lease Applications
                        </h1>
                        <p className="text-muted-foreground">
                            Manage lease applications and quotes
                        </p>
                    </div>
                    <Link href="/lease-applications/create">
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            New Application
                        </Button>
                    </Link>
                </div>

                {/* Statistics Cards */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Total Applications
                            </CardTitle>
                            <FileText className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {statistics.total}
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Pending
                            </CardTitle>
                            <Clock className="h-4 w-4 text-blue-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-blue-600">
                                {statistics.pending}
                            </div>
                            <p className="text-xs text-muted-foreground">
                                Awaiting action
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Approved
                            </CardTitle>
                            <CheckCircle className="h-4 w-4 text-green-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-green-600">
                                {statistics.approved}
                            </div>
                            <p className="text-xs text-muted-foreground">
                                {statistics.awaiting_conversion} awaiting
                                conversion
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Under Review
                            </CardTitle>
                            <Send className="h-4 w-4 text-yellow-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-yellow-600">
                                {statistics.review}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Filters */}
                <div className="flex items-center gap-4">
                    <form
                        onSubmit={handleSearch}
                        className="flex items-center gap-2"
                    >
                        <div className="relative">
                            <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input
                                placeholder="Search applications..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="pl-8 w-[250px]"
                            />
                        </div>
                        <Button type="submit" variant="secondary" size="sm">
                            Search
                        </Button>
                    </form>

                    <Select
                        value={filters.status || "all"}
                        onValueChange={handleStatusFilter}
                    >
                        <SelectTrigger className="w-[180px]">
                            <SelectValue placeholder="Filter by status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Statuses</SelectItem>
                            {Object.entries(statuses).map(([value, label]) => (
                                <SelectItem key={value} value={value}>
                                    {label}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>

                {/* Applications Table */}
                {applications.data.length === 0 ? (
                    <Card>
                        <CardContent className="flex flex-col items-center justify-center py-12">
                            <FileText className="h-12 w-12 text-muted-foreground/50" />
                            <h3 className="mt-4 text-lg font-medium">
                                No applications found
                            </h3>
                            <p className="mt-2 text-sm text-muted-foreground">
                                Get started by creating a new lease application.
                            </p>
                            <Link
                                href="/lease-applications/create"
                                className="mt-4"
                            >
                                <Button>
                                    <Plus className="mr-2 h-4 w-4" />
                                    Create Application
                                </Button>
                            </Link>
                        </CardContent>
                    </Card>
                ) : (
                    <Card>
                        <CardContent className="p-0">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Application #</TableHead>
                                        <TableHead>Applicant</TableHead>
                                        <TableHead>Property</TableHead>
                                        <TableHead>Status</TableHead>
                                        <TableHead>Period</TableHead>
                                        <TableHead>Amount</TableHead>
                                        <TableHead className="text-right">
                                            Actions
                                        </TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {applications.data.map((application) => (
                                        <TableRow key={application.id}>
                                            <TableCell className="font-medium">
                                                {application.application_number}
                                            </TableCell>
                                            <TableCell>
                                                <div>
                                                    <p>
                                                        {application.applicant
                                                            ?.name ||
                                                            application.applicant_name}
                                                    </p>
                                                    <p className="text-xs text-muted-foreground">
                                                        {application.applicant
                                                            ?.email ||
                                                            application.applicant_email}
                                                    </p>
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <div>
                                                    {application.community
                                                        ?.name ||
                                                        application.building
                                                            ?.name ||
                                                        "-"}
                                                    {application.units.length >
                                                        0 && (
                                                        <div className="flex flex-wrap gap-1 mt-1">
                                                            {application.units
                                                                .slice(0, 2)
                                                                .map((unit) => (
                                                                    <Badge
                                                                        key={
                                                                            unit.id
                                                                        }
                                                                        variant="secondary"
                                                                        className="text-xs"
                                                                    >
                                                                        {
                                                                            unit.name
                                                                        }
                                                                    </Badge>
                                                                ))}
                                                            {application.units
                                                                .length > 2 && (
                                                                <Badge
                                                                    variant="secondary"
                                                                    className="text-xs"
                                                                >
                                                                    +
                                                                    {application
                                                                        .units
                                                                        .length -
                                                                        2}
                                                                </Badge>
                                                            )}
                                                        </div>
                                                    )}
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                {getStatusBadge(
                                                    application.status,
                                                    statuses,
                                                )}
                                            </TableCell>
                                            <TableCell>
                                                {application.proposed_start_date ? (
                                                    <div className="flex items-center gap-1 text-sm">
                                                        <Calendar className="h-3 w-3" />
                                                        <span>
                                                            {new Date(
                                                                application.proposed_start_date,
                                                            ).toLocaleDateString()}
                                                            {application.proposed_end_date && (
                                                                <>
                                                                    {" "}
                                                                    -{" "}
                                                                    {new Date(
                                                                        application.proposed_end_date,
                                                                    ).toLocaleDateString()}
                                                                </>
                                                            )}
                                                        </span>
                                                    </div>
                                                ) : (
                                                    "-"
                                                )}
                                            </TableCell>
                                            <TableCell>
                                                {application.quoted_rental_amount ? (
                                                    <div className="flex items-center gap-1">
                                                        <DollarSign className="h-3 w-3" />
                                                        {parseFloat(
                                                            application.quoted_rental_amount,
                                                        ).toLocaleString()}
                                                    </div>
                                                ) : (
                                                    "-"
                                                )}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <Link
                                                    href={`/lease-applications/${application.id}`}
                                                >
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                    >
                                                        <Eye className="h-4 w-4" />
                                                    </Button>
                                                </Link>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </CardContent>
                    </Card>
                )}

                {/* Pagination */}
                {applications.last_page > 1 && (
                    <div className="flex items-center justify-center gap-2">
                        {Array.from(
                            { length: applications.last_page },
                            (_, i) => i + 1,
                        ).map((page) => (
                            <Button
                                key={page}
                                variant={
                                    page === applications.current_page
                                        ? "default"
                                        : "outline"
                                }
                                size="sm"
                                onClick={() =>
                                    router.get("/lease-applications", {
                                        page,
                                        status: filters.status,
                                        search: filters.search,
                                    })
                                }
                            >
                                {page}
                            </Button>
                        ))}
                    </div>
                )}
            </div>
        </>
    );
}

ApplicationsIndex.layout = {
    breadcrumbs: [{ title: "Lease Applications", href: "/lease-applications" }],
};
