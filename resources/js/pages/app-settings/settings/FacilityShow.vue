<script setup lang="ts">
import { Head, Link, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

const { t } = useI18n();

type FacilityBooking = {
    id: number;
    booking_date: string;
    start_time: string;
    end_time: string;
    number_of_guests: number | null;
    notes: string | null;
};

type Facility = {
    id: number;
    name: string;
    name_en: string | null;
    name_ar: string | null;
    description: string | null;
    capacity: number | null;
    open_time: string | null;
    close_time: string | null;
    booking_fee: string;
    is_active: boolean;
    requires_approval: boolean;
    category?: { name?: string | null; name_en?: string | null } | null;
    community?: { name?: string | null } | null;
    bookings?: FacilityBooking[];
};

const props = defineProps<{ facility: Facility }>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.settings'), href: '/settings/invoice' },
            { title: t('app.navigation.facilities'), href: '/settings/facilities' },
        ],
    });
});
</script>

<template>
    <Head :title="t('app.appSettings.facilities.facilityTitle', { name: props.facility.name })" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading
                variant="small"
                :title="props.facility.name_en ?? props.facility.name"
                :description="t('app.appSettings.facilities.detailsDescription')"
            />
            <div class="flex gap-2">
                <Button variant="outline" as-child>
                    <Link href="/settings/facilities">{{ t('app.actions.back') }}</Link>
                </Button>
                <Button as-child>
                    <Link :href="`/settings/addNewFacility/${props.facility.id}`">{{ t('app.actions.edit') }}</Link>
                </Button>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.appSettings.facilities.details') }}</CardTitle>
            </CardHeader>
            <CardContent class="grid gap-3 text-sm md:grid-cols-2">
                <p><span class="font-medium">{{ t('app.appSettings.facilities.category') }}:</span> {{ props.facility.category?.name_en ?? props.facility.category?.name ?? t('app.common.notAvailable') }}</p>
                <p><span class="font-medium">{{ t('app.appSettings.facilities.community') }}:</span> {{ props.facility.community?.name ?? t('app.common.notAvailable') }}</p>
                <p><span class="font-medium">{{ t('app.appSettings.facilities.capacity') }}:</span> {{ props.facility.capacity ?? t('app.common.notAvailable') }}</p>
                <p><span class="font-medium">{{ t('app.appSettings.facilities.open') }}:</span> {{ props.facility.open_time ?? t('app.common.notAvailable') }}</p>
                <p><span class="font-medium">{{ t('app.appSettings.facilities.close') }}:</span> {{ props.facility.close_time ?? t('app.common.notAvailable') }}</p>
                <p><span class="font-medium">{{ t('app.appSettings.facilities.bookingFee') }}:</span> {{ props.facility.booking_fee }}</p>
                <p>
                    <span class="font-medium">{{ t('app.appSettings.facilities.status') }}:</span>
                    <Badge :variant="props.facility.is_active ? 'default' : 'secondary'">
                        {{ props.facility.is_active ? t('app.common.active') : t('app.common.inactive') }}
                    </Badge>
                </p>
                <p>
                    <span class="font-medium">{{ t('app.appSettings.facilities.approval') }}:</span>
                    <Badge :variant="props.facility.requires_approval ? 'default' : 'secondary'">
                        {{ props.facility.requires_approval ? t('app.appSettings.facilities.required') : t('app.appSettings.facilities.notRequired') }}
                    </Badge>
                </p>
                <p class="md:col-span-2"><span class="font-medium">{{ t('app.appSettings.facilities.descriptionLabel') }}:</span> {{ props.facility.description ?? t('app.common.notAvailable') }}</p>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.appSettings.facilities.recentBookings') }}</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3">
                <div v-for="booking in props.facility.bookings ?? []" :key="booking.id" class="rounded-md border p-3 text-sm">
                    <p class="font-medium">{{ t('app.appSettings.facilities.bookingTitle', { id: booking.id }) }}</p>
                    <p>{{ booking.booking_date }} - {{ booking.start_time }} {{ t('app.appSettings.facilities.to') }} {{ booking.end_time }}</p>
                    <p>{{ t('app.appSettings.facilities.guests') }}: {{ booking.number_of_guests ?? t('app.common.notAvailable') }}</p>
                    <p>{{ t('app.appSettings.facilities.notes') }}: {{ booking.notes ?? t('app.common.notAvailable') }}</p>
                </div>
                <p v-if="(props.facility.bookings ?? []).length === 0" class="text-muted-foreground text-sm">{{ t('app.appSettings.facilities.noBookings') }}</p>
            </CardContent>
        </Card>
    </div>
</template>
