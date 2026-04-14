import { Head } from "@inertiajs/react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { dashboard } from "@/routes";

interface DashboardDetailProps {
    title: string;
    endpoint: string;
    item: Record<string, unknown>;
}

export default function DashboardDetail({ title, endpoint, item }: DashboardDetailProps) {
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
                        <pre className="overflow-auto rounded bg-muted p-3 text-xs">{JSON.stringify(item, null, 2)}</pre>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

DashboardDetail.layout = {
    breadcrumbs: [
        {
            title: "Dashboard",
            href: dashboard(),
        },
    ],
};
