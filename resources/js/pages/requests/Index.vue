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
import type { Paginated, ServiceRequest } from '@/types';

const { isArabic, t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.requests.pageTitle'), href: '/requests' },
        ],
    });
});

const props = defineProps<{
    requests: Paginated<ServiceRequest>;
    statuses: Array<{ id: number; name: string; name_ar: string | null; name_en: string | null }>;
    categories: Array<{ id: number; name: string; name_ar: string | null; name_en: string | null }>;
    priorities: string[];
    filters: {
        search: string;
        status_id: string;
        category_id: string;
        priority: string;
        per_page: string;
    };
}>();

const filters = reactive({
    search: props.filters.search ?? '',
    status_id: props.filters.status_id ?? '',
    category_id: props.filters.category_id ?? '',
    priority: props.filters.priority ?? '',
    per_page: props.filters.per_page ?? '15',
});

const perPageOptions = ['10', '15', '25', '50'];

function localizedOptionName(option: { name: string; name_ar?: string | null; name_en?: string | null }): string {
    if (isArabic.value) {
        return option.name_ar ?? option.name ?? option.name_en ?? '';
    }

    return option.name_en ?? option.name ?? option.name_ar ?? '';
}

function applyFilters() {
    router.get('/requests', { ...filters }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function resetFilters() {
    filters.search = '';
    filters.status_id = '';
    filters.category_id = '';
    filters.priority = '';
    filters.per_page = '15';
    applyFilters();
}

const columns = computed<Column<ServiceRequest>[]>(() => [
    { key: 'id', label: t('app.requests.table.id') },
    { key: 'category.name', label: t('app.requests.table.category') },
    { key: 'subcategory.name', label: t('app.requests.table.subcategory') },
    { key: 'status.name', label: t('app.requests.table.status') },
    { key: 'community.name', label: t('app.requests.table.community') },
    { key: 'created_at', label: t('app.requests.table.created') },
]);
</script>

<template>
    <Head :title="t('app.requests.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            :title="t('app.requests.heading')"
            :description="t('app.requests.description')"
            create-href="/requests/create"
            :create-label="t('app.requests.newRequest')"
        />

        <form class="grid gap-4 rounded-lg border p-4 md:grid-cols-5" @submit.prevent="applyFilters">
            <div class="grid gap-2 md:col-span-2">
                <Label for="request-search">{{ t('app.requests.search') }}</Label>
                <Input
                    id="request-search"
                    v-model="filters.search"
                    :placeholder="t('app.requests.searchPlaceholder')"
                />
            </div>

            <div class="grid gap-2">
                <Label for="request-status">{{ t('app.requests.status') }}</Label>
                <select id="request-status" v-model="filters.status_id" class="rounded-md border border-input bg-background px-3 py-2 text-sm">
                    <option value="">{{ t('app.requests.allStatuses') }}</option>
                    <option v-for="status in props.statuses" :key="status.id" :value="String(status.id)">
                        {{ localizedOptionName(status) }}
                    </option>
                </select>
            </div>

            <div class="grid gap-2">
                <Label for="request-category">{{ t('app.requests.category') }}</Label>
                <select id="request-category" v-model="filters.category_id" class="rounded-md border border-input bg-background px-3 py-2 text-sm">
                    <option value="">{{ t('app.requests.allCategories') }}</option>
                    <option v-for="category in props.categories" :key="category.id" :value="String(category.id)">
                        {{ localizedOptionName(category) }}
                    </option>
                </select>
            </div>

            <div class="grid gap-2">
                <Label for="request-priority">{{ t('app.requests.priority') }}</Label>
                <select id="request-priority" v-model="filters.priority" class="rounded-md border border-input bg-background px-3 py-2 text-sm">
                    <option value="">{{ t('app.requests.allPriorities') }}</option>
                    <option v-for="priority in props.priorities" :key="priority" :value="priority">{{ priority }}</option>
                </select>
            </div>

            <div class="grid gap-2">
                <Label for="request-per-page">{{ t('app.requests.rowsPerPage') }}</Label>
                <select id="request-per-page" v-model="filters.per_page" class="rounded-md border border-input bg-background px-3 py-2 text-sm">
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
            :rows="requests.data"
            :links="requests.links"
            :row-href="(row: any) => `/requests/${row.id}`"
            :empty-message="t('app.requests.noRequestsFound')"
        />

        <div class="text-muted-foreground flex items-center justify-between text-sm">
            <p>
                {{ t('app.requests.showingSummary', { from: requests.from ?? 0, to: requests.to ?? 0, total: requests.total }) }}
            </p>
            <p>{{ t('app.common.pageSummary', { current: requests.current_page, last: requests.last_page }) }}</p>
        </div>
    </div>
</template>
