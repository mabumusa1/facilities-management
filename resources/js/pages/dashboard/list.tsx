import { Head } from "@inertiajs/react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { dashboard } from "@/routes";

interface ListItem {
    id: number | string;
    label?: string | null;
    [key: string]: unknown;
}

interface DashboardListProps {
    title: string;
    endpoint: string;
    items: ListItem[];
}

export default function DashboardList({ title, endpoint, items }: DashboardListProps) {
    return (
        <>
            <Head title={title} />
            <div className="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
                <h1 className="text-2xl font-semibold">{title}</h1>
                <p className="text-sm text-muted-foreground">{endpoint}</p>

                <Card>
                    <CardHeader>
                        <CardTitle>{title}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        {items.length === 0 ? (
                            <p className="text-sm text-muted-foreground">No records found.</p>
                        ) : (
                            <div className="space-y-2">
                                {items.map((item) => (
                                    <div key={item.id} className="rounded border p-3 text-sm">
                                        <p className="font-medium">{item.label || `#${item.id}`}</p>
                                        <p className="text-muted-foreground">ID: {item.id}</p>
                                    </div>
                                ))}
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

DashboardList.layout = {
    breadcrumbs: [
        {
            title: "Dashboard",
            href: dashboard(),
        },
    ],
};
