<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useI18n } from '@/composables/useI18n';
import type { ServiceRequest } from '@/types';

const props = defineProps<{ serviceRequest: ServiceRequest }>();
const { t } = useI18n();

const requesterName = computed(() => {
    const requester = (props.serviceRequest as any).requester;
    if (!requester) {
        return t('app.common.notAvailable');
    }

    return `${requester.first_name ?? ''} ${requester.last_name ?? ''}`.trim() || t('app.common.notAvailable');
});

const assigneeName = computed(() => {
    const assignee = (props.serviceRequest as any).assignee;
    if (!assignee) {
        return t('app.common.notAvailable');
    }

    return `${assignee.first_name ?? ''} ${assignee.last_name ?? ''}`.trim() || t('app.common.notAvailable');
});

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.requests.pageTitle'), href: '/requests' },
            { title: t('app.requests.show.breadcrumb'), href: '#' },
        ],
    });
});

function deleteRequest() {
    if (confirm(t('app.requests.show.confirmDeletePrompt'))) {
        router.delete(`/requests/${props.serviceRequest.id}`);
    }
}
</script>

<template>
    <Head :title="t('app.requests.show.pageTitle', { id: serviceRequest.id })" />
    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">{{ t('app.requests.show.pageTitle', { id: serviceRequest.id }) }}</h2>
                <p class="text-muted-foreground text-sm">{{ serviceRequest.category?.name }} &middot; {{ serviceRequest.subcategory?.name }}</p>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child><Link :href="`/requests/${serviceRequest.id}/edit`">{{ t('app.actions.edit') }}</Link></Button>
                <Button variant="destructive" @click="deleteRequest">{{ t('app.actions.delete') }}</Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.requests.show.status') }}</CardTitle></CardHeader><CardContent><Badge>{{ serviceRequest.status?.name ?? '—' }}</Badge></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.requests.show.priority') }}</CardTitle></CardHeader><CardContent><Badge variant="secondary">{{ serviceRequest.priority ?? '—' }}</Badge></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.requests.show.created') }}</CardTitle></CardHeader><CardContent><span>{{ serviceRequest.created_at }}</span></CardContent></Card>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>{{ t('app.requests.show.location') }}</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.requests.show.community') }}</span><span>{{ serviceRequest.community?.name ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.requests.show.unit') }}</span><span>{{ serviceRequest.unit?.name ?? '—' }}</span></div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>{{ t('app.requests.show.people') }}</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.requests.show.requester') }}</span><span>{{ requesterName }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.requests.show.assignee') }}</span><span>{{ assigneeName }}</span></div>
                </CardContent>
            </Card>
        </div>

        <Card v-if="serviceRequest.description">
            <CardHeader><CardTitle>{{ t('app.requests.show.description') }}</CardTitle></CardHeader>
            <CardContent><p class="whitespace-pre-wrap">{{ serviceRequest.description }}</p></CardContent>
        </Card>
    </div>
</template>
