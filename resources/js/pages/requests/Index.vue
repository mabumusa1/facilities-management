<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import type { Paginated, ServiceRequest } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Requests', href: '/requests' },
        ],
    },
});

const props = defineProps<{
    requests: Paginated<ServiceRequest>;
}>();

const columns: Column<ServiceRequest>[] = [
    { key: 'id', label: 'ID' },
    { key: 'category.name', label: 'Category' },
    { key: 'subcategory.name', label: 'Subcategory' },
    { key: 'status.name', label: 'Status' },
    { key: 'community.name', label: 'Community' },
    { key: 'created_at', label: 'Created' },
];
</script>

<template>
    <Head title="Requests" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            title="Service Requests"
            description="Track and manage maintenance and service requests."
            create-href="/requests/create"
            create-label="New Request"
        />

        <DataTable
            :columns="columns"
            :rows="requests.data"
            :links="requests.links"
            :row-href="(row: any) => `/requests/${row.id}`"
            empty-message="No requests found."
        />
    </div>
</template>
