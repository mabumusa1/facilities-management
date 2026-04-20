<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Building } from '@/types';

const props = defineProps<{ building: Building }>();

defineOptions({ layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Buildings', href: '/buildings' }, { title: 'Details', href: '#' }] } });

function deleteBuilding() {
    if (confirm('Are you sure?')) { router.delete(`/buildings/${props.building.id}`); }
}
</script>

<template>
    <Head :title="building.name" />
    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">{{ building.name }}</h2>
                <p class="text-muted-foreground text-sm">{{ building.community?.name }}</p>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child><Link :href="`/buildings/${building.id}/edit`">Edit</Link></Button>
                <Button variant="destructive" @click="deleteBuilding">Delete</Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Units</CardTitle></CardHeader><CardContent><div class="text-2xl font-bold">{{ building.units_count ?? 0 }}</div></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Floors</CardTitle></CardHeader><CardContent><div class="text-2xl font-bold">{{ building.no_floors ?? '—' }}</div></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Year Built</CardTitle></CardHeader><CardContent><div class="text-2xl font-bold">{{ building.year_build ?? '—' }}</div></CardContent></Card>
        </div>

        <Card v-if="building.units && building.units.length > 0">
            <CardHeader><CardTitle>Units</CardTitle></CardHeader>
            <CardContent>
                <div class="space-y-2">
                    <Link v-for="unit in building.units" :key="unit.id" :href="`/units/${unit.id}`" class="flex items-center justify-between rounded-md border p-3 hover:bg-muted/50">
                        <span class="font-medium">{{ unit.name }}</span>
                        <span class="text-muted-foreground text-sm">{{ unit.status?.name }}</span>
                    </Link>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
