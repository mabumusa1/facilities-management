<script setup lang="ts">
import { Head, setLayoutProps } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import { useI18n } from '@/composables/useI18n';
import type { Owner, Paginated } from '@/types';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.contacts.owners.pageTitle'), href: '/owners' },
        ],
    });
});

const props = defineProps<{
    owners: Paginated<Owner>;
}>();

const columns = computed<Column<Owner>[]>(() => [
    { key: 'name', label: t('app.contacts.shared.name'), render: (row: Owner) => `${row.first_name} ${row.last_name}` },
    { key: 'email', label: t('app.contacts.shared.email') },
    { key: 'phone_number', label: t('app.contacts.shared.phone') },
    { key: 'units_count', label: t('app.contacts.shared.units') },
    { key: 'active', label: t('app.contacts.shared.status'), render: (row: Owner) => row.active ? t('app.common.active') : t('app.common.inactive') },
]);
</script>

<template>
    <Head :title="t('app.contacts.owners.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            :title="t('app.contacts.owners.heading')"
            :description="t('app.contacts.owners.description')"
            create-href="/owners/create"
            :create-label="t('app.contacts.owners.newOwner')"
        />

        <DataTable
            :columns="columns"
            :rows="owners.data"
            :links="owners.links"
            :row-href="(row: any) => `/owners/${row.id}`"
            :empty-message="t('app.contacts.owners.emptyMessage')"
        />
    </div>
</template>
