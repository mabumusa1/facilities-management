<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { reactive, watchEffect } from 'vue';
import { index as renewalsIndex } from '@/actions/App/Http/Controllers/Leasing/LeaseRenewalController';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useI18n } from '@/composables/useI18n';
import type { Paginated } from '@/types';

const { t } = useI18n();

type RenewalOfferRow = {
    id: number;
    lease: {
        id: number;
        contract_number: string;
        tenant?: { first_name: string; last_name: string } | null;
        units?: Array<{ name: string }>;
    } | null;
    status: { id: number; name: string; name_en: string | null } | null;
    new_rent_amount: string;
    payment_frequency: string | null;
    valid_until: string;
    created_at: string;
};

const props = defineProps<{
    offers: Paginated<RenewalOfferRow>;
    statuses: Array<{ id: number; name: string; name_en: string | null }>;
    filters: {
        search: string;
        status_id: string;
    };
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.leases.pageTitle'), href: '/leases' },
            { title: t('app.leases.renewal.indexTitle'), href: '#' },
        ],
    });
});

const filters = reactive({
    search: props.filters.search ?? '',
    status_id: props.filters.status_id ?? '',
});

function applyFilters() {
    router.get(renewalsIndex.url(), { ...filters }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function resetFilters() {
    filters.search = '';
    filters.status_id = '';
    applyFilters();
}

const columns: Column<RenewalOfferRow>[] = [
    {
        key: 'lease',
        label: t('app.leases.show.pageTitle', { contract: '' }).trim() || 'Lease',
        render: (row: RenewalOfferRow) => row.lease?.contract_number ?? '—',
    },
    {
        key: 'tenant',
        label: t('app.leases.show.tenant') || 'Tenant',
        render: (row: RenewalOfferRow) =>
            row.lease?.tenant
                ? `${row.lease.tenant.first_name} ${row.lease.tenant.last_name}`.trim()
                : '—',
    },
    {
        key: 'unit',
        label: t('app.leases.show.units') || 'Unit',
        render: (row: RenewalOfferRow) => row.lease?.units?.[0]?.name ?? '—',
    },
    {
        key: 'new_rent_amount',
        label: t('app.leases.renewal.renewalAmount', { amount: '', freq: '' }).trim() || 'Renewal Amount',
        render: (row: RenewalOfferRow) =>
            t('app.leases.renewal.renewalAmount', {
                amount: row.new_rent_amount,
                freq: row.payment_frequency ?? '',
            }),
    },
    {
        key: 'status',
        label: t('app.leases.show.status'),
        render: (row: RenewalOfferRow) => row.status?.name_en ?? row.status?.name ?? '—',
    },
    {
        key: 'valid_until',
        label: t('app.leases.renewal.validUntil'),
    },
];
</script>

<template>
    <Head :title="t('app.leases.renewal.indexTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            :title="t('app.leases.renewal.indexTitle')"
            :description="t('app.leases.renewal.indexDescription')"
        />

        <form class="grid gap-4 rounded-lg border p-4 md:grid-cols-3" @submit.prevent="applyFilters">
            <div class="grid gap-2 md:col-span-2">
                <Label for="renewal-search">{{ t('app.common.search') }}</Label>
                <Input
                    id="renewal-search"
                    v-model="filters.search"
                    :placeholder="t('app.leases.renewal.searchPlaceholder')"
                />
            </div>

            <div class="grid gap-2">
                <Label for="renewal-status">{{ t('app.leases.show.status') }}</Label>
                <select
                    id="renewal-status"
                    v-model="filters.status_id"
                    class="rounded-md border border-input bg-background px-3 py-2 text-sm"
                >
                    <option value="">{{ t('app.common.all') }}</option>
                    <option v-for="status in statuses" :key="status.id" :value="String(status.id)">
                        {{ status.name_en ?? status.name }}
                    </option>
                </select>
            </div>

            <div class="flex items-end gap-2 md:col-span-3">
                <Button type="submit">{{ t('app.actions.apply') }}</Button>
                <Button type="button" variant="outline" @click="resetFilters">{{ t('app.actions.reset') }}</Button>
            </div>
        </form>

        <DataTable
            :columns="columns"
            :rows="offers.data"
            :links="offers.links"
            :row-href="(row: any) => `/leases/${row.lease?.id}`"
            :empty-message="t('app.leases.renewal.noOffersFound')"
        />

        <p class="text-muted-foreground text-sm">
            {{ t('app.leases.renewal.summaryCount', { count: offers.total }) }}
        </p>
    </div>
</template>
