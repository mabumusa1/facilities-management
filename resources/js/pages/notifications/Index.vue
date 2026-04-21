<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

type NotificationItem = {
    id: string;
    text: string;
    type: string;
    read: string | null;
    created_at: string;
};

const props = defineProps<{
    notifications: {
        data: NotificationItem[];
    };
    unreadCount: number;
}>();

function markAsRead(id: string) {
    router.post(`/notifications/${id}/mark-as-read`);
}

function markAllAsRead() {
    router.post('/notifications/mark-all-as-read');
}

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Notifications', href: '/notifications' },
        ],
    },
});
</script>

<template>
    <Head title="Notifications" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading variant="small" title="Notifications" description="Track system activity and mark items as read." />
            <div class="flex items-center gap-2">
                <Badge variant="secondary">Unread: {{ props.unreadCount }}</Badge>
                <Button variant="outline" size="sm" @click="markAllAsRead">Mark All as Read</Button>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Recent Notifications</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3">
                <div
                    v-for="notification in props.notifications.data"
                    :key="notification.id"
                    class="flex items-start justify-between gap-3 rounded-md border p-3"
                >
                    <div class="space-y-1">
                        <p class="text-sm font-medium">{{ notification.text }}</p>
                        <p class="text-muted-foreground text-xs">{{ notification.type }} • {{ notification.created_at }}</p>
                    </div>
                    <Button
                        v-if="!notification.read"
                        size="sm"
                        variant="outline"
                        @click="markAsRead(notification.id)"
                    >
                        Mark Read
                    </Button>
                    <Badge v-else variant="secondary">Read</Badge>
                </div>

                <p v-if="props.notifications.data.length === 0" class="text-muted-foreground text-sm">No notifications yet.</p>
            </CardContent>
        </Card>
    </div>
</template>
