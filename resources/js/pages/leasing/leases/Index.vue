<script setup lang="ts">
import { Head, router, setLayoutProps } from '@inertiajs/vue3';
import { computed, reactive, watchEffect } from 'vue';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import { useI18n } from '@/composables/useI18n';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Lease, Paginated } from '@/types';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.leases.pageTitle'), href: '/leases' },
        ],
    });
});

const props = defineProps<{
    leases: Paginated<Lease>;
    statuses: Array<{ id: number; name: string; name_en: string | null }>;
    tenants: Array<{ id: number; first_name: string; last_name: string }>;
    filters: {
        search: string;
        status_id: string;
        tenant_id: string;
        per_page: string;
    };
}>();

const filters = reactive({
    search: props.filters.search ?? '',
    status_id: props.filters.status_id ?? '',
    tenant_id: props.filters.tenant_id ?? '',
    per_page: props.filters.per_page ?? '15',
});

const perPageOptions = ['10', '15', '25', '50'];

function applyFilters() {
    router.get('/leases', { ...filters }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function resetFilters() {
    filters.search = '';
    filters.status_id = '';
    filters.tenant_id = '';
    filters.per_page = '15';
    applyFilters();
}

const columns = computed<Column<Lease>[]>(() => [
    { key: 'contract_number', label: t('app.leases.table.contract') },
    {
        key: 'tenant',
        label: t('app.leases.table.tenant'),
        render: (row: Lease) => (row.tenant ? `${row.tenant.first_name} ${row.tenant.last_name}` : '—'),
    },
    { key: 'status.name', label: t('app.leases.table.status') },
    { key: 'tenant_type', label: t('app.leases.table.type') },
    { key: 'start_date', label: t('app.leases.table.startDate') },
    { key: 'end_date', label: t('app.leases.table.endDate') },
    { key: 'rental_total_amount', label: t('app.leases.table.totalAmount') },
]);
</script>

<template>
    <Head :title="t('app.leases.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            :title="t('app.leases.heading')"
            :description="t('app.leases.description')"
            create-href="/leases/create"
            :create-label="t('app.leases.newLease')"
        />

        <form class="grid gap-4 rounded-lg border p-4 md:grid-cols-5" @submit.prevent="applyFilters">
            <div class="grid gap-2 md:col-span-2">
                <Label for="lease-search">{{ t('app.leases.search') }}</Label>
                <Input id="lease-search" v-model="filters.search" :placeholder="t('app.leases.searchPlaceholder')" />
            </div>

            <div class="grid gap-2">
                <Label for="lease-status">{{ t('app.leases.status') }}</Label>
                <select id="lease-status" v-model="filters.status_id" class="rounded-md border border-input bg-background px-3 py-2 text-sm">
                    <option value="">{{ t('app.leases.allStatuses') }}</option>
                    <option v-for="status in props.statuses" :key="status.id" :value="String(status.id)">
                        {{ status.name_en ?? status.name }}
                    </option>
                </select>
            </div>

            <div class="grid gap-2">
                <Label for="lease-tenant">{{ t('app.leases.tenant') }}</Label>
                <select id="lease-tenant" v-model="filters.tenant_id" class="rounded-md border border-input bg-background px-3 py-2 text-sm">
                    <option value="">{{ t('app.leases.allTenants') }}</option>
                    <option v-for="tenant in props.tenants" :key="tenant.id" :value="String(tenant.id)">
                        {{ tenant.first_name }} {{ tenant.last_name }}
                    </option>
                </select>
            </div>

            <div class="grid gap-2">
                <Label for="lease-per-page">{{ t('app.leases.rowsPerPage') }}</Label>
                <select id="lease-per-page" v-model="filters.per_page" class="rounded-md border border-input bg-background px-3 py-2 text-sm">
                    <option v-for="option in perPageOptions" :key="option" :value="option">{{ option }}</option>
                </select>
            </div>

            <div class="flex items-end gap-2 md:col-span-5">
                <Button type="submit">{{ t('app.actions.apply') }}</Button>
                <Button type="button" variant="outline" @click="resetFilters">{{ t('app.actions.reset') }}</Button>
            </div>
        </form>

        <DataTable
            :columns="columns"
            :rows="leases.data"
            :links="leases.links"
            :row-href="(row: any) => `/leases/${row.id}`"
            :empty-message="t('app.leases.noLeasesFound')"
        />

        <div class="text-muted-foreground flex items-center justify-between text-sm">
            <p>{{ t('app.leases.showingSummary', { from: leases.from ?? 0, to: leases.to ?? 0, total: leases.total }) }}</p>
            <p>{{ t('app.common.pageSummary', { current: leases.current_page, last: leases.last_page }) }}</p>
        </div>
    </div>
</template>
