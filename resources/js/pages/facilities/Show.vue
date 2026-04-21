<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Facility } from '@/types';

const props = defineProps<{ facility: Facility }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Facilities', href: '/facilities' }, { title: 'Details', href: '#' }] } });

function deleteFacility() { if (confirm('Are you sure?')) { router.delete(`/facilities/${props.facility.id}`); } }
</script>

<template>
    <Head :title="facility.name" />
    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">{{ facility.name }}</h2>
                <p class="text-muted-foreground text-sm">{{ facility.category?.name }} &middot; {{ facility.community?.name }}</p>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child><a :href="`/facilities/${facility.id}/edit`">Edit</a></Button>
                <Button variant="destructive" @click="deleteFacility">Delete</Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Capacity</CardTitle></CardHeader><CardContent><div class="text-2xl font-bold">{{ facility.capacity ?? '—' }}</div></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Status</CardTitle></CardHeader><CardContent><Badge :variant="facility.is_active ? 'default' : 'secondary'">{{ facility.is_active ? 'Active' : 'Inactive' }}</Badge></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Bookings</CardTitle></CardHeader><CardContent><div class="text-2xl font-bold">{{ (facility as any).bookings_count ?? 0 }}</div></CardContent></Card>
        </div>

        <Card v-if="(facility as any).about">
            <CardHeader><CardTitle>Description</CardTitle></CardHeader>
            <CardContent><p>{{ (facility as any).about }}</p></CardContent>
        </Card>
    </div>
</template>
