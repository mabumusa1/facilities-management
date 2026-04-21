<script setup lang="ts">
import { Head, router, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Announcement } from '@/types';

const { t } = useI18n();

const props = defineProps<{ announcement: Announcement }>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.announcements'), href: '/announcements' },
            { title: t('app.announcements.details'), href: '#' },
        ],
    });
});

function deleteAnnouncement() { if (confirm(t('app.announcements.confirmDelete'))) { router.delete(`/announcements/${props.announcement.id}`); } }
</script>

<template>
    <Head :title="announcement.title" />
    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">{{ announcement.title }}</h2>
                <div class="mt-1 flex items-center gap-2">
                    <Badge :variant="announcement.published_at ? 'default' : 'secondary'">{{ announcement.published_at ? t('app.announcements.published') : t('app.announcements.draft') }}</Badge>
                    <span class="text-muted-foreground text-sm">{{ announcement.created_at }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child><a :href="`/announcements/${announcement.id}/edit`">{{ t('app.actions.edit') }}</a></Button>
                <Button variant="destructive" @click="deleteAnnouncement">{{ t('app.actions.delete') }}</Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>{{ t('app.announcements.scope') }}</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.announcements.community') }}</span><span>{{ announcement.community?.name ?? t('app.announcements.all') }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.announcements.building') }}</span><span>{{ announcement.building?.name ?? t('app.announcements.all') }}</span></div>
                </CardContent>
            </Card>
        </div>

        <Card>
            <CardHeader><CardTitle>{{ t('app.announcements.content') }}</CardTitle></CardHeader>
            <CardContent><p class="whitespace-pre-wrap">{{ announcement.body }}</p></CardContent>
        </Card>
    </div>
</template>
