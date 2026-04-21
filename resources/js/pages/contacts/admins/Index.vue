<script setup lang="ts">
import { Head, setLayoutProps } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import { useI18n } from '@/composables/useI18n';
import type { Admin, Paginated } from '@/types';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.contacts.admins.pageTitle'), href: '/admins' },
        ],
    });
});

const props = defineProps<{
    admins: Paginated<Admin>;
}>();

function roleLabel(role: string | null | undefined): string {
    if (role === 'Admins' || role === 'admin') {
        return t('app.contacts.admins.roles.admin');
    }

    if (role === 'accountingManagers') {
        return t('app.contacts.admins.roles.accountingManager');
    }

    if (role === 'serviceManagers') {
        return t('app.contacts.admins.roles.serviceManager');
    }

    if (role === 'marketingManagers') {
        return t('app.contacts.admins.roles.marketingManager');
    }

    if (role === 'salesAndLeasingManagers') {
        return t('app.contacts.admins.roles.salesLeasingManager');
    }

    return role ?? '—';
}

const columns = computed<Column<Admin>[]>(() => [
    { key: 'name', label: t('app.contacts.shared.name'), render: (row: Admin) => `${row.first_name} ${row.last_name}` },
    { key: 'email', label: t('app.contacts.shared.email') },
    { key: 'phone_number', label: t('app.contacts.shared.phone') },
    { key: 'role', label: t('app.contacts.shared.role'), render: (row: Admin) => roleLabel(row.role) },
    { key: 'active', label: t('app.contacts.shared.status'), render: (row: Admin) => row.active ? t('app.common.active') : t('app.common.inactive') },
]);
</script>

<template>
    <Head :title="t('app.contacts.admins.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            :title="t('app.contacts.admins.heading')"
            :description="t('app.contacts.admins.description')"
            create-href="/admins/create"
            :create-label="t('app.contacts.admins.newAdmin')"
        />

        <DataTable
            :columns="columns"
            :rows="admins.data"
            :links="admins.links"
            :row-href="(row: any) => `/admins/${row.id}`"
            :empty-message="t('app.contacts.admins.emptyMessage')"
        />
    </div>
</template>
