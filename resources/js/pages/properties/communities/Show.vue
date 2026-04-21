<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Community } from '@/types';

const props = defineProps<{ community: Community }>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Communities', href: '/communities' },
            { title: 'Details', href: '#' },
        ],
    },
});

function deleteCommunity() {
    if (confirm('Are you sure you want to delete this community?')) {
        router.delete(`/communities/${props.community.id}`);
    }
}
</script>

<template>
    <Head :title="community.name" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">{{ community.name }}</h2>
                <p class="text-muted-foreground text-sm">Community details and statistics</p>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child>
                    <Link :href="`/communities/${community.id}/edit`">Edit</Link>
                </Button>
                <Button variant="destructive" @click="deleteCommunity">Delete</Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <Card>
                <CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Buildings</CardTitle></CardHeader>
                <CardContent><div class="text-2xl font-bold">{{ community.buildings_count ?? 0 }}</div></CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Units</CardTitle></CardHeader>
                <CardContent><div class="text-2xl font-bold">{{ community.units_count ?? 0 }}</div></CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Requests</CardTitle></CardHeader>
                <CardContent><div class="text-2xl font-bold">{{ community.requests_count ?? 0 }}</div></CardContent>
            </Card>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>Location</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">Country</span><span>{{ community.country?.name ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">City</span><span>{{ community.city?.name ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">District</span><span>{{ community.district?.name ?? '—' }}</span></div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>Financials</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">Currency</span><span>{{ community.currency?.name ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Sales Commission</span><span>{{ community.sales_commission_rate ?? '—' }}%</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Rental Commission</span><span>{{ community.rental_commission_rate ?? '—' }}%</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Marketplace</span><Badge :variant="community.is_market_place ? 'default' : 'secondary'">{{ community.is_market_place ? 'Yes' : 'No' }}</Badge></div>
                </CardContent>
            </Card>
        </div>

        <Card v-if="community.buildings && community.buildings.length > 0">
            <CardHeader><CardTitle>Buildings</CardTitle></CardHeader>
            <CardContent>
                <div class="space-y-2">
                    <Link v-for="building in community.buildings" :key="building.id" :href="`/buildings/${building.id}`" class="flex items-center justify-between rounded-md border p-3 hover:bg-muted/50">
                        <span class="font-medium">{{ building.name }}</span>
                        <span class="text-muted-foreground text-sm">{{ building.units_count ?? 0 }} units</span>
                    </Link>
                </div>
            </CardContent>
        </Card>

        <Card v-if="community.facilities && community.facilities.length > 0">
            <CardHeader><CardTitle>Facilities</CardTitle></CardHeader>
            <CardContent>
                <div class="space-y-2">
                    <Link v-for="facility in community.facilities" :key="facility.id" :href="`/facilities/${facility.id}`" class="flex items-center justify-between rounded-md border p-3 hover:bg-muted/50">
                        <span class="font-medium">{{ facility.name }}</span>
                        <Badge :variant="facility.is_active ? 'default' : 'secondary'">{{ facility.is_active ? 'Active' : 'Inactive' }}</Badge>
                    </Link>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
