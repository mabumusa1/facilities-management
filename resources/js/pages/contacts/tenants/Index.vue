<script setup lang="ts">
import { Head, setLayoutProps } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import { useI18n } from '@/composables/useI18n';
import type { Paginated, Resident } from '@/types';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.contacts.tenants.pageTitle'), href: '/residents' },
        ],
    });
});

const props = defineProps<{
    residents: Paginated<Resident>;
}>();

const columns = computed<Column<Resident>[]>(() => [
    { key: 'name', label: t('app.contacts.shared.name'), render: (row: Resident) => `${row.first_name} ${row.last_name}` },
    { key: 'email', label: t('app.contacts.shared.email') },
    { key: 'phone_number', label: t('app.contacts.shared.phone') },
    { key: 'units_count', label: t('app.contacts.shared.units') },
    { key: 'leases_count', label: t('app.contacts.shared.leases') },
    { key: 'active', label: t('app.contacts.shared.status'), render: (row: Resident) => row.active ? t('app.common.active') : t('app.common.inactive') },
]);
</script>

<template>
    <Head :title="t('app.contacts.tenants.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            :title="t('app.contacts.tenants.heading')"
            :description="t('app.contacts.tenants.description')"
            create-href="/residents/create"
            :create-label="t('app.contacts.tenants.newTenant')"
        />

        <DataTable
            :columns="columns"
            :rows="residents.data"
            :links="residents.links"
            :row-href="(row: any) => `/residents/${row.id}`"
            :empty-message="t('app.contacts.tenants.emptyMessage')"
        />
    </div>
</template>
