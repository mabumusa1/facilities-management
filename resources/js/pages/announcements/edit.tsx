import { Head, Link, useForm } from "@inertiajs/react";
import { ArrowLeft, Save } from "lucide-react";
import type { FormEventHandler } from "react";
import { Button } from "@/components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { Checkbox } from "@/components/ui/checkbox";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";

interface Announcement {
    id: number;
    title: string;
    description: string;
    start_date: string;
    start_time: string;
    end_date: string;
    end_time: string;
    is_visible: boolean;
    priority: "low" | "normal" | "high" | "urgent";
    status: "draft" | "scheduled" | "active" | "expired" | "cancelled";
    notify_user_types: string[] | null;
}

interface AnnouncementEditProps {
    announcement: Announcement;
}

interface AnnouncementFormData {
    title: string;
    description: string;
    start_date: string;
    start_time: string;
    end_date: string;
    end_time: string;
    is_visible: boolean;
    priority: "low" | "normal" | "high" | "urgent";
    notify_user_types: string[];
}

export default function AnnouncementsEdit({
    announcement,
}: AnnouncementEditProps) {
    const { data, setData, put, processing, errors } =
        useForm<AnnouncementFormData>({
            title: announcement.title,
            description: announcement.description,
            start_date: announcement.start_date,
            start_time: announcement.start_time,
            end_date: announcement.end_date,
            end_time: announcement.end_time,
            is_visible: announcement.is_visible,
            priority: announcement.priority,
            notify_user_types: announcement.notify_user_types || [],
        });

    const handleSubmit: FormEventHandler = (e) => {
        e.preventDefault();
        put(`/announcements/${announcement.id}`);
    };

    const toggleNotifyUserType = (type: string) => {
        const currentTypes = data.notify_user_types || [];

        if (currentTypes.includes(type)) {
            setData(
                "notify_user_types",
                currentTypes.filter((t) => t !== type),
            );
        } else {
            setData("notify_user_types", [...currentTypes, type]);
        }
    };

    return (
        <>
            <Head title={`Edit: ${announcement.title}`} />
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
                <div className="flex items-center gap-4">
                    <Link href={`/announcements/${announcement.id}`}>
                        <Button variant="ghost" size="icon">
                            <ArrowLeft className="h-4 w-4" />
                        </Button>
                    </Link>
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">
                            Edit Announcement
                        </h1>
                        <p className="text-muted-foreground">
                            Update announcement details
                        </p>
                    </div>
                </div>

                <form onSubmit={handleSubmit}>
                    <div className="grid gap-6 lg:grid-cols-3">
                        <div className="lg:col-span-2 space-y-6">
                            <Card>
                                <CardHeader>
                                    <CardTitle>Announcement Details</CardTitle>
                                    <CardDescription>
                                        Basic information about the announcement
                                    </CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="title">Title</Label>
                                        <Input
                                            id="title"
                                            value={data.title}
                                            onChange={(e) =>
                                                setData("title", e.target.value)
                                            }
                                            placeholder="Enter announcement title"
                                        />
                                        {errors.title && (
                                            <p className="text-sm text-destructive">
                                                {errors.title}
                                            </p>
                                        )}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="description">
                                            Description
                                        </Label>
                                        <Textarea
                                            id="description"
                                            value={data.description}
                                            onChange={(e) =>
                                                setData(
                                                    "description",
                                                    e.target.value,
                                                )
                                            }
                                            placeholder="Enter announcement description"
                                            rows={6}
                                        />
                                        {errors.description && (
                                            <p className="text-sm text-destructive">
                                                {errors.description}
                                            </p>
                                        )}
                                    </div>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>Schedule</CardTitle>
                                    <CardDescription>
                                        When should this announcement be
                                        displayed
                                    </CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="grid gap-4 sm:grid-cols-2">
                                        <div className="space-y-2">
                                            <Label htmlFor="start_date">
                                                Start Date
                                            </Label>
                                            <Input
                                                id="start_date"
                                                type="date"
                                                value={data.start_date}
                                                onChange={(e) =>
                                                    setData(
                                                        "start_date",
                                                        e.target.value,
                                                    )
                                                }
                                            />
                                            {errors.start_date && (
                                                <p className="text-sm text-destructive">
                                                    {errors.start_date}
                                                </p>
                                            )}
                                        </div>

                                        <div className="space-y-2">
                                            <Label htmlFor="start_time">
                                                Start Time
                                            </Label>
                                            <Input
                                                id="start_time"
                                                type="time"
                                                value={data.start_time}
                                                onChange={(e) =>
                                                    setData(
                                                        "start_time",
                                                        e.target.value,
                                                    )
                                                }
                                            />
                                            {errors.start_time && (
                                                <p className="text-sm text-destructive">
                                                    {errors.start_time}
                                                </p>
                                            )}
                                        </div>
                                    </div>

                                    <div className="grid gap-4 sm:grid-cols-2">
                                        <div className="space-y-2">
                                            <Label htmlFor="end_date">
                                                End Date
                                            </Label>
                                            <Input
                                                id="end_date"
                                                type="date"
                                                value={data.end_date}
                                                onChange={(e) =>
                                                    setData(
                                                        "end_date",
                                                        e.target.value,
                                                    )
                                                }
                                            />
                                            {errors.end_date && (
                                                <p className="text-sm text-destructive">
                                                    {errors.end_date}
                                                </p>
                                            )}
                                        </div>

                                        <div className="space-y-2">
                                            <Label htmlFor="end_time">
                                                End Time
                                            </Label>
                                            <Input
                                                id="end_time"
                                                type="time"
                                                value={data.end_time}
                                                onChange={(e) =>
                                                    setData(
                                                        "end_time",
                                                        e.target.value,
                                                    )
                                                }
                                            />
                                            {errors.end_time && (
                                                <p className="text-sm text-destructive">
                                                    {errors.end_time}
                                                </p>
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
                                    <CardDescription>
                                        Configure announcement options
                                    </CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="priority">
                                            Priority
                                        </Label>
                                        <Select
                                            value={data.priority}
                                            onValueChange={(
                                                value:
                                                    | "low"
                                                    | "normal"
                                                    | "high"
                                                    | "urgent",
                                            ) => setData("priority", value)}
                                        >
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select priority" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="low">
                                                    Low
                                                </SelectItem>
                                                <SelectItem value="normal">
                                                    Normal
                                                </SelectItem>
                                                <SelectItem value="high">
                                                    High
                                                </SelectItem>
                                                <SelectItem value="urgent">
                                                    Urgent
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                        {errors.priority && (
                                            <p className="text-sm text-destructive">
                                                {errors.priority}
                                            </p>
                                        )}
                                    </div>

                                    <div className="flex items-center space-x-2">
                                        <Checkbox
                                            id="is_visible"
                                            checked={data.is_visible}
                                            onCheckedChange={(checked) =>
                                                setData(
                                                    "is_visible",
                                                    checked as boolean,
                                                )
                                            }
                                        />
                                        <Label
                                            htmlFor="is_visible"
                                            className="cursor-pointer"
                                        >
                                            Visible to users
                                        </Label>
                                    </div>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>Notifications</CardTitle>
                                    <CardDescription>
                                        Who should be notified
                                    </CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-3">
                                    <div className="flex items-center space-x-2">
                                        <Checkbox
                                            id="notify_all"
                                            checked={data.notify_user_types?.includes(
                                                "all",
                                            )}
                                            onCheckedChange={() =>
                                                toggleNotifyUserType("all")
                                            }
                                        />
                                        <Label
                                            htmlFor="notify_all"
                                            className="cursor-pointer"
                                        >
                                            All Users
                                        </Label>
                                    </div>
                                    <div className="flex items-center space-x-2">
                                        <Checkbox
                                            id="notify_tenant"
                                            checked={data.notify_user_types?.includes(
                                                "tenant",
                                            )}
                                            onCheckedChange={() =>
                                                toggleNotifyUserType("tenant")
                                            }
                                        />
                                        <Label
                                            htmlFor="notify_tenant"
                                            className="cursor-pointer"
                                        >
                                            Tenants
                                        </Label>
                                    </div>
                                    <div className="flex items-center space-x-2">
                                        <Checkbox
                                            id="notify_owner"
                                            checked={data.notify_user_types?.includes(
                                                "owner",
                                            )}
                                            onCheckedChange={() =>
                                                toggleNotifyUserType("owner")
                                            }
                                        />
                                        <Label
                                            htmlFor="notify_owner"
                                            className="cursor-pointer"
                                        >
                                            Owners
                                        </Label>
                                    </div>
                                </CardContent>
                            </Card>

                            <div className="flex gap-2">
                                <Button
                                    type="submit"
                                    className="flex-1"
                                    disabled={processing}
                                >
                                    <Save className="mr-2 h-4 w-4" />
                                    Save Changes
                                </Button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </>
    );
}

AnnouncementsEdit.layout = {
    breadcrumbs: [
        {
            title: "Announcements",
            href: "/announcements",
        },
        {
            title: "Edit",
            href: "#",
        },
    ],
};
