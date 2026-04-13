import { Head, Link, router } from '@inertiajs/react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Megaphone,
    Plus,
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
    CheckCircle,
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
    created_at: string;
    creator?: {
        id: number;
        name: string;
    };
}

interface AnnouncementStatistics {
    total: number;
    active: number;
    draft: number;
    scheduled: number;
    expired: number;
    cancelled: number;
}

interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface AnnouncementsIndexProps {
    announcements: PaginatedData<Announcement>;
    statistics: AnnouncementStatistics;
    filters: {
        status: string | null;
    };
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

function AnnouncementCard({ announcement }: { announcement: Announcement }) {
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
        <Card className="transition-shadow hover:shadow-md">
            <CardHeader className="pb-3">
                <div className="flex items-start justify-between">
                    <div className="flex-1">
                        <div className="flex items-center gap-2 mb-2">
                            {getStatusBadge(announcement.status)}
                            {getPriorityBadge(announcement.priority)}
                            {announcement.is_visible ? (
                                <Eye className="h-4 w-4 text-green-500" />
                            ) : (
                                <EyeOff className="h-4 w-4 text-gray-400" />
                            )}
                        </div>
                        <Link href={`/announcements/${announcement.id}`}>
                            <CardTitle className="text-lg hover:text-primary">{announcement.title}</CardTitle>
                        </Link>
                    </div>
                    <div className="flex items-center gap-1">
                        {announcement.status === 'draft' && (
                            <Button variant="ghost" size="icon" onClick={handlePublish} title="Publish">
                                <Send className="h-4 w-4 text-green-600" />
                            </Button>
                        )}
                        {(announcement.status === 'active' || announcement.status === 'scheduled') && (
                            <Button variant="ghost" size="icon" onClick={handleCancel} title="Cancel">
                                <XCircle className="h-4 w-4 text-orange-600" />
                            </Button>
                        )}
                        <Link href={`/announcements/${announcement.id}/edit`}>
                            <Button variant="ghost" size="icon" title="Edit">
                                <Edit className="h-4 w-4" />
                            </Button>
                        </Link>
                        <Button variant="ghost" size="icon" onClick={handleDelete} title="Delete">
                            <Trash2 className="h-4 w-4 text-destructive" />
                        </Button>
                    </div>
                </div>
            </CardHeader>
            <CardContent>
                <p className="text-sm text-muted-foreground line-clamp-2 mb-3">{announcement.description}</p>
                <div className="flex flex-wrap items-center gap-4 text-xs text-muted-foreground">
                    <div className="flex items-center gap-1">
                        <Calendar className="h-3 w-3" />
                        <span>
                            {new Date(announcement.start_date).toLocaleDateString()} -{' '}
                            {new Date(announcement.end_date).toLocaleDateString()}
                        </span>
                    </div>
                    <div className="flex items-center gap-1">
                        <Clock className="h-3 w-3" />
                        <span>
                            {announcement.start_time} - {announcement.end_time}
                        </span>
                    </div>
                    {announcement.creator && (
                        <span>By: {announcement.creator.name}</span>
                    )}
                </div>
            </CardContent>
        </Card>
    );
}

export default function AnnouncementsIndex({ announcements, statistics, filters }: AnnouncementsIndexProps) {
    const handleStatusFilter = (status: string) => {
        router.get('/announcements', { status: status === 'all' ? null : status }, { preserveState: true });
    };

    return (
        <>
            <Head title="Announcements" />
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Announcements</h1>
                        <p className="text-muted-foreground">Manage announcements for your properties</p>
                    </div>
                    <Link href="/announcements/create">
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            New Announcement
                        </Button>
                    </Link>
                </div>

                {/* Statistics Cards */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total</CardTitle>
                            <Megaphone className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{statistics.total}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Active</CardTitle>
                            <CheckCircle className="h-4 w-4 text-green-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-green-600">{statistics.active}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Draft</CardTitle>
                            <Edit className="h-4 w-4 text-gray-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-gray-600">{statistics.draft}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Scheduled</CardTitle>
                            <Clock className="h-4 w-4 text-blue-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-blue-600">{statistics.scheduled}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Expired</CardTitle>
                            <Calendar className="h-4 w-4 text-orange-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-orange-600">{statistics.expired}</div>
                        </CardContent>
                    </Card>
                </div>

                {/* Filter */}
                <div className="flex items-center gap-4">
                    <Select value={filters.status || 'all'} onValueChange={handleStatusFilter}>
                        <SelectTrigger className="w-[180px]">
                            <SelectValue placeholder="Filter by status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Statuses</SelectItem>
                            <SelectItem value="draft">Draft</SelectItem>
                            <SelectItem value="scheduled">Scheduled</SelectItem>
                            <SelectItem value="active">Active</SelectItem>
                            <SelectItem value="expired">Expired</SelectItem>
                            <SelectItem value="cancelled">Cancelled</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                {/* Announcements List */}
                {announcements.data.length === 0 ? (
                    <Card>
                        <CardContent className="flex flex-col items-center justify-center py-12">
                            <Megaphone className="h-12 w-12 text-muted-foreground/50" />
                            <h3 className="mt-4 text-lg font-medium">No announcements</h3>
                            <p className="mt-2 text-sm text-muted-foreground">
                                Get started by creating your first announcement.
                            </p>
                            <Link href="/announcements/create" className="mt-4">
                                <Button>
                                    <Plus className="mr-2 h-4 w-4" />
                                    Create Announcement
                                </Button>
                            </Link>
                        </CardContent>
                    </Card>
                ) : (
                    <div className="grid gap-4 md:grid-cols-2">
                        {announcements.data.map((announcement) => (
                            <AnnouncementCard key={announcement.id} announcement={announcement} />
                        ))}
                    </div>
                )}

                {/* Pagination */}
                {announcements.last_page > 1 && (
                    <div className="flex items-center justify-center gap-2">
                        {Array.from({ length: announcements.last_page }, (_, i) => i + 1).map((page) => (
                            <Button
                                key={page}
                                variant={page === announcements.current_page ? 'default' : 'outline'}
                                size="sm"
                                onClick={() => router.get('/announcements', { page, status: filters.status })}
                            >
                                {page}
                            </Button>
                        ))}
                    </div>
                )}
            </div>
        </>
    );
}

AnnouncementsIndex.layout = {
    breadcrumbs: [
        {
            title: 'Announcements',
            href: '/announcements',
        },
    ],
};
