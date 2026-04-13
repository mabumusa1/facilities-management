import { Head, Link, router } from '@inertiajs/react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    ArrowLeft,
    Calendar,
    Clock,
    Eye,
    EyeOff,
    Edit,
    Trash2,
    Send,
    XCircle,
    AlertTriangle,
    Info,
    User,
    Bell,
} from 'lucide-react';

interface Announcement {
    id: number;
    title: string;
    description: string;
    start_date: string;
    start_time: string;
    end_date: string;
    end_time: string;
    is_visible: boolean;
    priority: 'low' | 'normal' | 'high' | 'urgent';
    status: 'draft' | 'scheduled' | 'active' | 'expired' | 'cancelled';
    notify_user_types: string[] | null;
    community_ids: number[] | null;
    building_ids: number[] | null;
    created_at: string;
    updated_at: string;
    creator?: {
        id: number;
        name: string;
    };
}

interface AnnouncementShowProps {
    announcement: Announcement;
}

function getStatusBadge(status: string) {
    const statusConfig: Record<string, { variant: 'default' | 'secondary' | 'destructive' | 'outline'; className: string }> = {
        draft: { variant: 'secondary', className: 'bg-gray-100 text-gray-800' },
        scheduled: { variant: 'outline', className: 'border-blue-500 text-blue-600' },
        active: { variant: 'default', className: 'bg-green-100 text-green-800' },
        expired: { variant: 'secondary', className: 'bg-orange-100 text-orange-800' },
        cancelled: { variant: 'destructive', className: 'bg-red-100 text-red-800' },
    };

    const config = statusConfig[status] || statusConfig.draft;
    return (
        <Badge variant={config.variant} className={config.className}>
            {status.charAt(0).toUpperCase() + status.slice(1)}
        </Badge>
    );
}

function getPriorityBadge(priority: string) {
    const priorityConfig: Record<string, { icon: React.ReactNode; className: string }> = {
        low: { icon: <Info className="h-3 w-3" />, className: 'bg-gray-100 text-gray-600' },
        normal: { icon: <Info className="h-3 w-3" />, className: 'bg-blue-100 text-blue-600' },
        high: { icon: <AlertTriangle className="h-3 w-3" />, className: 'bg-orange-100 text-orange-600' },
        urgent: { icon: <AlertTriangle className="h-3 w-3" />, className: 'bg-red-100 text-red-600' },
    };

    const config = priorityConfig[priority] || priorityConfig.normal;
    return (
        <Badge variant="outline" className={config.className}>
            {config.icon}
            <span className="ml-1">{priority.charAt(0).toUpperCase() + priority.slice(1)}</span>
        </Badge>
    );
}

