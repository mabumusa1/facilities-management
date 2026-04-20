<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import type { Facility, Paginated } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Facilities', href: '/facilities' },
        ],
    },
});

const props = defineProps<{
    facilities: Paginated<Facility>;
}>();

const columns: Column<Facility>[] = [
    { key: 'name', label: 'Name' },
    { key: 'category.name', label: 'Category' },
    { key: 'community.name', label: 'Community' },
    { key: 'capacity', label: 'Capacity' },
    { key: 'status', label: 'Status', render: (row: Facility) => row.status ? 'Active' : 'Inactive' },
];
</script>

<template>
    <Head title="Facilities" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            title="Facilities"
            description="Manage shared amenities and facilities."
            create-href="/facilities/create"
            create-label="New Facility"
        />

        <DataTable
            :columns="columns"
            :rows="facilities.data"
            :links="facilities.links"
            :row-href="(row: any) => `/facilities/${row.id}`"
            empty-message="No facilities found."
        />
    </div>
</template>
