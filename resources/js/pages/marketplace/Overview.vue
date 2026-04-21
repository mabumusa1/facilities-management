<script setup lang="ts">
import { Head, Link, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.marketplace'), href: '/marketplace' },
        ],
    });
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
    <Head :title="t('app.marketplace.overview.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading variant="small" :title="t('app.marketplace.overview.heading')" :description="t('app.marketplace.overview.description')" />
            <div class="flex gap-2">
                <Button variant="outline" as-child>
                    <Link href="/marketplace/customers">{{ t('app.navigation.customers') }}</Link>
                </Button>
                <Button as-child>
                    <Link href="/marketplace/listing">{{ t('app.navigation.listing') }}</Link>
                </Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <Card>
                <CardHeader><CardTitle class="text-sm">{{ t('app.marketplace.overview.activeListings') }}</CardTitle></CardHeader>
                <CardContent class="text-2xl font-bold">{{ props.stats.activeListings }}</CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle class="text-sm">{{ t('app.marketplace.overview.totalLeads') }}</CardTitle></CardHeader>
                <CardContent class="text-2xl font-bold">{{ props.stats.totalLeads }}</CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle class="text-sm">{{ t('app.marketplace.overview.scheduledVisits') }}</CardTitle></CardHeader>
                <CardContent class="text-2xl font-bold">{{ props.stats.scheduledVisits }}</CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle class="text-sm">{{ t('app.marketplace.overview.listedCommunities') }}</CardTitle></CardHeader>
                <CardContent class="text-2xl font-bold">{{ props.stats.listedCommunities }}</CardContent>
            </Card>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.marketplace.overview.recentListings') }}</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3">
                <div v-for="listing in props.recentListings" :key="listing.id" class="rounded-md border p-3 text-sm">
                    <p class="font-medium">{{ listing.unit?.name ?? t('app.marketplace.common.listingFallback', { id: listing.id }) }}</p>
                    <p>{{ t('app.marketplace.common.type') }}: {{ listing.listing_type }} | {{ t('app.marketplace.common.price') }}: {{ listing.price }}</p>
                    <p>{{ t('app.marketplace.common.status') }}: {{ listing.is_active ? t('app.common.active') : t('app.common.inactive') }}</p>
                </div>
                <p v-if="props.recentListings.length === 0" class="text-muted-foreground text-sm">{{ t('app.marketplace.overview.noListingsFound') }}</p>
            </CardContent>
        </Card>
    </div>
</template>
