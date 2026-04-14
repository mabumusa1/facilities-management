import { Head, Link, router } from "@inertiajs/react";
import {
    AlertCircle,
    CheckCircle2,
    ChevronLeft,
    ChevronRight,
    ClipboardList,
    Plus,
    Search,
    Timer,
} from "lucide-react";
import { useMemo, useState } from "react";
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
    create,
    index as serviceRequestsIndex,
    show as serviceRequestsShow,
} from "@/routes/service-requests";

interface ServiceRequest {
    id: number;
    request_number: string;
    title: string;
    description?: string;
    priority: string;
    category?: {
        id: number;
        name: string;
    } | null;
    requester?: {
        id: number;
        name: string;
    } | null;
    status: {
        id: number | null;
        name: string;
        slug: string;
    };
    created_at: string;
}

interface Props {
    requests: {
        data: ServiceRequest[];
        links: {
            first: string | null;
            last: string | null;
            prev: string | null;
            next: string | null;
        };
        meta: {
            current_page: number;
            from: number | null;
            last_page: number;
            per_page: number;
            to: number | null;
            total: number;
        };
    };
    filters: {
        status?: string;
        category?: number;
        priority?: string;
        search?: string;
    };
    categories: Array<{
        id: number;
        name: string;
    }>;
}

function statusBadgeClass(slug: string | undefined): string {
    switch (slug) {
        case "request_completed":
        case "completed":
            return "bg-emerald-100 text-emerald-700";
        case "request_in_progress":
        case "in_progress":
            return "bg-blue-100 text-blue-700";
        case "request_rejected":
        case "cancelled":
            return "bg-red-100 text-red-700";
        default:
            return "bg-amber-100 text-amber-800";
    }
}

function priorityBadgeClass(priority: string): string {
    switch (priority) {
        case "urgent":
            return "bg-red-100 text-red-700";
        case "high":
            return "bg-orange-100 text-orange-700";
        case "medium":
            return "bg-blue-100 text-blue-700";
        default:
            return "bg-slate-100 text-slate-700";
    }
}

