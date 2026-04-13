import { Head, router } from '@inertiajs/react';
import {
    Bell,
    BellOff,
    Calendar,
    Check,
    CheckCheck,
    Mail,
    Settings,
    Trash2,
    Wrench,
} from 'lucide-react';
import { useState } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';

interface Notification {
    id: string;
    type: string;
    data: {
        type: string;
        title: string;
        message: string;
        action_url?: string;
        icon?: string;
        severity?: 'info' | 'warning' | 'success' | 'error';
        [key: string]: unknown;
    };
    read_at: string | null;
    created_at: string;
}

interface NotificationStatistics {
    total: number;
    unread: number;
    read: number;
    today: number;
    this_week: number;
}

interface NotificationPreferences {
    email_lease_expiring: boolean;
    email_service_request: boolean;
    email_payment_reminder: boolean;
    push_lease_expiring: boolean;
    push_service_request: boolean;
    push_payment_reminder: boolean;
    inapp_enabled: boolean;
}

interface NotificationsIndexProps {
    notifications: Notification[];
    statistics: NotificationStatistics;
    preferences: NotificationPreferences;
}

function formatTimeAgo(dateString: string): string {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now.getTime() - date.getTime()) / 1000);

    if (diffInSeconds < 60) {
return 'Just now';
}

    if (diffInSeconds < 3600) {
return `${Math.floor(diffInSeconds / 60)}m ago`;
}

    if (diffInSeconds < 86400) {
return `${Math.floor(diffInSeconds / 3600)}h ago`;
}

    if (diffInSeconds < 604800) {
return `${Math.floor(diffInSeconds / 86400)}d ago`;
}

    return date.toLocaleDateString();
}

function getTypeIcon(type?: string) {
    switch (type) {
        case 'lease_expiring':
            return <Calendar className="h-5 w-5 text-orange-500" />;
        case 'service_request_created':
        case 'service_request_status_changed':
            return <Wrench className="h-5 w-5 text-blue-500" />;
        default:
            return <Bell className="h-5 w-5 text-gray-500" />;
    }
}

function NotificationItem({
    notification,
    onMarkAsRead,
    onDelete,
}: {
    notification: Notification;
    onMarkAsRead: (id: string) => void;
    onDelete: (id: string) => void;
}) {
    const isUnread = !notification.read_at;

    return (
        <div
            className={`flex items-start gap-4 rounded-lg border p-4 transition-colors ${
                isUnread ? 'border-blue-200 bg-blue-50/50' : 'bg-card'
            }`}
        >
            <div className="flex-shrink-0">{getTypeIcon(notification.data.type)}</div>
            <div className="min-w-0 flex-1">
                <div className="flex items-center gap-2">
                    <h4 className={`text-sm ${isUnread ? 'font-semibold' : 'font-medium'}`}>
                        {notification.data.title}
                    </h4>
                    {isUnread && (
                        <Badge variant="secondary" className="bg-blue-100 text-blue-800 text-xs">
                            New
                        </Badge>
                    )}
                </div>
                <p className="mt-1 text-sm text-muted-foreground">{notification.data.message}</p>
                <div className="mt-2 flex items-center gap-4">
                    <span className="text-xs text-muted-foreground">{formatTimeAgo(notification.created_at)}</span>
                    {notification.data.action_url && (
                        <a href={notification.data.action_url} className="text-xs text-primary hover:underline">
                            View details
                        </a>
                    )}
                </div>
            </div>
            <div className="flex items-center gap-1">
                {isUnread && (
                    <Button
                        variant="ghost"
                        size="icon"
                        className="h-8 w-8"
                        onClick={() => onMarkAsRead(notification.id)}
                        title="Mark as read"
                    >
                        <Check className="h-4 w-4" />
                    </Button>
                )}
                <Button
                    variant="ghost"
                    size="icon"
                    className="h-8 w-8 text-destructive hover:text-destructive"
                    onClick={() => onDelete(notification.id)}
                    title="Delete"
                >
                    <Trash2 className="h-4 w-4" />
                </Button>
            </div>
        </div>
    );
}

