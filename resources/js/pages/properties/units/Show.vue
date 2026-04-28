<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useI18n } from '@/composables/useI18n';
import type { Unit } from '@/types';

const props = defineProps<{ unit: Unit }>();

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.properties.units.pageTitle'), href: '/units' },
            { title: t('app.properties.units.show.breadcrumb'), href: '#' },
        ],
    });
});

function deleteUnit() {
    if (confirm(t('app.properties.units.show.confirmDeletePrompt'))) {
        router.delete(`/units/${props.unit.id}`);
    }
}

/** Extract a specification value by key. */
function specValue(key: string): string | null {
    return props.unit.specifications?.find((s) => s.key === key)?.value ?? null;
}

/** Extract a room count by name. */
function roomCount(name: string): number {
    return Number(props.unit.rooms?.find((r) => r.name === name)?.count ?? 0);
}

const isFurnished = computed(() => specValue('furnished') === 'true');
const parkingBays = computed(() => Number(specValue('parking_bays') ?? 0));
const viewValue = computed(() => specValue('view'));

const viewLabel = computed(() => {
    const map: Record<string, string> = {
        sea_view: t('app.properties.units.edit.specifications.viewSea'),
        city_view: t('app.properties.units.edit.specifications.viewCity'),
        garden_view: t('app.properties.units.edit.specifications.viewGarden'),
        none: t('app.properties.units.edit.specifications.viewNone'),
    };
    return viewValue.value ? (map[viewValue.value] ?? viewValue.value) : null;
});

const hasSpecifications = computed(
    () => props.unit.specifications && props.unit.specifications.length > 0,
);

const amenities = computed(() => props.unit.features ?? []);

const rentPeriodLabel = computed(() => {
    if (props.unit.rent_period === 'year') {
        return t('app.properties.units.show.rentPeriodYear');
    }
    if (props.unit.rent_period === 'month') {
        return t('app.properties.units.show.rentPeriodMonth');
    }
    return null;
});
</script>

<template>
    <Head :title="unit.name" />
    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">{{ unit.name }}</h2>
                <p class="text-muted-foreground text-sm">{{ unit.community?.name }} &middot; {{ unit.building?.name ?? t('app.properties.units.show.noBuilding') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child><Link :href="`/units/${unit.id}/edit`">{{ t('app.actions.edit') }}</Link></Button>
                <Button variant="destructive" @click="deleteUnit">{{ t('app.actions.delete') }}</Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-4">
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.properties.units.show.category') }}</CardTitle></CardHeader><CardContent><div class="font-semibold">{{ unit.category?.name ?? '—' }}</div></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.properties.units.show.type') }}</CardTitle></CardHeader><CardContent><div class="font-semibold">{{ unit.type?.name ?? '—' }}</div></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.properties.units.show.status') }}</CardTitle></CardHeader><CardContent><Badge>{{ unit.status?.name ?? '—' }}</Badge></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.properties.units.show.area') }}</CardTitle></CardHeader><CardContent><div class="font-semibold">{{ unit.net_area ? t('app.properties.units.show.areaValue', { value: unit.net_area }) : '—' }}</div></CardContent></Card>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>{{ t('app.properties.units.show.details') }}</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.properties.units.show.floor') }}</span><span>{{ unit.floor_no ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.properties.units.show.yearBuilt') }}</span><span>{{ unit.year_build ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.properties.units.show.marketplace') }}</span><Badge :variant="unit.is_market_place ? 'default' : 'secondary'">{{ unit.is_market_place ? t('app.common.yes') : t('app.common.no') }}</Badge></div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>{{ t('app.properties.units.show.occupancy') }}</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.properties.units.show.owner') }}</span><span>{{ unit.owner ? `${unit.owner.first_name} ${unit.owner.last_name}` : '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.properties.units.show.tenant') }}</span><span>{{ unit.tenant ? `${unit.tenant.first_name} ${unit.tenant.last_name}` : '—' }}</span></div>
                </CardContent>
            </Card>
        </div>

        <!-- Specifications Card (NEW) -->
        <Card v-if="hasSpecifications">
            <CardHeader><CardTitle>{{ t('app.properties.units.show.specifications') }}</CardTitle></CardHeader>
            <CardContent class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">{{ t('app.properties.units.edit.specifications.bedrooms') }}</span>
                    <span>{{ roomCount('bedroom') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">{{ t('app.properties.units.edit.specifications.bathrooms') }}</span>
                    <span>{{ roomCount('bathroom') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">{{ t('app.properties.units.edit.specifications.livingRooms') }}</span>
                    <span>{{ roomCount('living_room') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">{{ t('app.properties.units.edit.specifications.furnished') }}</span>
                    <span>{{ isFurnished ? t('app.properties.units.show.furnished') : t('app.properties.units.show.unfurnished') }}</span>
                </div>
                <div v-if="parkingBays > 0" class="flex justify-between">
                    <span class="text-muted-foreground">{{ t('app.properties.units.edit.specifications.parking') }}</span>
                    <span>{{ parkingBays }}</span>
                </div>
                <div v-if="viewLabel" class="flex justify-between">
                    <span class="text-muted-foreground">{{ t('app.properties.units.edit.specifications.view') }}</span>
                    <span>{{ viewLabel }}</span>
                </div>
            </CardContent>
        </Card>

        <!-- Amenities Card (NEW) -->
        <Card>
            <CardHeader><CardTitle>{{ t('app.properties.units.show.amenities') }}</CardTitle></CardHeader>
            <CardContent>
                <div v-if="amenities.length > 0" class="flex flex-wrap gap-2">
                    <Badge v-for="amenity in amenities" :key="amenity.id" variant="secondary">
                        {{ amenity.name_en ?? amenity.name }}
                    </Badge>
                </div>
                <p v-else class="text-muted-foreground text-sm">{{ t('app.properties.units.show.noAmenities') }}</p>
            </CardContent>
        </Card>

        <!-- Pricing Card (NEW) -->
        <Card v-if="unit.asking_rent_amount">
            <CardHeader><CardTitle>{{ t('app.properties.units.show.pricing') }}</CardTitle></CardHeader>
            <CardContent class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">{{ t('app.properties.units.show.askingRent') }}</span>
                    <span class="font-semibold">
                        {{ unit.currency?.code ?? '' }}
                        {{ Number(unit.asking_rent_amount).toLocaleString() }}
                        <span v-if="rentPeriodLabel" class="text-muted-foreground font-normal"> / {{ rentPeriodLabel }}</span>
                    </span>
                </div>
            </CardContent>
        </Card>

        <Card v-if="unit.about">
            <CardHeader><CardTitle>{{ t('app.properties.units.show.descriptionLabel') }}</CardTitle></CardHeader>
            <CardContent><p>{{ unit.about }}</p></CardContent>
        </Card>
    </div>
</template>
