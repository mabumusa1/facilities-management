<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { reactive, watchEffect } from 'vue';
import { index, show } from '@/actions/App/Http/Controllers/Services/AdminServiceRequestController';
import DataTable from '@/components/DataTable.vue';
import type { Column } from '@/components/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useI18n } from '@/composables/useI18n';
import type { Paginated } from '@/types';

type LocalCategory = {
    id: number;
    name_en: string;
    name_ar: string;
};

type LocalStatus = {
    id: number;
    name: string;
    name_en: string | null;
    name_ar: string | null;
};

type LocalCommunity = {
    id: number;
    name: string;
};

type AdminServiceRequest = {
    id: number;
    request_code: string | null;
    urgency: string;
    priority: string | null;
    room_location: string | null;
    description: string | null;
    sla_response_due_at: string | null;
    sla_resolution_due_at: string | null;
    assigned_to_user_id: number | null;
    assigned_at: string | null;
    created_at: string | null;
    is_overdue: boolean;
    is_near_sla: boolean;
    category: LocalCategory | null;
    subcategory: LocalCategory | null;
    status: LocalStatus | null;
    unit: { id: number; name: string } | null;
    community: LocalCommunity | null;
    assigned_to: { id: number; name: string } | null;
    requester_name: string | null;
};

type TabCounts = {
    all: number;
    unassigned: number;
    overdue: number;
    sla_breach: number;
};

const props = defineProps<{
    serviceRequests: Paginated<AdminServiceRequest>;
    tabCounts: TabCounts;
    statuses: LocalStatus[];
    serviceCategories: LocalCategory[];
    communities: LocalCommunity[];
    urgencies: string[];
    filters: {
        search: string;
        status_id: string;
        service_category_id: string;
        community_id: string;
        urgency: string;
        tab: string;
    };
}>();

const { isArabic, t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.serviceRequests.triagePageTitle'), href: index.url() },
        ],
    });
});

const filters = reactive({
    search: props.filters.search ?? '',
    status_id: props.filters.status_id ?? '',
    service_category_id: props.filters.service_category_id ?? '',
    community_id: props.filters.community_id ?? '',
    urgency: props.filters.urgency ?? '',
    tab: props.filters.tab ?? 'all',
});

const tabs = [
    { key: 'all', label: t('app.serviceRequests.tabAll'), count: props.tabCounts.all },
    { key: 'unassigned', label: t('app.serviceRequests.tabUnassigned'), count: props.tabCounts.unassigned },
    { key: 'overdue', label: t('app.serviceRequests.tabOverdue'), count: props.tabCounts.overdue },
    { key: 'sla_breach', label: t('app.serviceRequests.tabSlaBreach'), count: props.tabCounts.sla_breach },
];

function localizedName(item: LocalCategory): string {
    return isArabic.value ? item.name_ar : item.name_en;
}

function localizedStatusName(status: LocalStatus): string {
    if (isArabic.value) {
        return status.name_ar ?? status.name_en ?? status.name;
    }
    return status.name_en ?? status.name;
}

function formatDate(isoString: string | null): string {
    if (! isoString) return '';
    return new Date(isoString).toLocaleDateString(isArabic.value ? 'ar' : 'en', {
        dateStyle: 'medium',
    });
}

function urgencyLabel(urgency: string): string {
    return urgency === 'urgent'
        ? t('app.serviceRequests.urgencyUrgent')
        : t('app.serviceRequests.urgencyNormal');
}

