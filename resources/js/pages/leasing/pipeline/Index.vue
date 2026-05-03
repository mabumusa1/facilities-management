<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { computed, reactive, ref, watchEffect } from 'vue';
import {
    exportMethod as pipelineExport,
    exportPreview,
    index as pipelineIndex,
} from '@/actions/App/Http/Controllers/Leasing/LeasePipelineController';
import { show as leaseShow } from '@/actions/App/Http/Controllers/Leasing/LeaseController';
import { show as alertSettingsShow } from '@/actions/App/Http/Controllers/Leasing/LeaseAlertSettingsController';
import PageHeader from '@/components/PageHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();

type LeaseRow = {
    id: number;
    contract_number: string;
    unit: string | null;
    building: string | null;
    community: string | null;
    tenant_name: string;
    start_date: string | null;
    end_date: string | null;
    rental_total_amount: string | number | null;
    payment_frequency: string | null;
    status: { id: number; name: string; name_en: string | null; name_ar: string | null } | null;
    days_until_expiry: number | null;
};

type Groups = {
    expiring_soon: LeaseRow[];
    active: LeaseRow[];
    expired: LeaseRow[];
    terminated: LeaseRow[];
    pending: LeaseRow[];
};

const props = defineProps<{
    groups: Groups;
    totalCount: number;
    communities: Array<{ id: number; name: string }>;
    statuses: Array<{ id: number; name: string; name_en: string | null }>;
    filters: {
        expiry_window: number;
        status_id: string;
        community_id: string;
        search: string;
    };
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.leases.pageTitle'), href: '/leases' },
            { title: t('app.pipeline.title'), href: '#' },
        ],
    });
});

const filters = reactive({
    expiry_window: String(props.filters.expiry_window ?? 30),
    status_id: props.filters.status_id ?? '',
    community_id: props.filters.community_id ?? '',
    search: props.filters.search ?? '',
});

