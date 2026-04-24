<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useI18n } from '@/composables/useI18n';
import type { Amenity, Community } from '@/types';

const props = defineProps<{ community: Community }>();

const { t, isArabic } = useI18n();

const WORKING_DAY_KEYS = ['sat', 'sun', 'mon', 'tue', 'wed', 'thu', 'fri'] as const;
type DayKey = (typeof WORKING_DAY_KEYS)[number];

function dayLabel(key: DayKey): string {
    return t(`app.properties.communities.show.${key}`);
}

function isDayWorking(day: string): boolean {
    return (props.community.working_days ?? []).includes(day);
}

function amenityDisplayName(amenity: Pick<Amenity, 'id' | 'name' | 'name_en' | 'name_ar'>): string {
    return (isArabic.value ? amenity.name_ar : amenity.name_en) ?? amenity.name;
}

const formattedCoordinates = computed(() => {
    const lat = props.community.latitude;
    const lng = props.community.longitude;
    if (lat === null || lat === undefined || lng === null || lng === undefined) {
        return null;
    }
    return `${Number(lat).toFixed(4)}, ${Number(lng).toFixed(4)}`;
});

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

        <!-- Amenities -->
        <Card>
            <CardHeader><CardTitle>{{ t('app.properties.communities.show.amenities') }}</CardTitle></CardHeader>
            <CardContent>
                <div v-if="community.amenities && community.amenities.length > 0" class="flex flex-wrap gap-2">
                    <Badge v-for="amenity in community.amenities" :key="amenity.id" variant="secondary">
                        {{ amenityDisplayName(amenity) }}
                    </Badge>
                </div>
                <p v-else class="text-muted-foreground text-sm">{{ t('app.properties.communities.show.noAmenities') }}</p>
            </CardContent>
        </Card>

        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>{{ t('app.properties.communities.show.location') }}</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.properties.communities.show.country') }}</span><span>{{ community.country?.name ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.properties.communities.show.city') }}</span><span>{{ community.city?.name ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.properties.communities.show.district') }}</span><span>{{ community.district?.name ?? '—' }}</span></div>
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">{{ t('app.properties.communities.show.coordinates') }}</span>
                        <span v-if="formattedCoordinates" dir="ltr" class="font-mono text-sm">{{ formattedCoordinates }}</span>
                        <span v-else>{{ t('app.properties.communities.show.noCoordinates') }}</span>
                    </div>
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

        <!-- Working Days -->
        <Card>
            <CardHeader><CardTitle>{{ t('app.properties.communities.show.workingDays') }}</CardTitle></CardHeader>
            <CardContent>
                <div
                    v-if="community.working_days && community.working_days.length > 0"
                    class="grid grid-cols-7 gap-1"
                >
                    <div
                        v-for="day in WORKING_DAY_KEYS"
                        :key="day"
                        :aria-label="`${dayLabel(day)}: ${isDayWorking(day) ? t('app.properties.communities.show.aria.working_day') : t('app.properties.communities.show.aria.non_working_day')}`"
                        :class="[
                            'flex min-w-[40px] flex-col items-center rounded-md border px-1 py-2 text-xs font-medium',
                            isDayWorking(day)
                                ? 'bg-primary/20 text-primary border-primary/30'
                                : 'text-muted-foreground border-border bg-background',
                        ]"
                    >
                        {{ dayLabel(day) }}
                    </div>
                </div>
                <p v-else class="text-muted-foreground text-sm">{{ t('app.properties.communities.show.noDays') }}</p>
            </CardContent>
        </Card>

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
