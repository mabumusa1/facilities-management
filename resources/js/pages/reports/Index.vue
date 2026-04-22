<script setup lang="ts">
import { Head, Link, setLayoutProps, useHttp } from '@inertiajs/vue3';
import { computed, onMounted, ref, watchEffect } from 'vue';
import Heading from '@/components/Heading.vue';
import { useI18n } from '@/composables/useI18n';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { reports as dashboardReports, systemReports as dashboardSystemReports } from '@/routes/dashboard';
import { lease, maintenance } from '@/routes/dashboard/system-reports';
import {
    bookmarks,
    filters,
    load,
    pages,
    prepare,
    print,
    refresh,
    render as renderReport,
    save,
    saveAs,
    settings,
    theme,
    zoom,
} from '@/routes/report';
import { active } from '@/routes/report/pages';
import { expenses, income } from '@/routes/reports';
import { units as performanceUnits } from '@/routes/reports/performance';

type ApiResponse<TData> = {
    data: TData;
    message?: string;
    success?: boolean;
};

type ReportPage = {
    name: string;
    display_name: string;
    is_active?: boolean;
};

type ReportFilter = {
    name: string;
    value: string | number | null;
};

type ReportSummary = {
    count: number;
    total: number;
    paid: number;
    unpaid: number;
};

type UnitPerformance = {
    vacant: number;
    sold: number;
    leased: number;
    soldAndLeased: number;
};

const { t } = useI18n();

const props = defineProps<{
    reportMode: string;
    title: string;
}>();

const readHttp = useHttp<Record<string, never>, unknown>({});
const writeHttp = useHttp<{
    reportId: string;
    name: string;
    theme: string;
    value: number;
}, unknown>({
    reportId: 'default-report',
    name: '',
    theme: 'light',
    value: 1,
});

const isRunningAction = ref(false);
const isLoadingSummary = ref(false);
const actionMessage = ref<string | null>(null);
const actionError = ref<string | null>(null);
const lastAction = ref<string | null>(null);
const lastResponse = ref<unknown>(null);

const reportPages = ref<ReportPage[]>([]);
const activeReportPage = ref<ReportPage | null>(null);
const reportFilters = ref<ReportFilter[]>([]);
const reportBookmarks = ref<Array<Record<string, unknown>>>([]);
const reportSettings = ref<Record<string, unknown>>({});

const incomeSummary = ref<ReportSummary | null>(null);
const expenseSummary = ref<ReportSummary | null>(null);
const unitsSummary = ref<UnitPerformance | null>(null);

const numberFormatter = new Intl.NumberFormat();
const amountFormatter = new Intl.NumberFormat(undefined, {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.reports'), href: dashboardReports.url() },
        ],
    });
});

const reportViewLinks = computed(() => [
    {
        title: t('app.navigation.reports'),
        href: dashboardReports.url(),
        active: props.reportMode === 'reports',
    },
    {
        title: t('app.navigation.systemReports'),
        href: dashboardSystemReports.url(),
        active: props.reportMode === 'system-reports',
    },
    {
        title: t('app.reports.leaseReports'),
        href: lease.url(),
        active: props.reportMode === 'system-reports-lease',
    },
    {
        title: t('app.reports.maintenanceReports'),
        href: maintenance.url(),
        active: props.reportMode === 'system-reports-maintenance',
    },
]);

const formattedLastResponse = computed(() => {
    if (lastResponse.value === null) {
        return '';
    }

    return JSON.stringify(lastResponse.value, null, 2);
});

const unitPerformanceBadges = computed(() => {
    if (unitsSummary.value === null) {
        return [];
    }

    return [
        { label: 'Vacant', value: unitsSummary.value.vacant },
        { label: 'Sold', value: unitsSummary.value.sold },
        { label: 'Leased', value: unitsSummary.value.leased },
        { label: 'Sold & Leased', value: unitsSummary.value.soldAndLeased },
    ];
});

function normalizeError(error: unknown): string {
    if (error instanceof Error && error.message !== '') {
        return error.message;
    }

    return 'Request failed. Please try again.';
}

