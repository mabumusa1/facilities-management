<script setup lang="ts">
import { Head, setLayoutProps } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import { useI18n } from '@/composables/useI18n';
import type { Building, Paginated } from '@/types';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.properties.buildings.pageTitle'), href: '/buildings' },
        ],
    });
});

const props = defineProps<{
    buildings: Paginated<Building>;
}>();

const columns = computed<Column<Building>[]>(() => [
    { key: 'name', label: t('app.properties.buildings.table.name') },
    { key: 'community.name', label: t('app.properties.buildings.table.community') },
    { key: 'city.name', label: t('app.properties.buildings.table.city') },
    { key: 'no_floors', label: t('app.properties.buildings.table.floors') },
    { key: 'units_count', label: t('app.properties.buildings.table.units') },
    { key: 'year_build', label: t('app.properties.buildings.table.yearBuilt') },
]);
</script>

<template>
    <Head :title="t('app.properties.buildings.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            :title="t('app.properties.buildings.heading')"
            :description="t('app.properties.buildings.description')"
            create-href="/buildings/create"
            :create-label="t('app.properties.buildings.newBuilding')"
        />

        <DataTable
            :columns="columns"
            :rows="buildings.data"
            :links="buildings.links"
            :row-href="(row: any) => `/buildings/${row.id}`"
            :empty-message="t('app.properties.buildings.noBuildingsFound')"
        />
    </div>
</template>
