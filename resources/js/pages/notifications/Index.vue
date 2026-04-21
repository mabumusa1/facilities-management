<script setup lang="ts">
import { Head, router, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
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

const { t } = useI18n();

function markAsRead(id: string) {
    router.post(`/notifications/${id}/mark-as-read`);
}

function markAllAsRead() {
    router.post('/notifications/mark-all-as-read');
}

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.notifications.pageTitle'), href: '/notifications' },
        ],
    });
});
</script>

<template>
    <Head :title="t('app.notifications.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading
                variant="small"
                :title="t('app.notifications.heading')"
                :description="t('app.notifications.description')"
            />
            <div class="flex items-center gap-2">
                <Badge variant="secondary">{{ t('app.notifications.unread') }}: {{ props.unreadCount }}</Badge>
                <Button variant="outline" size="sm" @click="markAllAsRead">{{ t('app.actions.markAllAsRead') }}</Button>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.notifications.recent') }}</CardTitle>
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
                        {{ t('app.actions.markRead') }}
                    </Button>
                    <Badge v-else variant="secondary">{{ t('app.notifications.read') }}</Badge>
                </div>

                <p v-if="props.notifications.data.length === 0" class="text-muted-foreground text-sm">{{ t('app.notifications.noNotificationsYet') }}</p>
            </CardContent>
        </Card>
    </div>
</template>
