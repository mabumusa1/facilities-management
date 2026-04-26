<script setup lang="ts">
import { computed, watchEffect } from 'vue';
import { Head, setLayoutProps } from '@inertiajs/vue3';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import { useI18n } from '@/composables/useI18n';
import PageHeader from '@/components/PageHeader.vue';
import type { Facility, Paginated } from '@/types';

const { isArabic, t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.facilities'), href: '/facilities' },
        ],
    });
});

const props = defineProps<{
    facilities: Paginated<Facility>;
}>();

function localizedCategoryName(facility: Facility): string {
    if (!facility.category) {
        return t('app.common.notAvailable');
    }

    if (isArabic.value) {
        return facility.category.name_ar ?? facility.category.name ?? facility.category.name_en ?? t('app.common.notAvailable');
    }

    return facility.category.name_en ?? facility.category.name ?? facility.category.name_ar ?? t('app.common.notAvailable');
}

function localizedPricingMode(facility: Facility): string {
    if (facility.pricing_mode === 'per_session') {
        return t('app.facilities.pricingPerSession');
    }

    if (facility.pricing_mode === 'per_hour') {
        return t('app.facilities.pricingPerHour');
    }

    return t('app.facilities.pricingFree');
}

const columns = computed<Column<Facility>[]>(() => [
    { key: 'name', label: t('app.facilities.name') },
    { key: 'category.name', label: t('app.facilities.category'), render: (row: Facility) => localizedCategoryName(row) },
    { key: 'community.name', label: t('app.facilities.community') },
    { key: 'capacity', label: t('app.facilities.capacity') },
    { key: 'pricing_mode', label: t('app.facilities.pricing'), render: (row: Facility) => localizedPricingMode(row) },
    { key: 'is_active', label: t('app.facilities.status'), render: (row: Facility) => row.is_active ? t('app.common.active') : t('app.common.inactive') },
]);
</script>

<template>
    <Head :title="t('app.facilities.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            :title="t('app.facilities.heading')"
            :description="t('app.facilities.description')"
            create-href="/facilities/create"
            :create-label="t('app.facilities.newFacility')"
        />

        <DataTable
            :columns="columns"
            :rows="facilities.data"
            :links="facilities.links"
            :row-href="(row: any) => `/facilities/${row.id}`"
            :empty-message="t('app.facilities.noFacilitiesFound')"
        />
    </div>
</template>
