import { Head } from "@inertiajs/react";
import { Megaphone, Calendar, Clock, AlertTriangle, Info } from "lucide-react";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

interface Announcement {
    id: number;
    title: string;
    description: string;
    start_date: string;
    start_time: string;
    end_date: string;
    end_time: string;
    priority: "low" | "normal" | "high" | "urgent";
    created_at: string;
    creator?: {
        id: number;
        name: string;
    };
}

interface DirectoryIndexProps {
    announcements: Announcement[];
}

function getPriorityBadge(priority: string) {
    const priorityConfig: Record<
        string,
        { icon: React.ReactNode; className: string; label: string }
    > = {
        low: {
            icon: <Info className="h-3 w-3" />,
            className: "bg-gray-100 text-gray-600",
            label: "Low",
        },
        normal: {
            icon: <Info className="h-3 w-3" />,
            className: "bg-blue-100 text-blue-600",
            label: "Normal",
        },
        high: {
            icon: <AlertTriangle className="h-3 w-3" />,
            className: "bg-orange-100 text-orange-600",
            label: "High Priority",
        },
        urgent: {
            icon: <AlertTriangle className="h-3 w-3" />,
            className: "bg-red-100 text-red-600",
            label: "Urgent",
        },
    };

    const config = priorityConfig[priority] || priorityConfig.normal;

    return (
        <Badge variant="outline" className={config.className}>
            {config.icon}
            <span className="ml-1">{config.label}</span>
        </Badge>
    );
}

function AnnouncementCard({ announcement }: { announcement: Announcement }) {
    const isUrgent =
        announcement.priority === "urgent" || announcement.priority === "high";

    return (
        <Card
            className={`transition-shadow hover:shadow-md ${isUrgent ? "border-l-4 border-l-orange-500" : ""}`}
        >
            <CardHeader className="pb-3">
                <div className="flex items-start justify-between">
                    <div className="flex-1">
                        <div className="flex items-center gap-2 mb-2">
                            {getPriorityBadge(announcement.priority)}
                        </div>
                        <CardTitle className="text-lg">
                            {announcement.title}
                        </CardTitle>
                    </div>
                </div>
            </CardHeader>
            <CardContent>
                <p className="text-sm text-muted-foreground whitespace-pre-wrap mb-4">
                    {announcement.description}
                </p>
                <div className="flex flex-wrap items-center gap-4 text-xs text-muted-foreground border-t pt-3">
                    <div className="flex items-center gap-1">
                        <Calendar className="h-3 w-3" />
                        <span>
                            {new Date(
                                announcement.start_date,
                            ).toLocaleDateString()}{" "}
                            -{" "}
                            {new Date(
                                announcement.end_date,
                            ).toLocaleDateString()}
                        </span>
                    </div>
                    <div className="flex items-center gap-1">
                        <Clock className="h-3 w-3" />
                        <span>
                            {announcement.start_time} - {announcement.end_time}
                        </span>
                    </div>
                    {announcement.creator && (
                        <span>Posted by: {announcement.creator.name}</span>
                    )}
                </div>
            </CardContent>
        </Card>
    );
}

export default function DirectoryIndex({ announcements }: DirectoryIndexProps) {
    const urgentAnnouncements = announcements.filter(
        (a) => a.priority === "urgent" || a.priority === "high",
    );
    const regularAnnouncements = announcements.filter(
        (a) => a.priority !== "urgent" && a.priority !== "high",
    );

    return (
        <>
            <Head title="Directory" />
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight">
                        Directory
                    </h1>
                    <p className="text-muted-foreground">
                        Important announcements and notices for your community
                    </p>
                </div>

                {announcements.length === 0 ? (
                    <Card>
                        <CardContent className="flex flex-col items-center justify-center py-12">
                            <Megaphone className="h-12 w-12 text-muted-foreground/50" />
                            <h3 className="mt-4 text-lg font-medium">
                                No announcements
                            </h3>
                            <p className="mt-2 text-sm text-muted-foreground">
                                There are no active announcements at this time.
                            </p>
                        </CardContent>
                    </Card>
                ) : (
                    <div className="space-y-6">
                        {urgentAnnouncements.length > 0 && (
                            <div className="space-y-4">
                                <div className="flex items-center gap-2">
                                    <AlertTriangle className="h-5 w-5 text-orange-500" />
                                    <h2 className="text-lg font-semibold">
                                        Important Notices
                                    </h2>
                                </div>
                                <div className="grid gap-4 md:grid-cols-2">
                                    {urgentAnnouncements.map((announcement) => (
                                        <AnnouncementCard
                                            key={announcement.id}
                                            announcement={announcement}
                                        />
                                    ))}
                                </div>
                            </div>
                        )}

                        {regularAnnouncements.length > 0 && (
                            <div className="space-y-4">
                                <div className="flex items-center gap-2">
                                    <Megaphone className="h-5 w-5 text-muted-foreground" />
                                    <h2 className="text-lg font-semibold">
                                        Announcements
                                    </h2>
                                </div>
                                <div className="grid gap-4 md:grid-cols-2">
                                    {regularAnnouncements.map(
                                        (announcement) => (
                                            <AnnouncementCard
                                                key={announcement.id}
                                                announcement={announcement}
                                            />
                                        ),
                                    )}
                                </div>
                            </div>
                        )}
                    </div>
                )}
            </div>
        </>
    );
}

DirectoryIndex.layout = {
    breadcrumbs: [
        {
            title: "Directory",
            href: "/directory",
        },
    ],
};
