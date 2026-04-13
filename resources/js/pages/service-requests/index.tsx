import { Head, Link } from "@inertiajs/react";
import { Plus } from "lucide-react";
import { Button } from "@/components/ui/button";
import AppLayout from "@/layouts/app-layout";
import { create } from "@/routes/service-requests";

interface ServiceRequest {
    id: number;
    request_number: string;
    title: string;
    priority: string;
    status: {
        name: string;
        slug: string;
    };
    created_at: string;
}

interface Props {
    requests: {
        data: ServiceRequest[];
        links: any;
        meta: any;
    };
    filters: {
        status?: string;
        category?: number;
        priority?: string;
        search?: string;
    };
    categories: any[];
}

export default function Index({
    requests,
    filters: _filters,
    categories: _categories,
}: Props) {
    return (
        <AppLayout>
            <Head title="Service Requests" />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">Service Requests</h1>
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

                {/* TODO: Add filters, search, and table components */}
                <div className="rounded-lg border bg-card">
                    <div className="p-6">
                        <div className="space-y-4">
                            {requests.data.length === 0 ? (
                                <div className="text-center py-12">
                                    <p className="text-muted-foreground">
                                        No service requests found
                                    </p>
                                </div>
                            ) : (
                                <div className="space-y-2">
                                    {requests.data.map((request) => (
                                        <div
                                            key={request.id}
                                            className="flex items-center justify-between rounded-lg border p-4"
                                        >
                                            <div>
                                                <h3 className="font-medium">
                                                    {request.title}
                                                </h3>
                                                <p className="text-sm text-muted-foreground">
                                                    #{request.request_number} ·{" "}
                                                    {request.status.name} ·
                                                    Priority: {request.priority}
                                                </p>
                                            </div>
                                            <Link
                                                href={`/service-requests/${request.id}`}
                                            >
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                >
                                                    View
                                                </Button>
                                            </Link>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
