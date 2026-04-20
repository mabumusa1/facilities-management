<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import type { Lease, Paginated } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Leases', href: '/leases' },
        ],
    },
});

const props = defineProps<{
    leases: Paginated<Lease>;
}>();

const columns: Column<Lease>[] = [
    { key: 'contract_number', label: 'Contract #' },
    { key: 'tenant.name', label: 'Tenant' },
    { key: 'status.name', label: 'Status' },
    { key: 'start_date', label: 'Start Date' },
    { key: 'end_date', label: 'End Date' },
    { key: 'rental_total_amount', label: 'Total Amount' },
];
</script>

<template>
    <Head title="Leases" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            title="Leases"
            description="Manage rental agreements and contracts."
            create-href="/leases/create"
            create-label="New Lease"
        />

        <DataTable
            :columns="columns"
            :rows="leases.data"
            :links="leases.links"
            :row-href="(row: any) => `/leases/${row.id}`"
            empty-message="No leases found."
        />
    </div>
</template>
