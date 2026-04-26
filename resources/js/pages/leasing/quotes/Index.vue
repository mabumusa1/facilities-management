<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { computed, reactive, watchEffect } from 'vue';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import { useI18n } from '@/composables/useI18n';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Paginated } from '@/types';
import { index as quotesIndex, create as quotesCreate, show as quotesShow } from '@/actions/App/Http/Controllers/Leasing/QuoteController';

const { t } = useI18n();

type QuoteRow = {
    id: number;
    quote_number: string | null;
    contact: { id: number; first_name: string; last_name: string } | null;
    unit: { id: number; name: string } | null;
    status: { id: number; name: string; name_en: string | null } | null;
    valid_until: string;
    rent_amount: string;
    created_at: string;
};

const props = defineProps<{
    quotes: Paginated<QuoteRow>;
    statuses: Array<{ id: number; name: string; name_en: string | null }>;
    filters: {
        search: string;
        status_id: string;
        per_page: string;
    };
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.quotes.pageTitle'), href: quotesIndex.url() },
        ],
    });
});

const filters = reactive({
    search: props.filters.search ?? '',
    status_id: props.filters.status_id ?? '',
    per_page: props.filters.per_page ?? '15',
});

const perPageOptions = ['10', '15', '25', '50'];

function applyFilters() {
    router.get(quotesIndex.url(), { ...filters }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function resetFilters() {
    filters.search = '';
    filters.status_id = '';
    filters.per_page = '15';
    applyFilters();
}

const columns = computed<Column<QuoteRow>[]>(() => [
    {
        key: 'quote_number',
        label: t('app.quotes.table.quoteNumber'),
        render: (row: QuoteRow) => row.quote_number ?? '—',
    },
    {
        key: 'contact',
        label: t('app.quotes.table.contact'),
        render: (row: QuoteRow) =>
            row.contact ? `${row.contact.first_name} ${row.contact.last_name}` : '—',
    },
    {
        key: 'unit',
        label: t('app.quotes.table.unit'),
        render: (row: QuoteRow) => row.unit?.name ?? '—',
    },
    {
        key: 'status',
        label: t('app.quotes.table.status'),
        render: (row: QuoteRow) => row.status?.name_en ?? row.status?.name ?? '—',
    },
    { key: 'rent_amount', label: t('app.quotes.table.rentAmount') },
    { key: 'valid_until', label: t('app.quotes.table.validUntil') },
]);
</script>

<template>
    <Head :title="t('app.quotes.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            :title="t('app.quotes.heading')"
            :description="t('app.quotes.description')"
            :create-href="quotesCreate.url()"
            :create-label="t('app.quotes.newQuote')"
        />

        <form class="grid gap-4 rounded-lg border p-4 md:grid-cols-4" @submit.prevent="applyFilters">
            <div class="grid gap-2 md:col-span-2">
                <Label for="quote-search">{{ t('app.quotes.search') }}</Label>
                <Input
                    id="quote-search"
                    v-model="filters.search"
                    :placeholder="t('app.quotes.searchPlaceholder')"
                />
            </div>

            <div class="grid gap-2">
                <Label for="quote-status">{{ t('app.quotes.status') }}</Label>
                <select
                    id="quote-status"
                    v-model="filters.status_id"
                    class="rounded-md border border-input bg-background px-3 py-2 text-sm"
                >
                    <option value="">{{ t('app.quotes.allStatuses') }}</option>
                    <option v-for="status in props.statuses" :key="status.id" :value="String(status.id)">
                        {{ status.name_en ?? status.name }}
                    </option>
                </select>
            </div>

            <div class="grid gap-2">
                <Label for="quote-per-page">{{ t('app.leases.rowsPerPage') }}</Label>
                <select
                    id="quote-per-page"
                    v-model="filters.per_page"
                    class="rounded-md border border-input bg-background px-3 py-2 text-sm"
                >
                    <option v-for="option in perPageOptions" :key="option" :value="option">{{ option }}</option>
                </select>
            </div>

            <div class="flex items-end gap-2 md:col-span-4">
                <Button type="submit">{{ t('app.actions.apply') }}</Button>
                <Button type="button" variant="outline" @click="resetFilters">{{ t('app.actions.reset') }}</Button>
            </div>
        </form>

        <DataTable
            :columns="columns"
            :rows="quotes.data"
            :links="quotes.links"
            :row-href="(row: any) => quotesShow.url(row.id)"
            :empty-message="t('app.quotes.noQuotesFound')"
        />

        <div class="text-muted-foreground flex items-center justify-between text-sm">
            <p>
                {{ t('app.quotes.showingSummary', {
                    from: quotes.from ?? 0,
                    to: quotes.to ?? 0,
                    total: quotes.total,
                }) }}
            </p>
            <p>{{ t('app.common.pageSummary', { current: quotes.current_page, last: quotes.last_page }) }}</p>
        </div>
    </div>
</template>
