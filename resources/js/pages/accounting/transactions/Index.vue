<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import type { Paginated, Transaction } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Transactions', href: '/transactions' },
        ],
    },
});

const props = defineProps<{
    transactions: Paginated<Transaction>;
}>();

const columns: Column<Transaction>[] = [
    { key: 'id', label: 'ID' },
    { key: 'lease.contract_number', label: 'Lease' },
    { key: 'unit.name', label: 'Unit' },
    { key: 'category', label: 'Category', render: (row: any) => row.category?.name_en ?? row.category?.name ?? '—' },
    { key: 'type', label: 'Type', render: (row: any) => row.type?.name_en ?? row.type?.name ?? '—' },
    { key: 'status.name', label: 'Status' },
    { key: 'amount', label: 'Amount' },
    { key: 'due_on', label: 'Due Date' },
    { key: 'is_paid', label: 'Paid', render: (row: Transaction) => row.is_paid ? 'Yes' : 'No' },
];
</script>

<template>
    <Head title="Transactions" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            title="Transactions"
            description="View and manage financial transactions."
            create-href="/transactions/create"
            create-label="New Transaction"
        />

        <DataTable
            :columns="columns"
            :rows="transactions.data"
            :links="transactions.links"
            :row-href="(row: any) => `/transactions/${row.id}`"
            empty-message="No transactions found."
        />
    </div>
</template>