export default function Index({ requests, filters, categories }: Props) {
    const [search, setSearch] = useState(filters.search ?? "");
    const [status, setStatus] = useState(filters.status ?? "");
    const [category, setCategory] = useState(filters.category ? String(filters.category) : "");
    const [priority, setPriority] = useState(filters.priority ?? "");

    const pageStats = useMemo(() => {
        const visible = requests.data;
        const openCount = visible.filter(
            (item) => !["request_completed", "completed", "cancelled", "request_rejected"].includes(item.status?.slug),
        ).length;
        const highPriorityCount = visible.filter((item) => ["high", "urgent"].includes(item.priority)).length;
        const completedCount = visible.filter((item) => ["request_completed", "completed"].includes(item.status?.slug)).length;

        return {
            openCount,
            highPriorityCount,
            completedCount,
        };
    }, [requests.data]);

    const applyFilters = (override: Partial<{ search: string; status: string; category: string; priority: string }> = {}) => {
        const nextSearch = override.search ?? search;
        const nextStatus = override.status ?? status;
        const nextCategory = override.category ?? category;
        const nextPriority = override.priority ?? priority;

        router.get(
            serviceRequestsIndex(),
            {
                search: nextSearch || undefined,
                status: nextStatus || undefined,
                category: nextCategory ? Number(nextCategory) : undefined,
                priority: nextPriority || undefined,
            },
            { preserveState: true, replace: true },
        );
    };

    const clearFilters = () => {
        setSearch("");
        setStatus("");
        setCategory("");
        setPriority("");
        router.get(serviceRequestsIndex(), {}, { preserveState: true, replace: true });
    };

    return (
        <>
            <Head title="Service Requests" />

            <div className="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Service Requests</h1>
                        <p className="text-muted-foreground">
                            Manage service and maintenance requests
                        </p>
                    </div>
                    <Link href={create()}>
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            New Request
                        </Button>
                    </Link>
                </div>

                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Requests</CardTitle>
                            <ClipboardList className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{requests.meta.total}</div>
                            <p className="text-xs text-muted-foreground">Across all pages</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Open (This Page)</CardTitle>
                            <Timer className="h-4 w-4 text-blue-600" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-blue-700">{pageStats.openCount}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">High Priority</CardTitle>
                            <AlertCircle className="h-4 w-4 text-orange-600" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-orange-700">{pageStats.highPriorityCount}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Completed</CardTitle>
                            <CheckCircle2 className="h-4 w-4 text-emerald-600" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-emerald-700">{pageStats.completedCount}</div>
                        </CardContent>
                    </Card>
                </div>

                <Card>
                    <CardContent className="pt-6">
                        <div className="flex flex-wrap items-center gap-3">
                            <div className="relative min-w-[240px] flex-1">
                                <Search className="text-muted-foreground absolute top-2.5 left-2.5 h-4 w-4" />
                                <Input
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    onKeyDown={(e) => {
                                        if (e.key === "Enter") {
                                            applyFilters({ search: search.trim() });
                                        }
                                    }}
                                    placeholder="Search by request #, title, or description"
                                    className="pl-8"
                                />
                            </div>

                            <Select
                                value={status}
                                onValueChange={(value) => {
                                    setStatus(value);
                                    applyFilters({ status: value });
                                }}
                            >
                                <SelectTrigger className="w-[170px]">
                                    <SelectValue placeholder="Status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="">All statuses</SelectItem>
                                    <SelectItem value="request_new">New</SelectItem>
                                    <SelectItem value="request_in_progress">In progress</SelectItem>
                                    <SelectItem value="request_completed">Completed</SelectItem>
                                    <SelectItem value="request_rejected">Rejected</SelectItem>
                                </SelectContent>
                            </Select>

                            <Select
                                value={category}
                                onValueChange={(value) => {
                                    setCategory(value);
                                    applyFilters({ category: value });
                                }}
                            >
                                <SelectTrigger className="w-[180px]">
                                    <SelectValue placeholder="Category" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="">All categories</SelectItem>
                                    {categories.map((item) => (
                                        <SelectItem key={item.id} value={String(item.id)}>
                                            {item.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>

                            <Select
                                value={priority}
                                onValueChange={(value) => {
                                    setPriority(value);
                                    applyFilters({ priority: value });
                                }}
                            >
                                <SelectTrigger className="w-[150px]">
                                    <SelectValue placeholder="Priority" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="">All priorities</SelectItem>
                                    <SelectItem value="low">Low</SelectItem>
                                    <SelectItem value="medium">Medium</SelectItem>
                                    <SelectItem value="high">High</SelectItem>
                                    <SelectItem value="urgent">Urgent</SelectItem>
                                </SelectContent>
                            </Select>

                            <Button onClick={() => applyFilters({ search: search.trim() })}>
                                Search
                            </Button>
                            <Button variant="outline" onClick={clearFilters}>
                                Clear
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>
                            {requests.meta.total} {requests.meta.total === 1 ? "Request" : "Requests"}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        {requests.data.length === 0 ? (
                            <div className="py-12 text-center">
                                <p className="text-muted-foreground">No service requests found.</p>
                                <p className="text-muted-foreground text-sm">
                                    Try adjusting filters or create a new request.
                                </p>
                            </div>
                        ) : (
                            <div className="space-y-2">
                                {requests.data.map((request) => (
                                    <Link
                                        key={request.id}
                                        href={serviceRequestsShow({ service_request: request.id })}
                                        className="hover:bg-muted/50 flex items-start justify-between gap-4 rounded-lg border p-4 transition-colors"
                                    >
                                        <div className="min-w-0 space-y-2">
                                            <div className="flex flex-wrap items-center gap-2">
                                                <p className="font-medium">{request.title}</p>
                                                <Badge className={statusBadgeClass(request.status?.slug)}>
                                                    {request.status?.name ?? "Unknown"}
                                                </Badge>
                                                <Badge className={priorityBadgeClass(request.priority)}>
                                                    {request.priority}
                                                </Badge>
                                            </div>

                                            <div className="text-muted-foreground flex flex-wrap items-center gap-x-3 gap-y-1 text-sm">
                                                <span>#{request.request_number}</span>
                                                {request.category?.name && <span>{request.category.name}</span>}
                                                {request.requester?.name && <span>By {request.requester.name}</span>}
                                                <span>{request.created_at}</span>
                                            </div>

                                            {request.description && (
                                                <p className="text-muted-foreground line-clamp-2 text-sm">
                                                    {request.description}
                                                </p>
                                            )}
                                        </div>

                                        <Button variant="outline" size="sm">
                                            View
                                        </Button>
                                    </Link>
                                ))}
                            </div>
                        )}

                        <div className="mt-5 flex items-center justify-between border-t pt-4">
                            <p className="text-muted-foreground text-sm">
                                Showing {requests.meta.from ?? 0} to {requests.meta.to ?? 0} of {requests.meta.total}
                            </p>
                            <div className="flex items-center gap-2">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    disabled={!requests.links.prev}
                                    onClick={() => {
                                        if (requests.links.prev) {
                                            router.visit(requests.links.prev, { preserveState: true, replace: true });
                                        }
                                    }}
                                >
                                    <ChevronLeft className="mr-1 h-4 w-4" />
                                    Prev
                                </Button>
                                <span className="text-muted-foreground text-sm">
                                    Page {requests.meta.current_page} of {requests.meta.last_page}
                                </span>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    disabled={!requests.links.next}
                                    onClick={() => {
                                        if (requests.links.next) {
                                            router.visit(requests.links.next, { preserveState: true, replace: true });
                                        }
                                    }}
                                >
                                    Next
                                    <ChevronRight className="ml-1 h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

Index.layout = {
    breadcrumbs: [
        { title: "Service Requests", href: serviceRequestsIndex() },
    ],
};
