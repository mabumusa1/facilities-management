<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import type { Paginated, Unit } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Units', href: '/units' },
        ],
    },
});

const props = defineProps<{
    units: Paginated<Unit>;
}>();

const columns: Column<Unit>[] = [
    { key: 'name', label: 'Name' },
    { key: 'community.name', label: 'Community' },
    { key: 'building.name', label: 'Building' },
    { key: 'category.name', label: 'Category' },
    { key: 'type.name', label: 'Type' },
    { key: 'status.name', label: 'Status' },
];
</script>

<template>
    <Head title="Units" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            title="Units"
            description="Manage individual units across your properties."
            create-href="/units/create"
            create-label="New Unit"
        />

        <DataTable
            :columns="columns"
            :rows="units.data"
            :links="units.links"
            :row-href="(row: any) => `/units/${row.id}`"
            empty-message="No units found."
        />
    </div>
</template>
