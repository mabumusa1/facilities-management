import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';

interface Props {
    request: any;
    categories: any[];
    communities: any[];
    contacts: any[];
}

export default function Edit({ request, categories: _categories, communities: _communities, contacts: _contacts }: Props) {
    return (
        <AppLayout>
            <Head title={`Edit Request #${request.request_number}`} />

            <div className="space-y-6">
                <div>
                    <h1 className="text-3xl font-bold">Edit Service Request</h1>
                    <p className="text-muted-foreground">Request #{request.request_number}</p>
                </div>

                <div className="rounded-lg border bg-card p-6">
                    <p className="text-muted-foreground">Form UI will be implemented in a future update</p>
                    {/* TODO: Implement edit form with validation */}
                </div>
            </div>
        </AppLayout>
    );
}
