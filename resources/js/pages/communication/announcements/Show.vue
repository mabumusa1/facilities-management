<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Announcement } from '@/types';

const props = defineProps<{ announcement: Announcement }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Announcements', href: '/announcements' }, { title: 'Details', href: '#' }] } });

function deleteAnnouncement() { if (confirm('Are you sure?')) { router.delete(`/announcements/${props.announcement.id}`); } }
</script>

<template>
    <Head :title="announcement.title" />
    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">{{ announcement.title }}</h2>
                <div class="mt-1 flex items-center gap-2">
                    <Badge :variant="announcement.is_published ? 'default' : 'secondary'">{{ announcement.is_published ? 'Published' : 'Draft' }}</Badge>
                    <span class="text-muted-foreground text-sm">{{ announcement.created_at }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child><a :href="`/announcements/${announcement.id}/edit`">Edit</a></Button>
                <Button variant="destructive" @click="deleteAnnouncement">Delete</Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>Scope</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">Community</span><span>{{ announcement.community?.name ?? 'All' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Building</span><span>{{ announcement.building?.name ?? 'All' }}</span></div>
                </CardContent>
            </Card>
        </div>

        <Card>
            <CardHeader><CardTitle>Content</CardTitle></CardHeader>
            <CardContent><p class="whitespace-pre-wrap">{{ announcement.body }}</p></CardContent>
        </Card>
    </div>
</template>
