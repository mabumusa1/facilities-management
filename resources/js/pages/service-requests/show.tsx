import { Head, Link } from "@inertiajs/react";
import {
    AlertCircle,
    Calendar,
    CircleCheck,
    Clock,
    FileText,
    User,
    Wrench,
} from "lucide-react";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { edit, index as serviceRequestsIndex } from "@/routes/service-requests";

interface RequestStatus {
    name?: string;
    slug?: string;
}

interface RequestPerson {
    name?: string;
    email?: string;
    phone?: string;
}

interface RequestLocation {
    name?: string;
}

interface RequestHistoryItem {
    id: number;
    from_status?: {
        name?: string;
    } | null;
    to_status?: {
        name?: string;
    } | null;
    changed_by?: {
        name?: string;
    } | null;
    notes?: string | null;
    created_at: string;
}

interface ServiceRequestShow {
    id: number;
    request_number: string;
    title: string;
    description?: string | null;
    priority: string;
    status?: RequestStatus | null;
    category?: RequestLocation | null;
    subcategory?: RequestLocation | null;
    requester?: RequestPerson | null;
    professional?: RequestPerson | null;
    community?: RequestLocation | null;
    building?: RequestLocation | null;
    unit?: {
        unit_number?: string;
    } | null;
    scheduled_date?: string | null;
    scheduled_time?: string | null;
    created_at: string;
    updated_at: string;
    notes?: string | null;
    admin_notes?: string | null;
    professional_notes?: string | null;
    state_history?: RequestHistoryItem[];
}

interface Props {
    request: ServiceRequestShow;
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

export default function Show({ request }: Props) {
    const history = request.state_history ?? [];

    return (
        <>
            <Head title={`Request #${request.request_number}`} />

            <div className="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <div className="mb-2 flex flex-wrap items-center gap-2">
                            <Badge className={statusBadgeClass(request.status?.slug)}>
                                {request.status?.name ?? "Unknown"}
                            </Badge>
                            <Badge className={priorityBadgeClass(request.priority)}>
                                {request.priority}
                            </Badge>
                        </div>
                        <h1 className="text-2xl font-bold tracking-tight">{request.title}</h1>
                        <p className="text-muted-foreground">Request #{request.request_number}</p>
                    </div>
                    <Link href={edit({ service_request: request.id })}>
                        <Button variant="outline">Edit</Button>
                    </Link>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    <div className="space-y-6 lg:col-span-2">
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <FileText className="h-4 w-4" />
                                    Request Details
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <p className="text-muted-foreground text-sm">Category</p>
                                        <p className="font-medium">{request.category?.name ?? "-"}</p>
                                    </div>
                                    <div>
                                        <p className="text-muted-foreground text-sm">Subcategory</p>
                                        <p className="font-medium">{request.subcategory?.name ?? "-"}</p>
                                    </div>
                                    <div>
                                        <p className="text-muted-foreground text-sm">Created</p>
                                        <p className="font-medium">{request.created_at}</p>
                                    </div>
                                    <div>
                                        <p className="text-muted-foreground text-sm">Updated</p>
                                        <p className="font-medium">{request.updated_at}</p>
                                    </div>
                                </div>

                                <div>
                                    <p className="text-muted-foreground text-sm">Description</p>
                                    <p className="mt-1 leading-relaxed">
                                        {request.description || "No description provided"}
                                    </p>
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Clock className="h-4 w-4" />
                                    State History
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                {history.length === 0 ? (
                                    <p className="text-muted-foreground text-sm">No state transitions recorded yet.</p>
                                ) : (
                                    <div className="space-y-3">
                                        {history.map((item) => (
                                            <div key={item.id} className="rounded-lg border p-3">
                                                <div className="flex flex-wrap items-center gap-2 text-sm">
                                                    <Badge variant="outline">{item.from_status?.name ?? "Start"}</Badge>
                                                    <span className="text-muted-foreground">to</span>
                                                    <Badge variant="outline">{item.to_status?.name ?? "Unknown"}</Badge>
                                                </div>
                                                <p className="text-muted-foreground mt-1 text-xs">
                                                    {item.created_at}
                                                    {item.changed_by?.name ? ` • by ${item.changed_by.name}` : ""}
                                                </p>
                                                {item.notes && (
                                                    <p className="mt-2 text-sm">{item.notes}</p>
                                                )}
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    <div className="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2 text-base">
                                    <User className="h-4 w-4" />
                                    People
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4 text-sm">
                                <div>
                                    <p className="text-muted-foreground">Requester</p>
                                    <p className="font-medium">{request.requester?.name ?? "-"}</p>
                                </div>
                                <div>
                                    <p className="text-muted-foreground">Assigned Professional</p>
                                    <p className="font-medium">{request.professional?.name ?? "Not assigned"}</p>
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2 text-base">
                                    <Wrench className="h-4 w-4" />
                                    Location & Schedule
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4 text-sm">
                                <div>
                                    <p className="text-muted-foreground">Community</p>
                                    <p className="font-medium">{request.community?.name ?? "-"}</p>
                                </div>
                                <div>
                                    <p className="text-muted-foreground">Building / Unit</p>
                                    <p className="font-medium">
                                        {request.building?.name ?? "-"}
                                        {request.unit?.unit_number ? ` • ${request.unit.unit_number}` : ""}
                                    </p>
                                </div>
                                <div>
                                    <p className="text-muted-foreground">Scheduled</p>
                                    <p className="font-medium flex items-center gap-1">
                                        <Calendar className="h-3.5 w-3.5" />
                                        {request.scheduled_date ?? "Not scheduled"}
                                        {request.scheduled_time ? ` at ${request.scheduled_time}` : ""}
                                    </p>
                                </div>
                            </CardContent>
                        </Card>

                        {(request.notes || request.admin_notes || request.professional_notes) && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2 text-base">
                                        <AlertCircle className="h-4 w-4" />
                                        Notes
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-3 text-sm">
                                    {request.notes && (
                                        <div>
                                            <p className="text-muted-foreground">Request Notes</p>
                                            <p>{request.notes}</p>
                                        </div>
                                    )}
                                    {request.admin_notes && (
                                        <div>
                                            <p className="text-muted-foreground">Admin Notes</p>
                                            <p>{request.admin_notes}</p>
                                        </div>
                                    )}
                                    {request.professional_notes && (
                                        <div>
                                            <p className="text-muted-foreground">Professional Notes</p>
                                            <p>{request.professional_notes}</p>
                                        </div>
                                    )}
                                </CardContent>
                            </Card>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}

Show.layout = {
    breadcrumbs: [
        { title: "Service Requests", href: serviceRequestsIndex() },
        { title: "Details", href: "#" },
    ],
};