function getResponseData<TData>(response: unknown): TData | null {
    if (typeof response !== 'object' || response === null || !('data' in response)) {
        return null;
    }

    return (response as ApiResponse<TData>).data;
}

function getResponseMessage(response: unknown): string | null {
    if (typeof response !== 'object' || response === null || !('message' in response)) {
        return null;
    }

    const message = (response as ApiResponse<unknown>).message;

    return typeof message === 'string' ? message : null;
}

function formatInteger(value: number | null | undefined): string {
    if (value === null || value === undefined) {
        return '—';
    }

    return numberFormatter.format(value);
}

function formatAmount(value: number | null | undefined): string {
    if (value === null || value === undefined) {
        return '—';
    }

    return amountFormatter.format(value);
}

async function runAction(endpoint: string, executor: () => Promise<unknown>): Promise<unknown | null> {
    isRunningAction.value = true;
    actionError.value = null;

    try {
        const response = await executor();
        lastAction.value = endpoint;
        lastResponse.value = response;
        actionMessage.value = getResponseMessage(response) ?? 'Request completed.';

        return response;
    } catch (error) {
        lastAction.value = endpoint;
        lastResponse.value = null;
        actionMessage.value = null;
        actionError.value = normalizeError(error);

        return null;
    } finally {
        isRunningAction.value = false;
    }
}

async function runReadAction(endpoint: string, request: ReturnType<typeof pages>): Promise<unknown | null> {
    return runAction(endpoint, () => readHttp.submit(request));
}

async function runWriteAction(endpoint: string, request: ReturnType<typeof load>): Promise<unknown | null> {
    return runAction(endpoint, () => writeHttp.submit(request));
}

async function fetchReportMetadata(): Promise<void> {
    const pagesData = await readHttp.submit(pages());
    reportPages.value = getResponseData<ReportPage[]>(pagesData) ?? [];

    const activePageData = await readHttp.submit(active());
    activeReportPage.value = getResponseData<ReportPage>(activePageData);

    const filtersData = await readHttp.submit(filters());
    reportFilters.value = getResponseData<ReportFilter[]>(filtersData) ?? [];

    const bookmarksData = await readHttp.submit(bookmarks());
    reportBookmarks.value = getResponseData<Array<Record<string, unknown>>>(bookmarksData) ?? [];

    const settingsData = await readHttp.submit(settings());
    reportSettings.value = getResponseData<Record<string, unknown>>(settingsData) ?? {};
}

async function refreshSummaryData(): Promise<void> {
    isLoadingSummary.value = true;

    try {
        const incomeData = await readHttp.submit(income());
        incomeSummary.value = getResponseData<ReportSummary>(incomeData);

        const expensesData = await readHttp.submit(expenses());
        expenseSummary.value = getResponseData<ReportSummary>(expensesData);

        const performanceData = await readHttp.submit(performanceUnits());
        unitsSummary.value = getResponseData<UnitPerformance>(performanceData);
    } catch (error) {
        actionError.value = normalizeError(error);
    } finally {
        isLoadingSummary.value = false;
    }
}

async function runLoadAction(): Promise<void> {
    await runWriteAction('/report/load', load());
}

async function runPrepareAction(): Promise<void> {
    await runWriteAction('/report/prepare', prepare());
}

async function runRenderAction(): Promise<void> {
    await runWriteAction('/report/render', renderReport());
}

async function runPagesAction(): Promise<void> {
    const response = await runReadAction('/report/pages', pages());
    reportPages.value = getResponseData<ReportPage[]>(response) ?? [];
}

async function runActivePageAction(): Promise<void> {
    const response = await runReadAction('/report/pages/active', active());
    activeReportPage.value = getResponseData<ReportPage>(response);
}

async function runFiltersAction(): Promise<void> {
    const response = await runReadAction('/report/filters', filters());
    reportFilters.value = getResponseData<ReportFilter[]>(response) ?? [];
}

async function runBookmarksAction(): Promise<void> {
    const response = await runReadAction('/report/bookmarks', bookmarks());
    reportBookmarks.value = getResponseData<Array<Record<string, unknown>>>(response) ?? [];
}