export default function NotificationsIndex({ notifications, statistics, preferences }: NotificationsIndexProps) {
    const [localPreferences, setLocalPreferences] = useState(preferences);
    const [isUpdatingPreferences, setIsUpdatingPreferences] = useState(false);

    const handleMarkAsRead = (id: string) => {
        router.post(
            `/api/notifications/${id}/read`,
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    router.reload({ only: ['notifications', 'statistics'] });
                },
            },
        );
    };

    const handleMarkAllAsRead = () => {
        router.post(
            '/api/notifications/read-all',
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    router.reload({ only: ['notifications', 'statistics'] });
                },
            },
        );
    };

    const handleDelete = (id: string) => {
        router.delete(`/api/notifications/${id}`, {
            preserveScroll: true,
            onSuccess: () => {
                router.reload({ only: ['notifications', 'statistics'] });
            },
        });
    };

    const handleDeleteAll = () => {
        if (confirm('Are you sure you want to delete all notifications?')) {
            router.delete('/api/notifications', {
                preserveScroll: true,
                onSuccess: () => {
                    router.reload({ only: ['notifications', 'statistics'] });
                },
            });
        }
    };

    const handlePreferenceChange = (key: keyof NotificationPreferences, value: boolean) => {
        const newPreferences = { ...localPreferences, [key]: value };
        setLocalPreferences(newPreferences);
        setIsUpdatingPreferences(true);

        router.put(
            '/api/notifications/preferences',
            { [key]: value },
            {
                preserveScroll: true,
                onFinish: () => setIsUpdatingPreferences(false),
            },
        );
    };

    return (
        <>
            <Head title="Notifications" />
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Notifications</h1>
                        <p className="text-muted-foreground">Manage your notifications and preferences</p>
                    </div>
                    <div className="flex items-center gap-2">
                        {statistics.unread > 0 && (
                            <Button variant="outline" size="sm" onClick={handleMarkAllAsRead}>
                                <CheckCheck className="mr-2 h-4 w-4" />
                                Mark all as read
                            </Button>
                        )}
                        {notifications.length > 0 && (
                            <Button variant="outline" size="sm" onClick={handleDeleteAll}>
                                <Trash2 className="mr-2 h-4 w-4" />
                                Clear all
                            </Button>
                        )}
                    </div>
                </div>

                {/* Statistics Cards */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total</CardTitle>
                            <Bell className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{statistics.total}</div>
                            <p className="text-xs text-muted-foreground">All notifications</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Unread</CardTitle>
                            <Badge variant="secondary">{statistics.unread}</Badge>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-blue-600">{statistics.unread}</div>
                            <p className="text-xs text-muted-foreground">Awaiting attention</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Today</CardTitle>
                            <Calendar className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{statistics.today}</div>
                            <p className="text-xs text-muted-foreground">Received today</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">This Week</CardTitle>
                            <Calendar className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{statistics.this_week}</div>
                            <p className="text-xs text-muted-foreground">Received this week</p>
                        </CardContent>
                    </Card>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Notifications List */}
                    <div className="lg:col-span-2">
                        <Card>
                            <CardHeader>
                                <div className="flex items-center gap-2">
                                    <Bell className="h-5 w-5 text-blue-500" />
                                    <CardTitle>All Notifications</CardTitle>
                                </div>
                                <CardDescription>
                                    {statistics.unread > 0
                                        ? `You have ${statistics.unread} unread notification${statistics.unread > 1 ? 's' : ''}`
                                        : 'All caught up!'}
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                {notifications.length === 0 ? (
                                    <div className="flex flex-col items-center justify-center py-12 text-center">
                                        <BellOff className="h-12 w-12 text-muted-foreground/50" />
                                        <h3 className="mt-4 text-lg font-medium">No notifications</h3>
                                        <p className="mt-2 text-sm text-muted-foreground">
                                            You're all caught up! Check back later for new updates.
                                        </p>
                                    </div>
                                ) : (
                                    <div className="space-y-3">
                                        {notifications.map((notification) => (
                                            <NotificationItem
                                                key={notification.id}
                                                notification={notification}
                                                onMarkAsRead={handleMarkAsRead}
                                                onDelete={handleDelete}
                                            />
                                        ))}
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    {/* Preferences */}
                    <div>
                        <Card>
                            <CardHeader>
                                <div className="flex items-center gap-2">
                                    <Settings className="h-5 w-5 text-gray-500" />
                                    <CardTitle>Preferences</CardTitle>
                                </div>
                                <CardDescription>Manage how you receive notifications</CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-6">
                                {/* In-App Notifications */}
                                <div className="space-y-4">
                                    <div className="flex items-center gap-2">
                                        <Bell className="h-4 w-4" />
                                        <h4 className="text-sm font-medium">In-App Notifications</h4>
                                    </div>
                                    <div className="flex items-center justify-between">
                                        <Label htmlFor="inapp_enabled" className="text-sm text-muted-foreground">
                                            Enable in-app notifications
                                        </Label>
                                        <Switch
                                            id="inapp_enabled"
                                            checked={localPreferences.inapp_enabled}
                                            onCheckedChange={(checked: boolean) => handlePreferenceChange('inapp_enabled', checked)}
                                            disabled={isUpdatingPreferences}
                                        />
                                    </div>
                                </div>

                                {/* Email Notifications */}
                                <div className="space-y-4">
                                    <div className="flex items-center gap-2">
                                        <Mail className="h-4 w-4" />
                                        <h4 className="text-sm font-medium">Email Notifications</h4>
                                    </div>
                                    <div className="space-y-3">
                                        <div className="flex items-center justify-between">
                                            <Label htmlFor="email_lease_expiring" className="text-sm text-muted-foreground">
                                                Lease expiring
                                            </Label>
                                            <Switch
                                                id="email_lease_expiring"
                                                checked={localPreferences.email_lease_expiring}
                                                onCheckedChange={(checked: boolean) =>
                                                    handlePreferenceChange('email_lease_expiring', checked)
                                                }
                                                disabled={isUpdatingPreferences}
                                            />
                                        </div>
                                        <div className="flex items-center justify-between">
                                            <Label htmlFor="email_service_request" className="text-sm text-muted-foreground">
                                                Service requests
                                            </Label>
                                            <Switch
                                                id="email_service_request"
                                                checked={localPreferences.email_service_request}
                                                onCheckedChange={(checked: boolean) =>
                                                    handlePreferenceChange('email_service_request', checked)
                                                }
                                                disabled={isUpdatingPreferences}
                                            />
                                        </div>
                                        <div className="flex items-center justify-between">
                                            <Label htmlFor="email_payment_reminder" className="text-sm text-muted-foreground">
                                                Payment reminders
                                            </Label>
                                            <Switch
                                                id="email_payment_reminder"
                                                checked={localPreferences.email_payment_reminder}
                                                onCheckedChange={(checked: boolean) =>
                                                    handlePreferenceChange('email_payment_reminder', checked)
                                                }
                                                disabled={isUpdatingPreferences}
                                            />
                                        </div>
                                    </div>
                                </div>

                                {/* Push Notifications */}
                                <div className="space-y-4">
                                    <div className="flex items-center gap-2">
                                        <Bell className="h-4 w-4" />
                                        <h4 className="text-sm font-medium">Push Notifications</h4>
                                    </div>
                                    <div className="space-y-3">
                                        <div className="flex items-center justify-between">
                                            <Label htmlFor="push_lease_expiring" className="text-sm text-muted-foreground">
                                                Lease expiring
                                            </Label>
                                            <Switch
                                                id="push_lease_expiring"
                                                checked={localPreferences.push_lease_expiring}
                                                onCheckedChange={(checked: boolean) =>
                                                    handlePreferenceChange('push_lease_expiring', checked)
                                                }
                                                disabled={isUpdatingPreferences}
                                            />
                                        </div>
                                        <div className="flex items-center justify-between">
                                            <Label htmlFor="push_service_request" className="text-sm text-muted-foreground">
                                                Service requests
                                            </Label>
                                            <Switch
                                                id="push_service_request"
                                                checked={localPreferences.push_service_request}
                                                onCheckedChange={(checked: boolean) =>
                                                    handlePreferenceChange('push_service_request', checked)
                                                }
                                                disabled={isUpdatingPreferences}
                                            />
                                        </div>
                                        <div className="flex items-center justify-between">
                                            <Label htmlFor="push_payment_reminder" className="text-sm text-muted-foreground">
                                                Payment reminders
                                            </Label>
                                            <Switch
                                                id="push_payment_reminder"
                                                checked={localPreferences.push_payment_reminder}
                                                onCheckedChange={(checked: boolean) =>
                                                    handlePreferenceChange('push_payment_reminder', checked)
                                                }
                                                disabled={isUpdatingPreferences}
                                            />
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </>
    );
}

NotificationsIndex.layout = {
    breadcrumbs: [
        {
            title: 'Notifications',
            href: '/notifications',
        },
    ],
};
