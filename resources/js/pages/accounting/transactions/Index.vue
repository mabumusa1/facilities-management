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
import type { Paginated, Transaction } from '@/types';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.transactions.pageTitle'), href: '/transactions' },
        ],
    });
});

const props = defineProps<{
    transactions: Paginated<Transaction>;
    statuses: Array<{ id: number; name: string; name_en: string | null }>;
    transactionCategories: Array<{ id: number; name: string; name_en: string | null }>;
    filters: {
        search: string;
        status_id: string;
        category_id: string;
        is_paid: string;
        per_page: string;
    };
}>();

const filters = reactive({
    search: props.filters.search ?? '',
    status_id: props.filters.status_id ?? '',
    category_id: props.filters.category_id ?? '',
    is_paid: props.filters.is_paid ?? '',
    per_page: props.filters.per_page ?? '15',
});

const perPageOptions = ['10', '15', '25', '50'];

function applyFilters() {
    router.get('/transactions', { ...filters }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function resetFilters() {
    filters.search = '';
    filters.status_id = '';
    filters.category_id = '';
    filters.is_paid = '';
    filters.per_page = '15';
    applyFilters();
}

const columns = computed<Column<Transaction>[]>(() => [
    { key: 'id', label: t('app.transactions.table.id') },
    { key: 'lease.contract_number', label: t('app.transactions.table.lease') },
    { key: 'unit.name', label: t('app.transactions.table.unit') },
    {
        key: 'category',
        label: t('app.transactions.table.category'),
        render: (row: any) => row.category?.name_en ?? row.category?.name ?? '—',
    },
    {
        key: 'type',
        label: t('app.transactions.table.type'),
        render: (row: any) => row.type?.name_en ?? row.type?.name ?? '—',
    },
    { key: 'status.name', label: t('app.transactions.table.status') },
    { key: 'amount', label: t('app.transactions.table.amount') },
    { key: 'due_on', label: t('app.transactions.table.dueDate') },
    {
        key: 'is_paid',
        label: t('app.transactions.table.paid'),
        render: (row: Transaction) => (row.is_paid ? t('app.common.yes') : t('app.common.no')),
    },
]);
</script>

<template>
    <Head :title="t('app.transactions.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            :title="t('app.transactions.heading')"
            :description="t('app.transactions.description')"
            create-href="/transactions/create"
            :create-label="t('app.transactions.newTransaction')"
        />

        <form class="grid gap-4 rounded-lg border p-4 md:grid-cols-5" @submit.prevent="applyFilters">
            <div class="grid gap-2 md:col-span-2">
                <Label for="transaction-search">{{ t('app.transactions.search') }}</Label>
                <Input
                    id="transaction-search"
                    v-model="filters.search"
                    :placeholder="t('app.transactions.searchPlaceholder')"
                />
            </div>

            <div class="grid gap-2">
                <Label for="transaction-status">{{ t('app.transactions.status') }}</Label>
                <select id="transaction-status" v-model="filters.status_id" class="rounded-md border border-input bg-background px-3 py-2 text-sm">
                    <option value="">{{ t('app.transactions.allStatuses') }}</option>
                    <option v-for="status in props.statuses" :key="status.id" :value="String(status.id)">
                        {{ status.name_en ?? status.name }}
                    </option>
                </select>
            </div>

            <div class="grid gap-2">
                <Label for="transaction-category">{{ t('app.transactions.category') }}</Label>
                <select id="transaction-category" v-model="filters.category_id" class="rounded-md border border-input bg-background px-3 py-2 text-sm">
                    <option value="">{{ t('app.transactions.allCategories') }}</option>
                    <option v-for="category in props.transactionCategories" :key="category.id" :value="String(category.id)">
                        {{ category.name_en ?? category.name }}
                    </option>
                </select>
            </div>

            <div class="grid gap-2">
                <Label for="transaction-paid">{{ t('app.transactions.payment') }}</Label>
                <select id="transaction-paid" v-model="filters.is_paid" class="rounded-md border border-input bg-background px-3 py-2 text-sm">
                    <option value="">{{ t('app.transactions.allPayments') }}</option>
                    <option value="1">{{ t('app.transactions.paid') }}</option>
                    <option value="0">{{ t('app.transactions.unpaid') }}</option>
                </select>
            </div>

            <div class="grid gap-2">
                <Label for="transaction-per-page">{{ t('app.transactions.rowsPerPage') }}</Label>
                <select id="transaction-per-page" v-model="filters.per_page" class="rounded-md border border-input bg-background px-3 py-2 text-sm">
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
            :rows="transactions.data"
            :links="transactions.links"
            :row-href="(row: any) => `/transactions/${row.id}`"
            :empty-message="t('app.transactions.noTransactionsFound')"
        />

        <div class="text-muted-foreground flex items-center justify-between text-sm">
            <p>
                {{ t('app.transactions.showingSummary', { from: transactions.from ?? 0, to: transactions.to ?? 0, total: transactions.total }) }}
            </p>
            <p>{{ t('app.common.pageSummary', { current: transactions.current_page, last: transactions.last_page }) }}</p>
        </div>
    </div>
</template>
