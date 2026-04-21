<script setup lang="ts">
import { computed, watchEffect } from 'vue';
import { Head, setLayoutProps } from '@inertiajs/vue3';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import { useI18n } from '@/composables/useI18n';
import PageHeader from '@/components/PageHeader.vue';
import type { FacilityBooking, Paginated } from '@/types';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.facilityBookings.pageTitle'), href: '/facility-bookings' },
        ],
    });
});

const props = defineProps<{
    bookings: Paginated<FacilityBooking>;
}>();

const columns = computed<Column<FacilityBooking>[]>(() => [
    { key: 'id', label: t('app.facilityBookings.id') },
    { key: 'facility.name', label: t('app.facilityBookings.facility') },
    { key: 'booker', label: t('app.facilityBookings.bookedBy'), render: (row: any) => row.booker ? `${row.booker.first_name ?? ''} ${row.booker.last_name ?? ''}`.trim() || row.booker.name || t('app.common.notAvailable') : t('app.common.notAvailable') },
    { key: 'status.name', label: t('app.facilityBookings.status') },
    { key: 'booking_date', label: t('app.facilityBookings.date') },
    { key: 'start_time', label: t('app.facilityBookings.start') },
    { key: 'end_time', label: t('app.facilityBookings.end') },
    { key: 'number_of_guests', label: t('app.facilityBookings.guests') },
]);
</script>

<template>
    <Head :title="t('app.facilityBookings.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            :title="t('app.facilityBookings.heading')"
            :description="t('app.facilityBookings.description')"
            create-href="/facility-bookings/create"
            :create-label="t('app.facilityBookings.newBooking')"
        />

        <DataTable
            :columns="columns"
            :rows="bookings.data"
            :links="bookings.links"
            :row-href="(row: any) => `/facility-bookings/${row.id}`"
            :empty-message="t('app.facilityBookings.noBookingsFound')"
        />
    </div>
</template>
