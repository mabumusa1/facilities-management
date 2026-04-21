<script setup lang="ts">
import { Head, setLayoutProps } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import { useI18n } from '@/composables/useI18n';
import type { Paginated, Unit } from '@/types';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.properties.units.pageTitle'), href: '/units' },
        ],
    });
});

const props = defineProps<{
    units: Paginated<Unit>;
}>();

const columns = computed<Column<Unit>[]>(() => [
    { key: 'name', label: t('app.properties.units.table.name') },
    { key: 'community.name', label: t('app.properties.units.table.community') },
    { key: 'building.name', label: t('app.properties.units.table.building') },
    { key: 'category.name', label: t('app.properties.units.table.category') },
    { key: 'status.name', label: t('app.properties.units.table.status') },
    { key: 'floor_no', label: t('app.properties.units.table.floor') },
    { key: 'net_area', label: t('app.properties.units.table.area') },
    { key: 'owner', label: t('app.properties.units.table.owner'), render: (row: Unit) => row.owner ? `${row.owner.first_name} ${row.owner.last_name}` : '—' },
    { key: 'tenant', label: t('app.properties.units.table.tenant'), render: (row: Unit) => row.tenant ? `${row.tenant.first_name} ${row.tenant.last_name}` : '—' },
]);
</script>

<template>
    <Head :title="t('app.properties.units.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            :title="t('app.properties.units.heading')"
            :description="t('app.properties.units.description')"
            create-href="/units/create"
            :create-label="t('app.properties.units.newUnit')"
        />

        <DataTable
            :columns="columns"
            :rows="units.data"
            :links="units.links"
            :row-href="(row: any) => `/units/${row.id}`"
            :empty-message="t('app.properties.units.noUnitsFound')"
        />
    </div>
</template>
