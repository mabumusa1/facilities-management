<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { ServiceRequest } from '@/types';

const props = defineProps<{ serviceRequest: ServiceRequest }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Requests', href: '/requests' }, { title: 'Details', href: '#' }] } });

function deleteRequest() { if (confirm('Are you sure?')) { router.delete(`/requests/${props.serviceRequest.id}`); } }
</script>

<template>
    <Head :title="`Request #${serviceRequest.id}`" />
    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Request #{{ serviceRequest.id }}</h2>
                <p class="text-muted-foreground text-sm">{{ serviceRequest.category?.name }} &middot; {{ serviceRequest.subcategory?.name }}</p>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child><Link :href="`/requests/${serviceRequest.id}/edit`">Edit</Link></Button>
                <Button variant="destructive" @click="deleteRequest">Delete</Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Status</CardTitle></CardHeader><CardContent><Badge>{{ serviceRequest.status?.name ?? '—' }}</Badge></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Priority</CardTitle></CardHeader><CardContent><Badge variant="secondary">{{ serviceRequest.priority?.name ?? '—' }}</Badge></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Created</CardTitle></CardHeader><CardContent><span>{{ serviceRequest.created_at }}</span></CardContent></Card>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>Location</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">Community</span><span>{{ serviceRequest.community?.name ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Unit</span><span>{{ serviceRequest.unit?.name ?? '—' }}</span></div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>People</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">Requester</span><span>{{ serviceRequest.requester?.first_name }} {{ serviceRequest.requester?.last_name }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Assignee</span><span>{{ serviceRequest.assignee ? `${serviceRequest.assignee.first_name} ${serviceRequest.assignee.last_name}` : '—' }}</span></div>
                </CardContent>
            </Card>
        </div>

        <Card v-if="serviceRequest.description">
            <CardHeader><CardTitle>Description</CardTitle></CardHeader>
            <CardContent><p class="whitespace-pre-wrap">{{ serviceRequest.description }}</p></CardContent>
        </Card>
    </div>
</template>
