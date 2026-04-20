<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import type { Building, Paginated } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Buildings', href: '/buildings' },
        ],
    },
});

const props = defineProps<{
    buildings: Paginated<Building>;
}>();

const columns: Column<Building>[] = [
    { key: 'name', label: 'Name' },
    { key: 'community.name', label: 'Community' },
    { key: 'city.name', label: 'City' },
    { key: 'no_floors', label: 'Floors' },
    { key: 'units_count', label: 'Units' },
    { key: 'year_build', label: 'Year Built' },
];
</script>

<template>
    <Head title="Buildings" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            title="Buildings"
            description="Manage buildings within your communities."
            create-href="/buildings/create"
            create-label="New Building"
        />

        <DataTable
            :columns="columns"
            :rows="buildings.data"
            :links="buildings.links"
            :row-href="(row: any) => `/buildings/${row.id}`"
            empty-message="No buildings found."
        />
    </div>
</template>
