<script setup lang="ts">
import { Head, setLayoutProps } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import { useI18n } from '@/composables/useI18n';
import type { Paginated, Professional } from '@/types';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.contacts.professionals.pageTitle'), href: '/professionals' },
        ],
    });
});

const props = defineProps<{
    professionals: Paginated<Professional>;
}>();

const columns = computed<Column<Professional>[]>(() => [
    { key: 'name', label: t('app.contacts.shared.name'), render: (row: Professional) => `${row.first_name} ${row.last_name}` },
    { key: 'email', label: t('app.contacts.shared.email') },
    { key: 'phone_number', label: t('app.contacts.shared.phone') },
    { key: 'active', label: t('app.contacts.shared.status'), render: (row: Professional) => row.active ? t('app.common.active') : t('app.common.inactive') },
]);
</script>

<template>
    <Head :title="t('app.contacts.professionals.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            :title="t('app.contacts.professionals.heading')"
            :description="t('app.contacts.professionals.description')"
            create-href="/professionals/create"
            :create-label="t('app.contacts.professionals.newProfessional')"
        />

        <DataTable
            :columns="columns"
            :rows="professionals.data"
            :links="professionals.links"
            :row-href="(row: any) => `/professionals/${row.id}`"
            :empty-message="t('app.contacts.professionals.emptyMessage')"
        />
    </div>
</template>
