<script setup lang="ts">
import { Head, router, setLayoutProps } from '@inertiajs/vue3';
import { computed, ref, watch, watchEffect } from 'vue';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Input } from '@/components/ui/input';
import { useI18n } from '@/composables/useI18n';
import type { Paginated, Resident } from '@/types';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.contacts.residents.pageTitle'), href: '/residents' },
        ],
    });
});

const props = defineProps<{
    residents: Paginated<Resident>;
    filters: { search: string };
}>();

const search = ref(props.filters.search ?? '');

let debounceHandle: ReturnType<typeof setTimeout> | null = null;

watch(search, (value) => {
    if (debounceHandle) {
        clearTimeout(debounceHandle);
    }
    debounceHandle = setTimeout(() => {
        router.get(
            '/residents',
            { search: value },
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }, 300);
});

function arabicName(row: Resident): string {
    return [row.first_name_ar, row.last_name_ar].filter(Boolean).join(' ') || '—';
}

function englishName(row: Resident): string {
    return [row.first_name, row.last_name].filter(Boolean).join(' ') || '—';
}

const columns = computed<Column<Resident>[]>(() => [
    { key: 'name_en', label: t('app.contacts.residents.firstNameEn'), render: englishName },
    { key: 'name_ar', label: t('app.contacts.residents.firstNameAr'), render: arabicName },
    { key: 'phone_number', label: t('app.contacts.shared.phone') },
    { key: 'units_count', label: t('app.contacts.shared.units') },
    { key: 'leases_count', label: t('app.contacts.shared.leases') },
    {
        key: 'active',
        label: t('app.contacts.shared.status'),
        render: (row: Resident) =>
            row.active ? t('app.common.active') : t('app.common.inactive'),
    },
]);
</script>

<template>
    <Head :title="t('app.contacts.residents.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            :title="t('app.contacts.residents.heading')"
            :description="t('app.contacts.residents.description')"
            create-href="/residents/create"
            :create-label="t('app.contacts.residents.newResident')"
        />

        <div class="max-w-md">
            <Input
                v-model="search"
                type="search"
                dir="auto"
                :placeholder="t('app.contacts.residents.searchPlaceholder')"
                :aria-label="t('app.contacts.residents.searchPlaceholder')"
            />
        </div>

        <DataTable
            :columns="columns"
            :rows="residents.data"
            :links="residents.links"
            :row-href="(row: any) => `/residents/${row.id}`"
            :empty-message="t('app.contacts.residents.emptyMessage')"
        />
    </div>
</template>