function applyFilters() {
    router.get(pipelineIndex.url(), { ...filters }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function resetFilters() {
    filters.expiry_window = '30';
    filters.status_id = '';
    filters.community_id = '';
    filters.search = '';
    applyFilters();
}

// ---- Export dialog ----
const showExportDialog = ref(false);
const exportCount = ref<number | null>(null);
const exportColumns = ref<string[]>([]);

async function openExportDialog() {
    try {
        const url = exportPreview.url(undefined, {
            query: {
                expiry_window: filters.expiry_window,
                status_id: filters.status_id || undefined,
                community_id: filters.community_id || undefined,
                search: filters.search || undefined,
            },
        });
        const res = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        if (res.ok) {
            const data = await res.json();
            exportCount.value = data.count;
            exportColumns.value = data.columns ?? [];
        }
    } catch {
        exportCount.value = props.totalCount;
    }
    showExportDialog.value = true;
}

function downloadExport() {
    const params = new URLSearchParams();
    if (filters.expiry_window) {
        params.set('expiry_window', filters.expiry_window);
    }
    if (filters.status_id) {
        params.set('status_id', filters.status_id);
    }
    if (filters.community_id) {
        params.set('community_id', filters.community_id);
    }
    if (filters.search) {
        params.set('search', filters.search);
    }
    window.location.href = `${pipelineExport.url()}?${params.toString()}`;
    showExportDialog.value = false;
}

// ---- Expiry badge ----
function expiryBadgeVariant(days: number | null): 'destructive' | 'warning' | null {
    if (days === null) {
        return null;
    }
    if (days <= 14) {
        return 'destructive';
    }
    if (days <= 30) {
        return 'warning';
    }
    return null;
}

function expiryBadgeText(days: number | null): string {
    if (days === null) {
        return '';
    }
    if (days < 0) {
        return t('app.pipeline.daysExpired');
    }
    return t('app.pipeline.daysRemaining', { n: days });
}

function expiryAriaLabel(days: number | null): string {
    if (days === null) {
        return '';
    }
    if (days < 0) {
        return t('app.pipeline.daysExpired');
    }
    return `${days} days remaining`;
}

// ---- Groups display ----
type GroupKey = keyof Groups;

const groupKeys: GroupKey[] = ['expiring_soon', 'active', 'expired', 'terminated', 'pending'];

function groupLabel(key: GroupKey): string {
    const count = props.groups[key].length;
    const map: Record<GroupKey, string> = {
        expiring_soon: t('app.pipeline.groupExpiringSoon', { n: count }),
        active: t('app.pipeline.groupActive', { n: count }),
        expired: t('app.pipeline.groupExpired', { n: count }),
        terminated: t('app.pipeline.groupTerminated', { n: count }),
        pending: t('app.pipeline.groupPending', { n: count }),
    };
    return map[key];
}

const shownCount = computed(() =>
    groupKeys.reduce((sum, k) => sum + props.groups[k].length, 0)
);

const expiryWindowOptions = [
    { value: '30', label: t('app.pipeline.expiryFilter30') },
    { value: '60', label: t('app.pipeline.expiryFilter60') },
    { value: '90', label: t('app.pipeline.expiryFilter90') },
];
</script>

<template>
    <div>
        <Head :title="t('app.pipeline.title')" />

        <PageHeader :title="t('app.pipeline.title')">
            <template #actions>
                <Button variant="outline" as-child size="sm">
                    <Link :href="alertSettingsShow.url()">
                        {{ t('app.pipeline.settingsLink') }}
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <div class="p-4 space-y-4">
            <!-- Filter bar -->
            <Card>
                <CardContent class="pt-4">
                    <div class="flex flex-wrap gap-3 items-end">
                        <div class="flex flex-col gap-1">
                            <Label for="expiry_window">{{ t('app.pipeline.expiryWindow') }}</Label>
                            <Select v-model="filters.expiry_window">
                                <SelectTrigger id="expiry_window" class="w-32">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="opt in expiryWindowOptions"
                                        :key="opt.value"
                                        :value="opt.value"
                                    >
                                        {{ opt.label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div class="flex flex-col gap-1">
                            <Label for="status_filter">{{ t('app.leases.status') }}</Label>
                            <Select v-model="filters.status_id">
                                <SelectTrigger id="status_filter" class="w-40">
                                    <SelectValue :placeholder="t('app.pipeline.allStatuses')" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="">{{ t('app.pipeline.allStatuses') }}</SelectItem>
                                    <SelectItem
                                        v-for="s in statuses"
                                        :key="s.id"
                                        :value="String(s.id)"
                                    >
                                        {{ s.name_en ?? s.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div class="flex flex-col gap-1">
                            <Label for="community_filter">{{ t('app.pipeline.columnCommunity') }}</Label>
                            <Select v-model="filters.community_id">
                                <SelectTrigger id="community_filter" class="w-44">
                                    <SelectValue :placeholder="t('app.pipeline.allCommunities')" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="">{{ t('app.pipeline.allCommunities') }}</SelectItem>
                                    <SelectItem
                                        v-for="c in communities"
                                        :key="c.id"
                                        :value="String(c.id)"
                                    >
                                        {{ c.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div class="flex flex-col gap-1 flex-1 min-w-48">
                            <Label for="search_input">{{ t('app.pipeline.search') }}</Label>
                            <Input
                                id="search_input"
                                v-model="filters.search"
                                :placeholder="t('app.pipeline.searchPlaceholder')"
                                @keyup.enter="applyFilters"
                            />
                        </div>

                        <div class="flex gap-2">
                            <Button @click="applyFilters">{{ t('app.pipeline.apply') }}</Button>
                            <Button variant="outline" @click="resetFilters">{{ t('app.pipeline.reset') }}</Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Status groups -->
            <div v-for="key in groupKeys" :key="key">
                <Card v-if="groups[key].length > 0">
                    <CardHeader>
                        <CardTitle as="h2">{{ groupLabel(key) }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <caption class="sr-only">
                                    {{ groupLabel(key) }}
                                </caption>
                                <thead>
                                    <tr class="border-b text-muted-foreground">
                                        <th scope="col" class="px-3 py-2 text-start font-medium">{{ t('app.pipeline.columnLease') }}</th>
                                        <th scope="col" class="px-3 py-2 text-start font-medium">{{ t('app.pipeline.columnUnit') }}</th>
                                        <th scope="col" class="px-3 py-2 text-start font-medium">{{ t('app.pipeline.columnBuilding') }}</th>
                                        <th scope="col" class="px-3 py-2 text-start font-medium">{{ t('app.pipeline.columnCommunity') }}</th>
                                        <th scope="col" class="px-3 py-2 text-start font-medium">{{ t('app.pipeline.columnTenant') }}</th>
                                        <th scope="col" class="px-3 py-2 text-start font-medium">{{ t('app.pipeline.columnStartDate') }}</th>
                                        <th scope="col" class="px-3 py-2 text-start font-medium">{{ t('app.pipeline.columnEndDate') }}</th>
                                        <th scope="col" class="px-3 py-2 text-start font-medium">{{ t('app.pipeline.columnRent') }}</th>
                                        <th scope="col" class="px-3 py-2 text-start font-medium">{{ t('app.pipeline.columnDays') }}</th>
                                        <th scope="col" class="px-3 py-2 text-start font-medium sr-only">{{ t('app.common.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="lease in groups[key]"
                                        :key="lease.id"
                                        class="border-b last:border-0 hover:bg-muted/30"
                                    >
                                        <td class="px-3 py-2 font-medium">{{ lease.contract_number }}</td>
                                        <td class="px-3 py-2">{{ lease.unit ?? '—' }}</td>
                                        <td class="px-3 py-2">{{ lease.building ?? '—' }}</td>
                                        <td class="px-3 py-2">{{ lease.community ?? '—' }}</td>
                                        <td class="px-3 py-2">{{ lease.tenant_name || '—' }}</td>
                                        <td class="px-3 py-2">{{ lease.start_date ?? '—' }}</td>
                                        <td class="px-3 py-2">{{ lease.end_date ?? '—' }}</td>
                                        <td class="px-3 py-2">{{ lease.rental_total_amount ?? '—' }}</td>
                                        <td class="px-3 py-2">
                                            <span
                                                v-if="lease.days_until_expiry !== null && expiryBadgeVariant(lease.days_until_expiry)"
                                                :aria-label="expiryAriaLabel(lease.days_until_expiry)"
                                            >
                                                <Badge :variant="expiryBadgeVariant(lease.days_until_expiry)!">
                                                    {{ expiryBadgeText(lease.days_until_expiry) }}
                                                </Badge>
                                            </span>
                                            <span
                                                v-else-if="lease.days_until_expiry !== null"
                                                :aria-label="expiryAriaLabel(lease.days_until_expiry)"
                                                class="text-muted-foreground"
                                            >
                                                {{ expiryBadgeText(lease.days_until_expiry) }}
                                            </span>
                                            <span v-else class="text-muted-foreground">—</span>
                                        </td>
                                        <td class="px-3 py-2">
                                            <Button as-child variant="ghost" size="sm">
                                                <Link :href="leaseShow.url(lease.id)">
                                                    {{ t('app.pipeline.viewLease') }}
                                                </Link>
                                            </Button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Empty state -->
            <div
                v-if="shownCount === 0"
                class="py-16 text-center space-y-3"
            >
                <p class="text-muted-foreground">{{ t('app.pipeline.empty') }}</p>
                <Button variant="outline" @click="resetFilters">
                    {{ t('app.pipeline.clearFilters') }}
                </Button>
            </div>

            <!-- Footer: count + export -->
            <div class="flex items-center justify-between text-sm text-muted-foreground px-1">
                <span>
                    {{ t('app.pipeline.countTotal', { shown: shownCount, total: totalCount }) }}
                </span>
                <Button variant="outline" size="sm" @click="openExportDialog">
                    {{ t('app.pipeline.export') }}
                </Button>
            </div>
        </div>

        <!-- Export dialog -->
        <Dialog v-model:open="showExportDialog">
            <DialogContent aria-labelledby="export-dialog-title">
                <DialogHeader>
                    <DialogTitle id="export-dialog-title">{{ t('app.pipeline.exportTitle') }}</DialogTitle>
                </DialogHeader>

                <p v-if="exportCount !== null" class="text-sm text-muted-foreground">
                    {{ t('app.pipeline.exportMatching', { n: exportCount }) }}
                </p>

                <div class="space-y-2">
                    <p class="text-sm font-medium">{{ t('app.pipeline.exportColumns') }}</p>
                    <ul class="grid grid-cols-2 gap-1 text-sm text-muted-foreground list-none">
                        <li v-for="col in exportColumns" :key="col" class="flex items-center gap-1">
                            <span aria-hidden="true">✓</span> {{ col }}
                        </li>
                    </ul>
                </div>

                <DialogFooter class="flex gap-2 justify-end">
                    <Button variant="outline" @click="showExportDialog = false">
                        {{ t('app.pipeline.exportCancel') }}
                    </Button>
                    <Button @click="downloadExport">
                        {{ t('app.pipeline.exportDownload') }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
