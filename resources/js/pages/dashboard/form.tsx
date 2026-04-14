import { Head } from "@inertiajs/react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { dashboard } from "@/routes";

interface DashboardFormProps {
    title: string;
    endpoint: string;
}

export default function DashboardForm({ title, endpoint }: DashboardFormProps) {
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
                        <p className="text-sm text-muted-foreground">
                            Form layout is available at this dashboard path.
                        </p>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

DashboardForm.layout = {
    breadcrumbs: [
        {
            title: "Dashboard",
            href: dashboard(),
        },
    ],
};
