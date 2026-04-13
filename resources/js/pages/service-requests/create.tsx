import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';

interface Props {
    categories: any[];
    communities: any[];
    contacts: any[];
}

export default function Create({ categories: _categories, communities: _communities, contacts: _contacts }: Props) {
    return (
        <AppLayout>
            <Head title="Create Service Request" />

            <div className="space-y-6">
                <div>
                    <h1 className="text-3xl font-bold">Create Service Request</h1>
                    <p className="text-muted-foreground">Submit a new service or maintenance request</p>
                </div>

                <div className="rounded-lg border bg-card p-6">
                    <p className="text-muted-foreground">Form UI will be implemented in a future update</p>
                    {/* TODO: Implement create form with validation */}
                </div>
            </div>
        </AppLayout>
    );
}
