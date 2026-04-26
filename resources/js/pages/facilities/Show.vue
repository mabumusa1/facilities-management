<script setup lang="ts">
import { Head, router, setLayoutProps } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Facility } from '@/types';

const { isArabic, t } = useI18n();

const props = defineProps<{
    facility: Facility;
    upcomingBookingsCount: number;
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.facilities'), href: '/facilities' },
            { title: t('app.facilities.details'), href: '#' },
        ],
    });
});

function deleteFacility() {
    if (confirm(t('app.common.warning'))) {
        router.delete(`/facilities/${props.facility.id}`);
    }
}

function localizedCategoryName(): string {
    if (!props.facility.category) {
        return t('app.common.notAvailable');
    }

    if (isArabic.value) {
        return props.facility.category.name_ar ?? props.facility.category.name ?? props.facility.category.name_en ?? t('app.common.notAvailable');
    }

    return props.facility.category.name_en ?? props.facility.category.name ?? props.facility.category.name_ar ?? t('app.common.notAvailable');
}

const pricingLabel = computed(() => {
    const f = props.facility;

    if (f.pricing_mode === 'free') {
        return t('app.facilities.pricingFree');
    }

    const mode = f.pricing_mode === 'per_session' ? t('app.facilities.pricingPerSession') : t('app.facilities.pricingPerHour');

    if (f.booking_fee) {
        return `${mode} · ${f.currency} ${f.booking_fee}`;
    }

    return mode;
});

const dayNames = computed(() => [
    t('app.facilities.sunday'),
    t('app.facilities.monday'),
    t('app.facilities.tuesday'),
    t('app.facilities.wednesday'),
    t('app.facilities.thursday'),
    t('app.facilities.friday'),
    t('app.facilities.saturday'),
]);

const activeRules = computed(() => (props.facility.availability_rules ?? []).filter((r) => r.is_active).sort((a, b) => a.day_of_week - b.day_of_week));
</script>

<template>
    <Head :title="facility.name" />
    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-2xl font-bold tracking-tight">{{ isArabic ? (facility.name_ar ?? facility.name) : (facility.name_en ?? facility.name) }}</h2>
                    <Badge v-if="!facility.is_active" variant="secondary">{{ t('app.common.inactive') }}</Badge>
                    <Badge v-if="facility.contract_required" variant="outline">{{ t('app.facilities.contractRequired') }}</Badge>
                </div>
                <p class="text-sm text-muted-foreground">{{ localizedCategoryName() }} &middot; {{ facility.community?.name }}</p>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child><a :href="`/facilities/${facility.id}/edit`">{{ t('app.actions.edit') }}</a></Button>
                <Button variant="destructive" @click="deleteFacility">{{ t('app.actions.delete') }}</Button>
            </div>
        </div>

        <!-- Summary cards -->
        <div class="grid gap-4 md:grid-cols-4">
            <Card>
                <CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.facilities.status') }}</CardTitle></CardHeader>
                <CardContent><Badge :variant="facility.is_active ? 'default' : 'secondary'">{{ facility.is_active ? t('app.common.active') : t('app.common.inactive') }}</Badge></CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.facilities.capacity') }}</CardTitle></CardHeader>
                <CardContent><div class="text-2xl font-bold">{{ facility.capacity ?? t('app.common.notAvailable') }}</div></CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.facilities.pricing') }}</CardTitle></CardHeader>
                <CardContent><div class="font-medium">{{ pricingLabel }}</div></CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.facilities.bookings') }}</CardTitle></CardHeader>
                <CardContent><div class="text-2xl font-bold">{{ upcomingBookingsCount }}</div></CardContent>
            </Card>
        </div>

        <!-- Availability summary -->
        <Card v-if="activeRules.length > 0">
            <CardHeader><CardTitle>{{ t('app.facilities.availabilitySummary') }}</CardTitle></CardHeader>
            <CardContent>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="py-2 pr-4 font-medium">{{ t('app.facilities.day') }}</th>
                            <th class="py-2 pr-4 font-medium">{{ t('app.facilities.opens') }}</th>
                            <th class="py-2 pr-4 font-medium">{{ t('app.facilities.closes') }}</th>
                            <th class="py-2 font-medium">{{ t('app.facilities.slotDuration') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="rule in activeRules" :key="rule.day_of_week" class="border-b last:border-0">
                            <td class="py-2 pr-4">{{ dayNames[rule.day_of_week] }}</td>
                            <td class="py-2 pr-4">{{ rule.open_time }}</td>
                            <td class="py-2 pr-4">{{ rule.close_time }}</td>
                            <td class="py-2">{{ rule.slot_duration_minutes }} min</td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>

        <!-- Booking Constraints -->
        <Card>
            <CardHeader><CardTitle>{{ t('app.facilities.constraintsSummary') }}</CardTitle></CardHeader>
            <CardContent class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">{{ t('app.facilities.bookingHorizon') }}</span>
                    <span>{{ facility.booking_horizon_days }} days</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">{{ t('app.facilities.cancellationDeadline') }}</span>
                    <span>{{ facility.cancellation_hours_before }} hours</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">{{ t('app.facilities.minDuration') }}</span>
                    <span>{{ facility.min_booking_duration_minutes }} min</span>
                </div>
                <div v-if="facility.max_booking_duration_minutes" class="flex justify-between">
                    <span class="text-muted-foreground">{{ t('app.facilities.maxDuration') }}</span>
                    <span>{{ facility.max_booking_duration_minutes }} min</span>
                </div>
            </CardContent>
        </Card>

        <!-- Notes -->
        <Card v-if="facility.notes">
            <CardHeader><CardTitle>{{ t('app.facilities.notes') }}</CardTitle></CardHeader>
            <CardContent><p class="text-sm">{{ facility.notes }}</p></CardContent>
        </Card>
    </div>
</template>
