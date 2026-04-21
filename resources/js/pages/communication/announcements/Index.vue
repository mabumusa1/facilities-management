<script setup lang="ts">
import { computed, watchEffect } from 'vue';
import { Head, setLayoutProps } from '@inertiajs/vue3';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import { useI18n } from '@/composables/useI18n';
import PageHeader from '@/components/PageHeader.vue';
import type { Announcement, Paginated } from '@/types';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.announcements'), href: '/announcements' },
        ],
    });
});

const props = defineProps<{
    announcements: Paginated<Announcement>;
}>();

const columns = computed<Column<Announcement>[]>(() => [
    { key: 'title', label: t('app.announcements.title') },
    { key: 'community.name', label: t('app.announcements.community') },
    { key: 'building.name', label: t('app.announcements.building') },
    { key: 'published_at', label: t('app.announcements.published') },
    { key: 'created_at', label: t('app.announcements.created') },
]);
</script>

<template>
    <Head :title="t('app.announcements.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            :title="t('app.announcements.heading')"
            :description="t('app.announcements.description')"
            create-href="/announcements/create"
            :create-label="t('app.announcements.newAnnouncement')"
        />

        <DataTable
            :columns="columns"
            :rows="announcements.data"
            :links="announcements.links"
            :row-href="(row: any) => `/announcements/${row.id}`"
            :empty-message="t('app.announcements.empty')"
        />
    </div>
</template>