export default function AnnouncementShow({ announcement }: AnnouncementShowProps) {
    const handlePublish = () => {
        router.post(`/announcements/${announcement.id}/publish`);
    };

    const handleCancel = () => {
        if (confirm('Are you sure you want to cancel this announcement?')) {
            router.post(`/announcements/${announcement.id}/cancel`);
        }
    };

    const handleDelete = () => {
        if (confirm('Are you sure you want to delete this announcement?')) {
            router.delete(`/announcements/${announcement.id}`);
        }
    };

    return (
        <>
            <Head title={announcement.title} />
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <Link href="/announcements">
                            <Button variant="ghost" size="icon">
                                <ArrowLeft className="h-4 w-4" />
                            </Button>
                        </Link>
                        <div>
                            <div className="flex items-center gap-2 mb-1">
                                {getStatusBadge(announcement.status)}
                                {getPriorityBadge(announcement.priority)}
                                {announcement.is_visible ? (
                                    <Badge variant="outline" className="border-green-500 text-green-600">
                                        <Eye className="mr-1 h-3 w-3" />
                                        Visible
                                    </Badge>
                                ) : (
                                    <Badge variant="outline" className="border-gray-400 text-gray-500">
                                        <EyeOff className="mr-1 h-3 w-3" />
                                        Hidden
                                    </Badge>
                                )}
                            </div>
                            <h1 className="text-2xl font-bold tracking-tight">{announcement.title}</h1>
                        </div>
                    </div>
                    <div className="flex items-center gap-2">
                        {announcement.status === 'draft' && (
                            <Button variant="outline" onClick={handlePublish}>
                                <Send className="mr-2 h-4 w-4" />
                                Publish
                            </Button>
                        )}
                        {(announcement.status === 'active' || announcement.status === 'scheduled') && (
                            <Button variant="outline" onClick={handleCancel}>
                                <XCircle className="mr-2 h-4 w-4" />
                                Cancel
                            </Button>
                        )}
                        <Link href={`/announcements/${announcement.id}/edit`}>
                            <Button variant="outline">
                                <Edit className="mr-2 h-4 w-4" />
                                Edit
                            </Button>
                        </Link>
                        <Button variant="destructive" onClick={handleDelete}>
                            <Trash2 className="mr-2 h-4 w-4" />
                            Delete
                        </Button>
                    </div>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    <div className="lg:col-span-2 space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Description</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p className="whitespace-pre-wrap">{announcement.description}</p>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle>Schedule</CardTitle>
                                <CardDescription>Display period for this announcement</CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div className="grid gap-4 sm:grid-cols-2">
                                    <div className="space-y-2">
                                        <div className="text-sm font-medium text-muted-foreground">Start</div>
                                        <div className="flex items-center gap-4">
                                            <div className="flex items-center gap-2">
                                                <Calendar className="h-4 w-4 text-muted-foreground" />
                                                <span>{new Date(announcement.start_date).toLocaleDateString()}</span>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <Clock className="h-4 w-4 text-muted-foreground" />
                                                <span>{announcement.start_time}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="space-y-2">
                                        <div className="text-sm font-medium text-muted-foreground">End</div>
                                        <div className="flex items-center gap-4">
                                            <div className="flex items-center gap-2">
                                                <Calendar className="h-4 w-4 text-muted-foreground" />
                                                <span>{new Date(announcement.end_date).toLocaleDateString()}</span>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <Clock className="h-4 w-4 text-muted-foreground" />
                                                <span>{announcement.end_time}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <div className="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Details</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                {announcement.creator && (
                                    <div className="flex items-center gap-2">
                                        <User className="h-4 w-4 text-muted-foreground" />
                                        <span className="text-sm">
                                            Created by <span className="font-medium">{announcement.creator.name}</span>
                                        </span>
                                    </div>
                                )}
                                <div className="flex items-center gap-2">
                                    <Calendar className="h-4 w-4 text-muted-foreground" />
                                    <span className="text-sm">
                                        Created on {new Date(announcement.created_at).toLocaleDateString()}
                                    </span>
                                </div>
                                {announcement.updated_at !== announcement.created_at && (
                                    <div className="flex items-center gap-2">
                                        <Clock className="h-4 w-4 text-muted-foreground" />
                                        <span className="text-sm">
                                            Last updated {new Date(announcement.updated_at).toLocaleDateString()}
                                        </span>
                                    </div>
                                )}
                            </CardContent>
                        </Card>

                        {announcement.notify_user_types && announcement.notify_user_types.length > 0 && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Bell className="h-4 w-4" />
                                        Notifications
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="flex flex-wrap gap-2">
                                        {announcement.notify_user_types.map((type) => (
                                            <Badge key={type} variant="outline">
                                                {type.charAt(0).toUpperCase() + type.slice(1)}
                                            </Badge>
                                        ))}
                                    </div>
                                </CardContent>
                            </Card>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}

AnnouncementShow.layout = {
    breadcrumbs: [
        {
            title: 'Announcements',
            href: '/announcements',
        },
        {
            title: 'View',
            href: '#',
        },
    ],
};
