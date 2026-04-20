<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import type { Admin, Paginated } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Admins', href: '/admins' },
        ],
    },
});

const props = defineProps<{
    admins: Paginated<Admin>;
}>();

const columns: Column<Admin>[] = [
    { key: 'name', label: 'Name', render: (row: Admin) => `${row.first_name} ${row.last_name}` },
    { key: 'email', label: 'Email' },
    { key: 'phone_number', label: 'Phone' },
    { key: 'role', label: 'Role' },
    { key: 'active', label: 'Status', render: (row: Admin) => row.active ? 'Active' : 'Inactive' },
];
</script>

<template>
    <Head title="Admins" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            title="Admins"
            description="Manage administrators and managers."
            create-href="/admins/create"
            create-label="New Admin"
        />

        <DataTable
            :columns="columns"
            :rows="admins.data"
            :links="admins.links"
            :row-href="(row: any) => `/admins/${row.id}`"
            empty-message="No admins found."
        />
    </div>
</template>
