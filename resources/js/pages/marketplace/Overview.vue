<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Marketplace', href: '/marketplace' },
        ],
    },
});

const props = defineProps<{
    stats: {
        activeListings: number;
        totalLeads: number;
        scheduledVisits: number;
        listedCommunities: number;
    };
    recentListings: Array<{
        id: number;
        listing_type: string;
        price: string;
        is_active: boolean;
        unit?: { name?: string | null } | null;
    }>;
}>();
</script>

<template>
    <Head title="Marketplace" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading variant="small" title="Marketplace" description="Overview of listings, leads, and visits." />
            <div class="flex gap-2">
                <Button variant="outline" as-child>
                    <Link href="/marketplace/customers">Customers</Link>
                </Button>
                <Button as-child>
                    <Link href="/marketplace/listing">Listings</Link>
                </Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <Card>
                <CardHeader><CardTitle class="text-sm">Active Listings</CardTitle></CardHeader>
                <CardContent class="text-2xl font-bold">{{ props.stats.activeListings }}</CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle class="text-sm">Total Leads</CardTitle></CardHeader>
                <CardContent class="text-2xl font-bold">{{ props.stats.totalLeads }}</CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle class="text-sm">Scheduled Visits</CardTitle></CardHeader>
                <CardContent class="text-2xl font-bold">{{ props.stats.scheduledVisits }}</CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle class="text-sm">Listed Communities</CardTitle></CardHeader>
                <CardContent class="text-2xl font-bold">{{ props.stats.listedCommunities }}</CardContent>
            </Card>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Recent Listings</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3">
                <div v-for="listing in props.recentListings" :key="listing.id" class="rounded-md border p-3 text-sm">
                    <p class="font-medium">{{ listing.unit?.name ?? `Listing #${listing.id}` }}</p>
                    <p>Type: {{ listing.listing_type }} | Price: {{ listing.price }}</p>
                    <p>Status: {{ listing.is_active ? 'Active' : 'Inactive' }}</p>
                </div>
                <p v-if="props.recentListings.length === 0" class="text-muted-foreground text-sm">No listings found.</p>
            </CardContent>
        </Card>
    </div>
</template>