async function runSettingsAction(): Promise<void> {
    const response = await runReadAction('/report/settings', settings());
    reportSettings.value = getResponseData<Record<string, unknown>>(response) ?? {};
}

async function runPrintAction(): Promise<void> {
    await runWriteAction('/report/print', print());
}

async function runRefreshAction(): Promise<void> {
    await runWriteAction('/report/refresh', refresh());
    await refreshSummaryData();
}

async function runSaveAction(): Promise<void> {
    await runWriteAction('/report/save', save());
}

async function runSaveAsAction(): Promise<void> {
    await runWriteAction('/report/saveAs', saveAs());
}

async function runThemeAction(): Promise<void> {
    await runWriteAction('/report/theme', theme());
}

async function runZoomAction(): Promise<void> {
    await runWriteAction('/report/zoom', zoom());
}

async function hydratePage(): Promise<void> {
    try {
        await fetchReportMetadata();
        await refreshSummaryData();
    } catch {
        actionError.value = 'Unable to initialize report data.';
    }
}

onMounted(() => {
    void hydratePage();
});
</script>

<template>
    <Head :title="props.title" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <Heading variant="small" :title="props.title" :description="t('app.reports.currentMode', { mode: props.reportMode })" />
            <div class="flex items-center gap-2">
                <Badge variant="secondary">Mode: {{ props.reportMode }}</Badge>
                <Button variant="outline" size="sm" :disabled="isLoadingSummary" @click="refreshSummaryData">
                    {{ isLoadingSummary ? 'Refreshing...' : 'Refresh Summary' }}
                </Button>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.reports.reportViews') }}</CardTitle>
                <CardDescription>Switch between available report pages.</CardDescription>
            </CardHeader>
            <CardContent class="flex flex-wrap gap-2">
                <Button
                    v-for="reportView in reportViewLinks"
                    :key="reportView.href"
                    :variant="reportView.active ? 'default' : 'outline'"
                    as-child
                >
                    <Link :href="reportView.href">{{ reportView.title }}</Link>
                </Button>
            </CardContent>
        </Card>

        <div class="grid gap-6 xl:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('app.reports.integratedEndpoints') }}</CardTitle>
                    <CardDescription>Run report actions and endpoint reads directly from this page.</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="grid gap-3 md:grid-cols-2">
                        <div class="space-y-2">
                            <p class="text-xs font-medium text-muted-foreground">Report ID</p>
                            <div class="flex items-center gap-2">
                                <Input v-model="writeHttp.reportId" placeholder="default-report" />
                                <Button :disabled="isRunningAction" @click="runLoadAction">Load</Button>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <p class="text-xs font-medium text-muted-foreground">Save As Name</p>
                            <div class="flex items-center gap-2">
                                <Input v-model="writeHttp.name" placeholder="Quarterly Snapshot" />
                                <Button :disabled="isRunningAction" @click="runSaveAsAction">Save As</Button>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <p class="text-xs font-medium text-muted-foreground">Theme</p>
                            <div class="flex items-center gap-2">
                                <Input v-model="writeHttp.theme" placeholder="light" />
                                <Button :disabled="isRunningAction" @click="runThemeAction">Apply Theme</Button>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <p class="text-xs font-medium text-muted-foreground">Zoom</p>
                            <div class="flex items-center gap-2">
                                <Input v-model.number="writeHttp.value" type="number" min="0.25" max="4" step="0.25" />
                                <Button :disabled="isRunningAction" @click="runZoomAction">Apply Zoom</Button>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-2 md:grid-cols-2 xl:grid-cols-3">
                        <Button variant="outline" :disabled="isRunningAction" @click="runPrepareAction">Prepare</Button>
                        <Button variant="outline" :disabled="isRunningAction" @click="runRenderAction">Render</Button>
                        <Button variant="outline" :disabled="isRunningAction" @click="runPagesAction">Pages</Button>
                        <Button variant="outline" :disabled="isRunningAction" @click="runActivePageAction">Active Page</Button>
                        <Button variant="outline" :disabled="isRunningAction" @click="runFiltersAction">Filters</Button>
                        <Button variant="outline" :disabled="isRunningAction" @click="runBookmarksAction">Bookmarks</Button>
                        <Button variant="outline" :disabled="isRunningAction" @click="runSettingsAction">Settings</Button>
                        <Button variant="outline" :disabled="isRunningAction" @click="runPrintAction">Print</Button>
                        <Button variant="outline" :disabled="isRunningAction" @click="runRefreshAction">Refresh</Button>
                        <Button variant="outline" :disabled="isRunningAction" @click="runSaveAction">Save</Button>
                    </div>

                    <div class="space-y-2">
                        <p v-if="actionMessage !== null" class="text-sm text-emerald-700">
                            {{ actionMessage }}
                        </p>
                        <p v-if="actionError !== null" class="text-sm text-red-600">
                            {{ actionError }}
                        </p>

                        <div v-if="lastAction !== null" class="rounded-md border bg-muted/40 p-3">
                            <p class="text-xs font-semibold text-muted-foreground">Latest Action: {{ lastAction }}</p>
                            <pre class="mt-2 max-h-64 overflow-auto text-xs">{{ formattedLastResponse }}</pre>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Report Snapshot</CardTitle>
                    <CardDescription>Income, expenses, and unit performance summaries.</CardDescription>
                </CardHeader>
                <CardContent class="space-y-5">
                    <div class="grid gap-3 md:grid-cols-2">
                        <div class="rounded-md border p-3">
                            <p class="text-xs text-muted-foreground">Income Total</p>
                            <p class="text-lg font-semibold">{{ formatAmount(incomeSummary?.total) }}</p>
                            <p class="text-xs text-muted-foreground">Transactions: {{ formatInteger(incomeSummary?.count) }}</p>
                        </div>

                        <div class="rounded-md border p-3">
                            <p class="text-xs text-muted-foreground">Expenses Total</p>
                            <p class="text-lg font-semibold">{{ formatAmount(expenseSummary?.total) }}</p>
                            <p class="text-xs text-muted-foreground">Transactions: {{ formatInteger(expenseSummary?.count) }}</p>
                        </div>
                    </div>

                    <div class="rounded-md border p-3">
                        <p class="text-xs text-muted-foreground">Unit Performance</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <Badge
                                v-for="item in unitPerformanceBadges"
                                :key="item.label"
                                variant="secondary"
                            >
                                {{ item.label }}: {{ formatInteger(item.value) }}
                            </Badge>
                            <p v-if="unitPerformanceBadges.length === 0" class="text-sm text-muted-foreground">
                                No unit performance data loaded.
                            </p>
                        </div>
                    </div>

                    <div class="rounded-md border p-3">
                        <p class="text-xs text-muted-foreground">Active Page</p>
                        <p class="text-sm font-medium">{{ activeReportPage?.display_name ?? 'Not available' }}</p>

                        <p class="mt-3 text-xs text-muted-foreground">Available Pages</p>
                        <ul class="mt-1 list-disc space-y-1 pl-5 text-sm">
                            <li v-for="page in reportPages" :key="page.name">
                                {{ page.display_name }}
                            </li>
                            <li v-if="reportPages.length === 0" class="list-none pl-0 text-muted-foreground">
                                No pages returned.
                            </li>
                        </ul>
                    </div>

                    <div class="rounded-md border p-3">
                        <p class="text-xs text-muted-foreground">Filters</p>
                        <ul class="mt-1 list-disc space-y-1 pl-5 text-sm">
                            <li v-for="filterItem in reportFilters" :key="filterItem.name">
                                {{ filterItem.name }}: {{ filterItem.value ?? 'None' }}
                            </li>
                            <li v-if="reportFilters.length === 0" class="list-none pl-0 text-muted-foreground">
                                No filters returned.
                            </li>
                        </ul>

                        <p class="mt-3 text-xs text-muted-foreground">Bookmarks</p>
                        <p class="text-sm">
                            {{ reportBookmarks.length }} loaded
                        </p>

                        <p class="mt-3 text-xs text-muted-foreground">Settings</p>
                        <pre class="mt-1 max-h-32 overflow-auto text-xs">{{ JSON.stringify(reportSettings, null, 2) }}</pre>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
