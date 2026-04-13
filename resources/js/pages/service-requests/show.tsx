import { Head, Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { edit } from '@/routes/service-requests';

interface Props {
    request: any;
}

export default function Show({ request }: Props) {
    return (
        <AppLayout>
            <Head title={`Request #${request.request_number}`} />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">{request.title}</h1>
                        <p className="text-muted-foreground">Request #{request.request_number}</p>
                    </div>
                    <Link href={edit(request.id)}>
                        <Button variant="outline">Edit</Button>
                    </Link>
                </div>

                <div className="grid gap-6">
                    <div className="rounded-lg border bg-card p-6">
                        <h2 className="text-lg font-semibold mb-4">Request Details</h2>
                        <dl className="grid grid-cols-2 gap-4">
                            <div>
                                <dt className="text-sm font-medium text-muted-foreground">Status</dt>
                                <dd className="mt-1">{request.status.name}</dd>
                            </div>
                            <div>
                                <dt className="text-sm font-medium text-muted-foreground">Priority</dt>
                                <dd className="mt-1 capitalize">{request.priority}</dd>
                            </div>
                            <div className="col-span-2">
                                <dt className="text-sm font-medium text-muted-foreground">Description</dt>
                                <dd className="mt-1">{request.description || 'No description provided'}</dd>
                            </div>
                        </dl>
                    </div>

                    {/* TODO: Add more detail sections for history, attachments, etc. */}
                </div>
            </div>
        </AppLayout>
    );
}
