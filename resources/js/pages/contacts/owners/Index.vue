<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import type { Owner, Paginated } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Owners', href: '/owners' },
        ],
    },
});

const props = defineProps<{
    owners: Paginated<Owner>;
}>();

const columns: Column<Owner>[] = [
    { key: 'name', label: 'Name', render: (row: Owner) => `${row.first_name} ${row.last_name}` },
    { key: 'email', label: 'Email' },
    { key: 'phone_number', label: 'Phone' },
    { key: 'units_count', label: 'Units' },
    { key: 'active', label: 'Status', render: (row: Owner) => row.active ? 'Active' : 'Inactive' },
];
</script>

<template>
    <Head title="Owners" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            title="Owners"
            description="Manage property owners."
            create-href="/owners/create"
            create-label="New Owner"
        />

        <DataTable
            :columns="columns"
            :rows="owners.data"
            :links="owners.links"
            :row-href="(row: any) => `/owners/${row.id}`"
            empty-message="No owners found."
        />
    </div>
</template>
