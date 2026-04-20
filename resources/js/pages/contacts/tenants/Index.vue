<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import type { Paginated, Resident } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Tenants', href: '/residents' },
        ],
    },
});

const props = defineProps<{
    residents: Paginated<Resident>;
}>();

const columns: Column<Resident>[] = [
    { key: 'name', label: 'Name', render: (row: Resident) => `${row.first_name} ${row.last_name}` },
    { key: 'email', label: 'Email' },
    { key: 'phone_number', label: 'Phone' },
    { key: 'units_count', label: 'Units' },
    { key: 'leases_count', label: 'Leases' },
    { key: 'active', label: 'Status', render: (row: Resident) => row.active ? 'Active' : 'Inactive' },
];
</script>

<template>
    <Head title="Tenants" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            title="Tenants"
            description="Manage tenants across your properties."
            create-href="/residents/create"
            create-label="New Tenant"
        />

        <DataTable
            :columns="columns"
            :rows="residents.data"
            :links="residents.links"
            :row-href="(row: any) => `/residents/${row.id}`"
            empty-message="No tenants found."
        />
    </div>
</template>
