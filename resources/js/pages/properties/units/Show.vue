<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Unit } from '@/types';

const props = defineProps<{ unit: Unit }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Units', href: '/units' }, { title: 'Details', href: '#' }] } });

function deleteUnit() { if (confirm('Are you sure?')) { router.delete(`/units/${props.unit.id}`); } }
</script>

<template>
    <Head :title="unit.name" />
    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">{{ unit.name }}</h2>
                <p class="text-muted-foreground text-sm">{{ unit.community?.name }} &middot; {{ unit.building?.name ?? 'No building' }}</p>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child><Link :href="`/units/${unit.id}/edit`">Edit</Link></Button>
                <Button variant="destructive" @click="deleteUnit">Delete</Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-4">
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Category</CardTitle></CardHeader><CardContent><div class="font-semibold">{{ unit.category?.name ?? '—' }}</div></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Type</CardTitle></CardHeader><CardContent><div class="font-semibold">{{ unit.type?.name ?? '—' }}</div></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Status</CardTitle></CardHeader><CardContent><Badge>{{ unit.status?.name ?? '—' }}</Badge></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Area</CardTitle></CardHeader><CardContent><div class="font-semibold">{{ unit.net_area ? `${unit.net_area} sqm` : '—' }}</div></CardContent></Card>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>Details</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">Floor</span><span>{{ unit.floor_no ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Year Built</span><span>{{ unit.year_build ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Marketplace</span><Badge :variant="unit.is_market_place ? 'default' : 'secondary'">{{ unit.is_market_place ? 'Yes' : 'No' }}</Badge></div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>Occupancy</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">Owner</span><span>{{ unit.owner ? `${unit.owner.first_name} ${unit.owner.last_name}` : '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Tenant</span><span>{{ unit.tenant ? `${unit.tenant.first_name} ${unit.tenant.last_name}` : '—' }}</span></div>
                </CardContent>
            </Card>
        </div>

        <Card v-if="unit.about">
            <CardHeader><CardTitle>Description</CardTitle></CardHeader>
            <CardContent><p>{{ unit.about }}</p></CardContent>
        </Card>
    </div>
</template>
