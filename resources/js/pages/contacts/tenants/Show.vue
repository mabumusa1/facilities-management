<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Resident } from '@/types';

const props = defineProps<{ resident: Resident }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Tenants', href: '/residents' }, { title: 'Details', href: '#' }] } });

function deleteResident() { if (confirm('Are you sure?')) { router.delete(`/residents/${props.resident.id}`); } }
</script>

<template>
    <Head :title="`${resident.first_name} ${resident.last_name}`" />
    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">{{ resident.first_name }} {{ resident.last_name }}</h2>
                <Badge :variant="resident.active ? 'default' : 'secondary'">{{ resident.active ? 'Active' : 'Inactive' }}</Badge>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child><Link :href="`/residents/${resident.id}/edit`">Edit</Link></Button>
                <Button variant="destructive" @click="deleteResident">Delete</Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Units</CardTitle></CardHeader><CardContent><div class="text-2xl font-bold">{{ resident.units_count ?? 0 }}</div></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Leases</CardTitle></CardHeader><CardContent><div class="text-2xl font-bold">{{ resident.leases_count ?? 0 }}</div></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Status</CardTitle></CardHeader><CardContent><Badge :variant="resident.active ? 'default' : 'secondary'">{{ resident.active ? 'Active' : 'Inactive' }}</Badge></CardContent></Card>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>Contact Info</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">Email</span><span>{{ resident.email ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Phone</span><span>{{ resident.phone_number }}</span></div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>Personal Info</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">National ID</span><span>{{ resident.national_id ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Gender</span><span>{{ resident.gender ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Date of Birth</span><span>{{ resident.georgian_birthdate ?? '—' }}</span></div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
