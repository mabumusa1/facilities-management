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

interface Unit {
    id: number;
    name: string;
}

interface Contact {
    id: number;
    name: string;
}

interface Status {
    id: number;
    name: string;
}

interface Lease {
    id: number;
    contract_number: string | null;
    tenant: Contact | null;
    units: Unit[];
    status: Status | null;
    start_date: string;
    end_date: string;
    rental_total_amount: string;
    is_renew: boolean;
    created_at: string;
}

interface LeaseStatistics {
    total: number;
    active: number;
    new: number;
    expired: number;
    cancelled: number;
    closed: number;
    expiring_soon: number;
    total_rental_amount: number;
}

interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface LeasesIndexProps {
    leases: PaginatedData<Lease>;
    statistics: LeaseStatistics;
    filters: {
        status: string | null;
        search: string | null;
    };
}

function getStatusBadge(status: Status | null) {
    if (!status) {
        return <Badge variant="secondary">Unknown</Badge>;
    }

    const statusConfig: Record<
        number,
        {
            variant: "default" | "secondary" | "destructive" | "outline";
            className: string;
            icon: React.ReactNode;
        }
    > = {
        30: {
            variant: "outline",
            className: "border-blue-500 text-blue-600",
            icon: <Clock className="h-3 w-3" />,
        },
        31: {
            variant: "default",
            className: "bg-green-100 text-green-800",
            icon: <CheckCircle className="h-3 w-3" />,
        },
        32: {
            variant: "secondary",
            className: "bg-orange-100 text-orange-800",
            icon: <AlertCircle className="h-3 w-3" />,
        },
        33: {
            variant: "destructive",
            className: "bg-red-100 text-red-800",
            icon: <XCircle className="h-3 w-3" />,
        },
        34: {
            variant: "secondary",
            className: "bg-gray-100 text-gray-800",
            icon: <FileText className="h-3 w-3" />,
        },
    };

    const config = statusConfig[status.id] || {
        variant: "secondary" as const,
        className: "",
        icon: null,
    };

    return (
        <Badge variant={config.variant} className={config.className}>
            {config.icon}
            <span className="ml-1">{status.name}</span>
        </Badge>
    );
}

export default function LeasesIndex({
    leases,
    statistics,
    filters,
}: LeasesIndexProps) {
    const [search, setSearch] = useState(filters.search || "");

    const handleStatusFilter = (status: string) => {
        router.get(
            "/leases",
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
            "/leases",
            { status: filters.status, search: search || null },
            { preserveState: true },
        );
    };

    return (
        <>
            <Head title="Leases" />
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">
                            Leases
                        </h1>
                        <p className="text-muted-foreground">
                            Manage lease contracts and agreements
                        </p>
                    </div>
                    <Link href="/leases/create">
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            New Lease
                        </Button>
                    </Link>
                </div>

                {/* Statistics Cards */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Total Leases
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
                                Active
                            </CardTitle>
                            <CheckCircle className="h-4 w-4 text-green-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-green-600">
                                {statistics.active}
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Expiring Soon
                            </CardTitle>
                            <AlertCircle className="h-4 w-4 text-orange-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-orange-600">
                                {statistics.expiring_soon}
                            </div>
                            <p className="text-xs text-muted-foreground">
                                Within 30 days
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Total Value
                            </CardTitle>
                            <DollarSign className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                $
                                {statistics.total_rental_amount.toLocaleString()}
                            </div>
                            <p className="text-xs text-muted-foreground">
                                Active leases
                            </p>
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
                                placeholder="Search leases..."
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
                            <SelectItem value="new">New</SelectItem>
                            <SelectItem value="active">Active</SelectItem>
                            <SelectItem value="expired">Expired</SelectItem>
                            <SelectItem value="cancelled">Cancelled</SelectItem>
                            <SelectItem value="closed">Closed</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                {/* Leases Table */}
                {leases.data.length === 0 ? (
                    <Card>
                        <CardContent className="flex flex-col items-center justify-center py-12">
                            <FileText className="h-12 w-12 text-muted-foreground/50" />
                            <h3 className="mt-4 text-lg font-medium">
                                No leases found
                            </h3>
                            <p className="mt-2 text-sm text-muted-foreground">
                                Get started by creating your first lease.
                            </p>
                            <Link href="/leases/create" className="mt-4">
                                <Button>
                                    <Plus className="mr-2 h-4 w-4" />
                                    Create Lease
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
                                        <TableHead>Contract #</TableHead>
                                        <TableHead>Tenant</TableHead>
                                        <TableHead>Units</TableHead>
                                        <TableHead>Status</TableHead>
                                        <TableHead>Period</TableHead>
                                        <TableHead>Amount</TableHead>
                                        <TableHead className="text-right">
                                            Actions
                                        </TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {leases.data.map((lease) => (
                                        <TableRow key={lease.id}>
                                            <TableCell className="font-medium">
                                                {lease.contract_number ||
                                                    `#${lease.id}`}
                                                {lease.is_renew && (
                                                    <Badge
                                                        variant="outline"
                                                        className="ml-2 text-xs"
                                                    >
                                                        Renewed
                                                    </Badge>
                                                )}
                                            </TableCell>
                                            <TableCell>
                                                {lease.tenant?.name || "-"}
                                            </TableCell>
                                            <TableCell>
                                                {lease.units.length > 0 ? (
                                                    <div className="flex flex-wrap gap-1">
                                                        {lease.units
                                                            .slice(0, 2)
                                                            .map((unit) => (
                                                                <Badge
                                                                    key={
                                                                        unit.id
                                                                    }
                                                                    variant="secondary"
                                                                    className="text-xs"
                                                                >
                                                                    {unit.name}
                                                                </Badge>
                                                            ))}
                                                        {lease.units.length >
                                                            2 && (
                                                            <Badge
                                                                variant="secondary"
                                                                className="text-xs"
                                                            >
                                                                +
                                                                {lease.units
                                                                    .length - 2}
                                                            </Badge>
                                                        )}
                                                    </div>
                                                ) : (
                                                    "-"
                                                )}
                                            </TableCell>
                                            <TableCell>
                                                {getStatusBadge(lease.status)}
                                            </TableCell>
                                            <TableCell>
                                                <div className="flex items-center gap-1 text-sm">
                                                    <Calendar className="h-3 w-3" />
                                                    <span>
                                                        {new Date(
                                                            lease.start_date,
                                                        ).toLocaleDateString()}{" "}
                                                        -{" "}
                                                        {new Date(
                                                            lease.end_date,
                                                        ).toLocaleDateString()}
                                                    </span>
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                $
                                                {parseFloat(
                                                    lease.rental_total_amount,
                                                ).toLocaleString()}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <Link
                                                    href={`/leases/${lease.id}`}
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
                {leases.last_page > 1 && (
                    <div className="flex items-center justify-center gap-2">
                        {Array.from(
                            { length: leases.last_page },
                            (_, i) => i + 1,
                        ).map((page) => (
                            <Button
                                key={page}
                                variant={
                                    page === leases.current_page
                                        ? "default"
                                        : "outline"
                                }
                                size="sm"
                                onClick={() =>
                                    router.get("/leases", {
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

LeasesIndex.layout = {
    breadcrumbs: [{ title: "Leases", href: "/leases" }],
};
