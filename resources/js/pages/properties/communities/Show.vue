<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useI18n } from '@/composables/useI18n';
import type { Community } from '@/types';

const props = defineProps<{ community: Community }>();

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.properties.communities.pageTitle'), href: '/communities' },
            { title: t('app.properties.communities.show.breadcrumb'), href: '#' },
        ],
    });
});

function deleteCommunity() {
    if (confirm(t('app.properties.communities.show.confirmDeletePrompt'))) {
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
                <p class="text-muted-foreground text-sm">{{ t('app.properties.communities.show.detailsDescription') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child>
                    <Link :href="`/communities/${community.id}/edit`">{{ t('app.actions.edit') }}</Link>
                </Button>
                <Button variant="destructive" @click="deleteCommunity">{{ t('app.actions.delete') }}</Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <Card>
                <CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.properties.communities.show.buildings') }}</CardTitle></CardHeader>
                <CardContent><div class="text-2xl font-bold">{{ community.buildings_count ?? 0 }}</div></CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.properties.communities.show.units') }}</CardTitle></CardHeader>
                <CardContent><div class="text-2xl font-bold">{{ community.units_count ?? 0 }}</div></CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.properties.communities.show.requests') }}</CardTitle></CardHeader>
                <CardContent><div class="text-2xl font-bold">{{ community.requests_count ?? 0 }}</div></CardContent>
            </Card>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>{{ t('app.properties.communities.show.location') }}</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.properties.communities.show.country') }}</span><span>{{ community.country?.name ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.properties.communities.show.city') }}</span><span>{{ community.city?.name ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.properties.communities.show.district') }}</span><span>{{ community.district?.name ?? '—' }}</span></div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>{{ t('app.properties.communities.show.financials') }}</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.properties.communities.show.currency') }}</span><span>{{ community.currency?.name ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.properties.communities.show.salesCommission') }}</span><span>{{ community.sales_commission_rate ?? '—' }}%</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.properties.communities.show.rentalCommission') }}</span><span>{{ community.rental_commission_rate ?? '—' }}%</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.properties.communities.show.marketplace') }}</span><Badge :variant="community.is_market_place ? 'default' : 'secondary'">{{ community.is_market_place ? t('app.common.yes') : t('app.common.no') }}</Badge></div>
                </CardContent>
            </Card>
        </div>

        <Card v-if="community.buildings && community.buildings.length > 0">
            <CardHeader><CardTitle>{{ t('app.properties.communities.show.relatedBuildings') }}</CardTitle></CardHeader>
            <CardContent>
                <div class="space-y-2">
                    <Link v-for="building in community.buildings" :key="building.id" :href="`/buildings/${building.id}`" class="flex items-center justify-between rounded-md border p-3 hover:bg-muted/50">
                        <span class="font-medium">{{ building.name }}</span>
                        <span class="text-muted-foreground text-sm">{{ t('app.properties.communities.show.buildingUnits', { count: building.units_count ?? 0 }) }}</span>
                    </Link>
                </div>
            </CardContent>
        </Card>

        <Card v-if="community.facilities && community.facilities.length > 0">
            <CardHeader><CardTitle>{{ t('app.properties.communities.show.facilities') }}</CardTitle></CardHeader>
            <CardContent>
                <div class="space-y-2">
                    <Link v-for="facility in community.facilities" :key="facility.id" :href="`/facilities/${facility.id}`" class="flex items-center justify-between rounded-md border p-3 hover:bg-muted/50">
                        <span class="font-medium">{{ facility.name }}</span>
                        <Badge :variant="facility.is_active ? 'default' : 'secondary'">{{ facility.is_active ? t('app.common.active') : t('app.common.inactive') }}</Badge>
                    </Link>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
