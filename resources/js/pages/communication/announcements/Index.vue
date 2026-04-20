<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import type { Announcement, Paginated } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Announcements', href: '/announcements' },
        ],
    },
});

const props = defineProps<{
    announcements: Paginated<Announcement>;
}>();

const columns: Column<Announcement>[] = [
    { key: 'title', label: 'Title' },
    { key: 'community.name', label: 'Community' },
    { key: 'building.name', label: 'Building' },
    { key: 'published_at', label: 'Published' },
    { key: 'created_at', label: 'Created' },
];
</script>

<template>
    <Head title="Announcements" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            title="Announcements"
            description="Create and manage community announcements."
            create-href="/announcements/create"
            create-label="New Announcement"
        />

        <DataTable
            :columns="columns"
            :rows="announcements.data"
            :links="announcements.links"
            :row-href="(row: any) => `/announcements/${row.id}`"
            empty-message="No announcements found."
        />
    </div>
</template>
