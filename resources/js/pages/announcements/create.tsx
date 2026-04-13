import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import type { FormEventHandler } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';

interface AnnouncementFormData {
    title: string;
    description: string;
    start_date: string;
    start_time: string;
    end_date: string;
    end_time: string;
    is_visible: boolean;
    priority: 'low' | 'normal' | 'high' | 'urgent';
    notify_user_types: string[];
}

const today = new Date().toISOString().split('T')[0];
const nextWeek = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

export default function AnnouncementsCreate() {
    const { data, setData, post, processing, errors } = useForm<AnnouncementFormData>({
        title: '',
        description: '',
        start_date: today,
        start_time: '09:00',
        end_date: nextWeek,
        end_time: '17:00',
        is_visible: true,
        priority: 'normal',
        notify_user_types: [],
    });

    const handleSubmit: FormEventHandler = (e) => {
        e.preventDefault();
        post('/announcements');
    };

    const toggleNotifyUserType = (type: string) => {
        const currentTypes = data.notify_user_types || [];

        if (currentTypes.includes(type)) {
            setData('notify_user_types', currentTypes.filter((t) => t !== type));
        } else {
            setData('notify_user_types', [...currentTypes, type]);
        }
    };

    return (
        <>
            <Head title="Create Announcement" />
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
                <div className="flex items-center gap-4">
                    <Link href="/announcements">
                        <Button variant="ghost" size="icon">
                            <ArrowLeft className="h-4 w-4" />
                        </Button>
                    </Link>
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Create Announcement</h1>
                        <p className="text-muted-foreground">Create a new announcement for your properties</p>
                    </div>
                </div>

                <form onSubmit={handleSubmit}>
                    <div className="grid gap-6 lg:grid-cols-3">
                        <div className="lg:col-span-2 space-y-6">
                            <Card>
                                <CardHeader>
                                    <CardTitle>Announcement Details</CardTitle>
                                    <CardDescription>Basic information about the announcement</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="title">Title</Label>
                                        <Input
                                            id="title"
                                            value={data.title}
                                            onChange={(e) => setData('title', e.target.value)}
                                            placeholder="Enter announcement title"
                                        />
                                        {errors.title && (
                                            <p className="text-sm text-destructive">{errors.title}</p>
                                        )}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="description">Description</Label>
                                        <Textarea
                                            id="description"
                                            value={data.description}
                                            onChange={(e) => setData('description', e.target.value)}
                                            placeholder="Enter announcement description"
                                            rows={6}
                                        />
                                        {errors.description && (
                                            <p className="text-sm text-destructive">{errors.description}</p>
                                        )}
                                    </div>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>Schedule</CardTitle>
                                    <CardDescription>When should this announcement be displayed</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="grid gap-4 sm:grid-cols-2">
                                        <div className="space-y-2">
                                            <Label htmlFor="start_date">Start Date</Label>
                                            <Input
                                                id="start_date"
                                                type="date"
                                                value={data.start_date}
                                                onChange={(e) => setData('start_date', e.target.value)}
                                            />
                                            {errors.start_date && (
                                                <p className="text-sm text-destructive">{errors.start_date}</p>
                                            )}
                                        </div>

                                        <div className="space-y-2">
                                            <Label htmlFor="start_time">Start Time</Label>
                                            <Input
                                                id="start_time"
                                                type="time"
                                                value={data.start_time}
                                                onChange={(e) => setData('start_time', e.target.value)}
                                            />
                                            {errors.start_time && (
                                                <p className="text-sm text-destructive">{errors.start_time}</p>
                                            )}
                                        </div>
                                    </div>

                                    <div className="grid gap-4 sm:grid-cols-2">
                                        <div className="space-y-2">
                                            <Label htmlFor="end_date">End Date</Label>
                                            <Input
                                                id="end_date"
                                                type="date"
                                                value={data.end_date}
                                                onChange={(e) => setData('end_date', e.target.value)}
                                            />
                                            {errors.end_date && (
                                                <p className="text-sm text-destructive">{errors.end_date}</p>
                                            )}
                                        </div>

                                        <div className="space-y-2">
                                            <Label htmlFor="end_time">End Time</Label>
                                            <Input
                                                id="end_time"
                                                type="time"
                                                value={data.end_time}
                                                onChange={(e) => setData('end_time', e.target.value)}
                                            />
                                            {errors.end_time && (
                                                <p className="text-sm text-destructive">{errors.end_time}</p>
                                            )}
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>

                        <div className="space-y-6">
                            <Card>
                                <CardHeader>
                                    <CardTitle>Settings</CardTitle>
                                    <CardDescription>Configure announcement options</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="priority">Priority</Label>
                                        <Select
                                            value={data.priority}
                                            onValueChange={(value: 'low' | 'normal' | 'high' | 'urgent') =>
                                                setData('priority', value)
                                            }
                                        >
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select priority" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="low">Low</SelectItem>
                                                <SelectItem value="normal">Normal</SelectItem>
                                                <SelectItem value="high">High</SelectItem>
                                                <SelectItem value="urgent">Urgent</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        {errors.priority && (
                                            <p className="text-sm text-destructive">{errors.priority}</p>
                                        )}
                                    </div>

                                    <div className="flex items-center space-x-2">
                                        <Checkbox
                                            id="is_visible"
                                            checked={data.is_visible}
                                            onCheckedChange={(checked) => setData('is_visible', checked as boolean)}
                                        />
                                        <Label htmlFor="is_visible" className="cursor-pointer">
                                            Visible to users
                                        </Label>
                                    </div>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>Notifications</CardTitle>
                                    <CardDescription>Who should be notified</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-3">
                                    <div className="flex items-center space-x-2">
                                        <Checkbox
                                            id="notify_all"
                                            checked={data.notify_user_types?.includes('all')}
                                            onCheckedChange={() => toggleNotifyUserType('all')}
                                        />
                                        <Label htmlFor="notify_all" className="cursor-pointer">
                                            All Users
                                        </Label>
                                    </div>
                                    <div className="flex items-center space-x-2">
                                        <Checkbox
                                            id="notify_tenant"
                                            checked={data.notify_user_types?.includes('tenant')}
                                            onCheckedChange={() => toggleNotifyUserType('tenant')}
                                        />
                                        <Label htmlFor="notify_tenant" className="cursor-pointer">
                                            Tenants
                                        </Label>
                                    </div>
                                    <div className="flex items-center space-x-2">
                                        <Checkbox
                                            id="notify_owner"
                                            checked={data.notify_user_types?.includes('owner')}
                                            onCheckedChange={() => toggleNotifyUserType('owner')}
                                        />
                                        <Label htmlFor="notify_owner" className="cursor-pointer">
                                            Owners
                                        </Label>
                                    </div>
                                </CardContent>
                            </Card>

                            <div className="flex gap-2">
                                <Button type="submit" className="flex-1" disabled={processing}>
                                    <Save className="mr-2 h-4 w-4" />
                                    Create Announcement
                                </Button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </>
    );
}

AnnouncementsCreate.layout = {
    breadcrumbs: [
        {
            title: 'Announcements',
            href: '/announcements',
        },
        {
            title: 'Create',
            href: '/announcements/create',
        },
    ],
};