function applyFilters() {
    router.get(index.url(), { ...filters }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function resetFilters() {
    filters.search = '';
    filters.status_id = '';
    filters.service_category_id = '';
    filters.community_id = '';
    filters.urgency = '';
    filters.tab = 'all';
    applyFilters();
}

function switchTab(tabKey: string) {
    filters.tab = tabKey;
    applyFilters();
}

const columns: Column<AdminServiceRequest>[] = [
    { key: 'request_code', label: t('app.serviceRequests.colRef') },
    { key: 'requester_name', label: t('app.serviceRequests.colResident'), render: (row) => row.requester_name ?? '—' },
    { key: 'unit.name', label: t('app.serviceRequests.colUnit'), render: (row) => row.unit?.name ?? '—' },
    {
        key: 'category',
        label: t('app.serviceRequests.colCategory'),
        render: (row) => row.category ? localizedName(row.category) : '—',
    },
    { key: 'urgency', label: t('app.serviceRequests.colUrgency'), render: (row) => urgencyLabel(row.urgency) },
    {
        key: 'status',
        label: t('app.serviceRequests.colStatus'),
        render: (row) => row.status ? localizedStatusName(row.status) : '—',
    },
    { key: 'created_at', label: t('app.serviceRequests.detailSubmitted'), render: (row) => formatDate(row.created_at) },
    { key: 'assigned_to', label: t('app.serviceRequests.colAssigned'), render: (row) => row.assigned_to?.name ?? '—' },
];

function hasActiveFilters(): boolean {
    return filters.search !== ''
        || filters.status_id !== ''
        || filters.service_category_id !== ''
        || filters.community_id !== ''
        || filters.urgency !== '';
}
</script>

<template>
    <Head :title="t('app.serviceRequests.triagePageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            :title="t('app.serviceRequests.triageHeading')"
            :description="t('app.serviceRequests.triageDescription')"
        />

        <!-- Tabs -->
        <div
            role="tablist"
            class="flex gap-1 border-b"
        >
            <button
                v-for="tab in tabs"
                :key="tab.key"
                role="tab"
                :aria-selected="filters.tab === tab.key"
                :aria-label="`${tab.label}, ${tab.count}`"
                class="flex items-center gap-1.5 border-b-2 px-4 py-2 text-sm font-medium transition-colors"
                :class="filters.tab === tab.key
                    ? 'border-primary text-primary'
                    : 'border-transparent text-muted-foreground hover:text-foreground'"
                @click="switchTab(tab.key)"
            >
                {{ tab.label }}
                <Badge
                    variant="secondary"
                    class="text-xs"
                >
                    {{ tab.count }}
                </Badge>
            </button>
        </div>

        <!-- Filter bar -->
        <div class="flex flex-wrap items-end gap-3">
            <div class="flex flex-1 min-w-48 flex-col gap-1">
                <Label>{{ t('app.serviceRequests.searchPlaceholder') }}</Label>
                <Input
                    v-model="filters.search"
                    :placeholder="t('app.serviceRequests.searchPlaceholder')"
                    @keydown.enter="applyFilters"
                />
            </div>

            <div class="flex flex-col gap-1 min-w-36">
                <Label>{{ t('app.serviceRequests.filterStatus') }}</Label>
                <Select v-model="filters.status_id">
                    <SelectTrigger>
                        <SelectValue :placeholder="t('app.serviceRequests.filterStatus')" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">{{ t('app.serviceRequests.filterStatus') }}</SelectItem>
                        <SelectItem
                            v-for="status in statuses"
                            :key="status.id"
                            :value="String(status.id)"
                        >
                            {{ localizedStatusName(status) }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div class="flex flex-col gap-1 min-w-36">
                <Label>{{ t('app.serviceRequests.filterCategory') }}</Label>
                <Select v-model="filters.service_category_id">
                    <SelectTrigger>
                        <SelectValue :placeholder="t('app.serviceRequests.filterCategory')" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">{{ t('app.serviceRequests.filterCategory') }}</SelectItem>
                        <SelectItem
                            v-for="cat in serviceCategories"
                            :key="cat.id"
                            :value="String(cat.id)"
                        >
                            {{ localizedName(cat) }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div class="flex flex-col gap-1 min-w-36">
                <Label>{{ t('app.serviceRequests.filterCommunity') }}</Label>
                <Select v-model="filters.community_id">
                    <SelectTrigger>
                        <SelectValue :placeholder="t('app.serviceRequests.filterCommunity')" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">{{ t('app.serviceRequests.filterCommunity') }}</SelectItem>
                        <SelectItem
                            v-for="community in communities"
                            :key="community.id"
                            :value="String(community.id)"
                        >
                            {{ community.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div class="flex flex-col gap-1 min-w-32">
                <Label>{{ t('app.serviceRequests.filterUrgency') }}</Label>
                <Select v-model="filters.urgency">
                    <SelectTrigger>
                        <SelectValue :placeholder="t('app.serviceRequests.filterUrgency')" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">{{ t('app.serviceRequests.filterUrgency') }}</SelectItem>
                        <SelectItem value="normal">{{ t('app.serviceRequests.urgencyNormal') }}</SelectItem>
                        <SelectItem value="urgent">{{ t('app.serviceRequests.urgencyUrgent') }}</SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div class="flex gap-2">
                <Button @click="applyFilters">{{ t('app.serviceRequests.applyFilters') }}</Button>
                <Button
                    variant="outline"
                    @click="resetFilters"
                >
                    {{ t('app.serviceRequests.resetFilters') }}
                </Button>
            </div>
        </div>

        <!-- Unassigned tab empty state -->
        <div
            v-if="filters.tab === 'unassigned' && tabCounts.unassigned === 0"
            class="flex flex-col items-center justify-center gap-2 rounded-lg border border-green-200 bg-green-50 p-8 text-center dark:border-green-900 dark:bg-green-950"
        >
            <p class="text-green-800 dark:text-green-200 font-medium">
                {{ t('app.serviceRequests.allAssigned') }}
            </p>
        </div>

        <!-- Empty state with filters active -->
        <div
            v-else-if="serviceRequests.data.length === 0 && hasActiveFilters()"
            class="flex flex-col items-center justify-center gap-4 rounded-lg border border-dashed p-12 text-center"
        >
            <p class="text-muted-foreground">{{ t('app.serviceRequests.noFilteredRequests') }}</p>
            <Button
                variant="link"
                @click="resetFilters"
            >
                {{ t('app.serviceRequests.clearFilters') }}
            </Button>
        </div>

        <!-- Empty state no requests at all -->
        <div
            v-else-if="serviceRequests.data.length === 0"
            class="flex flex-col items-center justify-center gap-4 rounded-lg border border-dashed p-12 text-center"
        >
            <p class="text-muted-foreground">{{ t('app.serviceRequests.noRequests') }}</p>
        </div>

        <!-- Data table with SLA indicators -->
        <div
            v-else
            class="space-y-4"
        >
            <div class="rounded-md border">
                <table class="w-full caption-bottom text-sm">
                    <thead class="[&_tr]:border-b">
                        <tr class="border-b transition-colors">
                            <th
                                v-for="col in columns"
                                :key="col.key"
                                class="h-12 px-4 text-start align-middle font-medium text-muted-foreground"
                            >
                                {{ col.label }}
                            </th>
                            <th class="h-12 px-4 text-start align-middle font-medium text-muted-foreground">
                                {{ t('app.serviceRequests.colSla') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="[&_tr:last-child]:border-0">
                        <tr
                            v-for="sr in serviceRequests.data"
                            :key="sr.id"
                            class="border-b transition-colors hover:bg-muted/50 cursor-pointer"
                            :class="{
                                'border-l-4 border-l-red-500 bg-red-50 dark:bg-red-950': sr.is_overdue,
                                'border-l-4 border-l-yellow-500': sr.is_near_sla && ! sr.is_overdue,
                            }"
                            @click="router.visit(show.url(sr.id))"
                        >
                            <td class="px-4 py-3">
                                <bdi
                                    dir="ltr"
                                    class="font-mono text-primary"
                                >
                                    {{ sr.request_code ?? '—' }}
                                </bdi>
                            </td>
                            <td class="px-4 py-3">{{ sr.requester_name ?? '—' }}</td>
                            <td class="px-4 py-3">{{ sr.unit?.name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                {{ sr.category ? localizedName(sr.category) : '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <Badge
                                    :variant="sr.urgency === 'urgent' ? 'destructive' : 'secondary'"
                                    class="text-xs"
                                >
                                    {{ urgencyLabel(sr.urgency) }}
                                </Badge>
                            </td>
                            <td class="px-4 py-3">
                                <Badge
                                    v-if="sr.status"
                                    variant="outline"
                                    class="text-xs"
                                >
                                    {{ localizedStatusName(sr.status) }}
                                </Badge>
                                <span v-else>—</span>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground text-xs">
                                {{ formatDate(sr.created_at) }}
                            </td>
                            <td class="px-4 py-3">{{ sr.assigned_to?.name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <Badge
                                    v-if="sr.is_overdue"
                                    variant="destructive"
                                    class="text-xs"
                                    :aria-label="t('app.serviceRequests.slaOverdue')"
                                >
                                    ⚠ {{ t('app.serviceRequests.slaOverdue') }}
                                </Badge>
                                <Badge
                                    v-else-if="sr.is_near_sla"
                                    class="bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 text-xs"
                                >
                                    ⏳ {{ t('app.serviceRequests.slaDueSoon') }}
                                </Badge>
                                <span
                                    v-else-if="sr.sla_response_due_at"
                                    class="text-green-600 text-xs"
                                >
                                    ✓ {{ t('app.serviceRequests.slaOnTime') }}
                                </span>
                                <span
                                    v-else
                                    class="text-muted-foreground text-xs"
                                >
                                    —
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="serviceRequests.last_page > 1"
                class="flex justify-center gap-1"
            >
                <template
                    v-for="link in serviceRequests.links"
                    :key="link.label"
                >
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="rounded px-3 py-1 text-sm transition-colors"
                        :class="link.active
                            ? 'bg-primary text-primary-foreground'
                            : 'hover:bg-muted'"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>
    </div>
</template>
