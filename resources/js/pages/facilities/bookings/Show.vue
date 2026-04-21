<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { FacilityBooking } from '@/types';

const { t } = useI18n();

const props = defineProps<{ facilityBooking: FacilityBooking }>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.facilityBookings.pageTitle'), href: '/facility-bookings' },
            { title: t('app.facilityBookings.details'), href: '#' },
        ],
    });
});

function deleteBooking() { if (confirm(t('app.facilityBookings.confirmDelete'))) { router.delete(`/facility-bookings/${props.facilityBooking.id}`); } }
</script>

<template>
    <Head :title="t('app.facilityBookings.bookingTitle', { id: facilityBooking.id })" />
    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">{{ t('app.facilityBookings.bookingTitle', { id: facilityBooking.id }) }}</h2>
                <p class="text-muted-foreground text-sm">{{ facilityBooking.facility?.name }}</p>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child><Link :href="`/facility-bookings/${facilityBooking.id}/edit`">{{ t('app.actions.edit') }}</Link></Button>
                <Button variant="destructive" @click="deleteBooking">{{ t('app.actions.delete') }}</Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>{{ t('app.facilityBookings.bookingDetails') }}</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.facilityBookings.facility') }}</span><span>{{ facilityBooking.facility?.name ?? t('app.common.notAvailable') }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.facilityBookings.bookedBy') }}</span><span>{{ facilityBooking.booker ? `${facilityBooking.booker.first_name ?? ''} ${facilityBooking.booker.last_name ?? ''}`.trim() || facilityBooking.booker.name : t('app.common.notAvailable') }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.facilityBookings.status') }}</span><Badge>{{ facilityBooking.status?.name ?? t('app.common.notAvailable') }}</Badge></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.facilityBookings.guests') }}</span><span>{{ (facilityBooking as any).number_of_guests ?? t('app.common.notAvailable') }}</span></div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>{{ t('app.facilityBookings.schedule') }}</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.facilityBookings.date') }}</span><span>{{ facilityBooking.booking_date }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.facilityBookings.start') }}</span><span>{{ facilityBooking.start_time }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.facilityBookings.end') }}</span><span>{{ facilityBooking.end_time }}</span></div>
                </CardContent>
            </Card>
        </div>

        <Card v-if="(facilityBooking as any).notes">
            <CardHeader><CardTitle>{{ t('app.facilityBookings.notes') }}</CardTitle></CardHeader>
            <CardContent><p>{{ (facilityBooking as any).notes }}</p></CardContent>
        </Card>
    </div>
</template>
