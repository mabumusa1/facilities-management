<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import type { FacilityBooking, Paginated } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Facility Bookings', href: '/facility-bookings' },
        ],
    },
});

const props = defineProps<{
    bookings: Paginated<FacilityBooking>;
}>();

const columns: Column<FacilityBooking>[] = [
    { key: 'id', label: 'ID' },
    { key: 'facility.name', label: 'Facility' },
    { key: 'resident.name', label: 'Resident' },
    { key: 'status.name', label: 'Status' },
    { key: 'booking_date', label: 'Date' },
    { key: 'start_time', label: 'Start' },
    { key: 'end_time', label: 'End' },
];
</script>

<template>
    <Head title="Facility Bookings" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            title="Facility Bookings"
            description="View and manage facility reservations."
        />

        <DataTable
            :columns="columns"
            :rows="bookings.data"
            :links="bookings.links"
            :row-href="(row: any) => `/facility-bookings/${row.id}`"
            empty-message="No bookings found."
        />
    </div>
</template>
