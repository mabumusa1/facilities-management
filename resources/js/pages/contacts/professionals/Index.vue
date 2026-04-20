<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import type { Paginated, Professional } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Professionals', href: '/professionals' },
        ],
    },
});

const props = defineProps<{
    professionals: Paginated<Professional>;
}>();

const columns: Column<Professional>[] = [
    { key: 'name', label: 'Name', render: (row: Professional) => `${row.first_name} ${row.last_name}` },
    { key: 'email', label: 'Email' },
    { key: 'phone_number', label: 'Phone' },
    { key: 'active', label: 'Status', render: (row: Professional) => row.active ? 'Active' : 'Inactive' },
];
</script>

<template>
    <Head title="Professionals" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            title="Professionals"
            description="Manage service professionals and technicians."
            create-href="/professionals/create"
            create-label="New Professional"
        />

        <DataTable
            :columns="columns"
            :rows="professionals.data"
            :links="professionals.links"
            :row-href="(row: any) => `/professionals/${row.id}`"
            empty-message="No professionals found."
        />
    </div>
</template>
