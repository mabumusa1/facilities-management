<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import type { Community, Paginated } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Communities', href: '/communities' },
        ],
    },
});

const props = defineProps<{
    communities: Paginated<Community>;
}>();

const columns: Column<Community>[] = [
    { key: 'name', label: 'Name' },
    { key: 'country.name', label: 'Country' },
    { key: 'city.name', label: 'City' },
    { key: 'district.name', label: 'District' },
    { key: 'buildings_count', label: 'Buildings' },
    { key: 'units_count', label: 'Units' },
];
</script>

<template>
    <Head title="Communities" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            title="Communities"
            description="Manage your property communities and developments."
            create-href="/communities/create"
            create-label="New Community"
        />

        <DataTable
            :columns="columns"
            :rows="communities.data"
            :links="communities.links"
            :row-href="(row: any) => `/communities/${row.id}`"
            empty-message="No communities found. Create your first community to get started."
        />
    </div>
</template>
