<script setup lang="ts">
import { Head, setLayoutProps } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import { useI18n } from '@/composables/useI18n';
import type { Community, Paginated } from '@/types';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.properties.communities.pageTitle'), href: '/communities' },
        ],
    });
});

const props = defineProps<{
    communities: Paginated<Community>;
}>();

const columns = computed<Column<Community>[]>(() => [
    { key: 'name', label: t('app.properties.communities.table.name') },
    { key: 'country.name', label: t('app.properties.communities.table.country') },
    { key: 'city.name', label: t('app.properties.communities.table.city') },
    { key: 'currency.code', label: t('app.properties.communities.table.currency') },
    { key: 'buildings_count', label: t('app.properties.communities.table.buildings') },
    { key: 'units_count', label: t('app.properties.communities.table.units') },
    { key: 'requests_count', label: t('app.properties.communities.table.requests') },
    { key: 'sales_commission_rate', label: t('app.properties.communities.table.salesCommission') },
    { key: 'rental_commission_rate', label: t('app.properties.communities.table.rentalCommission') },
]);
</script>

<template>
    <Head :title="t('app.properties.communities.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            :title="t('app.properties.communities.heading')"
            :description="t('app.properties.communities.description')"
            create-href="/communities/create"
            :create-label="t('app.properties.communities.newCommunity')"
        />

        <DataTable
            :columns="columns"
            :rows="communities.data"
            :links="communities.links"
            :row-href="(row: any) => `/communities/${row.id}`"
            :empty-message="t('app.properties.communities.noCommunitiesFound')"
        />
    </div>
</template>
