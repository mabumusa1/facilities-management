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
    { key: 'status.name', label: 'Status' },
    { key: 'floor_no', label: 'Floor' },
    { key: 'net_area', label: 'Area (sqm)' },
    { key: 'owner', label: 'Owner', render: (row: Unit) => row.owner ? `${row.owner.first_name} ${row.owner.last_name}` : '—' },
    { key: 'tenant', label: 'Tenant', render: (row: Unit) => row.tenant ? `${row.tenant.first_name} ${row.tenant.last_name}` : '—' },
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
