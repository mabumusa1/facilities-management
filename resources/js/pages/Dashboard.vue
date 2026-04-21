<script setup lang="ts">
import { Head, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';

const { currentLocale, t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [{ title: t('app.navigation.dashboard'), href: '/dashboard' }],
    });
});

defineProps<{
    stats?: {
        communities: number;
        buildings: number;
        units: number;
        tenants: number;
        activeLeases: number;
        openRequests: number;
        pendingTransactions: number;
        totalRevenue: string;
    };
    recentLeases?: {
        id: number;
        contract_number: string;
        tenant_name: string;
        status: string | null;
        start_date: string;
        end_date: string;
        amount: string;
    }[];
    recentRequests?: {
        id: number;
        category: string | null;
        status: string | null;
        priority: string | null;
        created_at: string;
    }[];
    requiresAttention?: {
        key: string;
        title: string;
        count: number;
        href: string;
    }[];
}>();

function formatCurrency(value: string | number | undefined): string {
    if (!value) {
        return Number(0).toLocaleString(currentLocale.value === 'ar' ? 'ar-SA' : 'en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    return Number(value).toLocaleString(currentLocale.value === 'ar' ? 'ar-SA' : 'en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

function priorityVariant(priority: string | null): 'default' | 'secondary' | 'destructive' | 'outline' {
    switch (priority) {
        case 'urgent': return 'destructive';
        case 'high': return 'destructive';
        case 'medium': return 'secondary';
        default: return 'outline';
    }
}
</script>

<template>
    <Head :title="t('app.dashboard.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <!-- Stats Cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">{{ t('app.dashboard.communities') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <Skeleton v-if="!stats" class="h-8 w-16" />
                    <div v-else class="text-2xl font-bold">{{ stats.communities }}</div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">{{ t('app.dashboard.buildings') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <Skeleton v-if="!stats" class="h-8 w-16" />
                    <div v-else class="text-2xl font-bold">{{ stats.buildings }}</div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">{{ t('app.dashboard.units') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <Skeleton v-if="!stats" class="h-8 w-16" />
                    <div v-else class="text-2xl font-bold">{{ stats.units }}</div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">{{ t('app.dashboard.tenants') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <Skeleton v-if="!stats" class="h-8 w-16" />
                    <div v-else class="text-2xl font-bold">{{ stats.tenants }}</div>
                </CardContent>
            </Card>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">{{ t('app.dashboard.activeLeases') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <Skeleton v-if="!stats" class="h-8 w-16" />
                    <div v-else class="text-2xl font-bold">{{ stats.activeLeases }}</div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">{{ t('app.dashboard.openRequests') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <Skeleton v-if="!stats" class="h-8 w-16" />
                    <div v-else class="text-2xl font-bold">{{ stats.openRequests }}</div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">{{ t('app.dashboard.pendingTransactions') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <Skeleton v-if="!stats" class="h-8 w-16" />
                    <div v-else class="text-2xl font-bold">{{ stats.pendingTransactions }}</div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">{{ t('app.dashboard.totalRevenue') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <Skeleton v-if="!stats" class="h-8 w-20" />
                    <div v-else class="text-2xl font-bold">{{ formatCurrency(stats.totalRevenue) }}</div>
                </CardContent>
            </Card>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.dashboard.requiresAttention') }}</CardTitle>
            </CardHeader>
            <CardContent>
                <div v-if="!requiresAttention" class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
                    <Skeleton v-for="i in 5" :key="i" class="h-24 w-full" />
                </div>
                <div v-else class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
                    <a
                        v-for="item in requiresAttention"
                        :key="item.key"
                        :href="item.href"
                        class="rounded-lg border p-4 transition-colors hover:bg-muted"
                    >
                        <p class="text-muted-foreground text-xs">{{ item.title }}</p>
                        <p class="mt-2 text-2xl font-bold">{{ item.count }}</p>
                    </a>
                </div>
            </CardContent>
        </Card>

        <!-- Recent Leases -->
        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.dashboard.recentLeases') }}</CardTitle>
            </CardHeader>
            <CardContent>
                <div v-if="!recentLeases" class="space-y-3">
                    <Skeleton v-for="i in 5" :key="i" class="h-10 w-full" />
                </div>
                <Table v-else>
                    <TableHeader>
                        <TableRow>
                            <TableHead>{{ t('app.dashboard.contract') }}</TableHead>
                            <TableHead>{{ t('app.dashboard.tenant') }}</TableHead>
                            <TableHead>{{ t('app.dashboard.status') }}</TableHead>
                            <TableHead>{{ t('app.dashboard.period') }}</TableHead>
                            <TableHead class="text-right">{{ t('app.dashboard.amount') }}</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="lease in recentLeases" :key="lease.id">
                            <TableCell>
                                <a :href="`/leases/${lease.id}`" class="text-primary hover:underline">{{ lease.contract_number }}</a>
                            </TableCell>
                            <TableCell>{{ lease.tenant_name }}</TableCell>
                            <TableCell><Badge variant="secondary">{{ lease.status ?? t('app.common.notAvailable') }}</Badge></TableCell>
                            <TableCell>{{ lease.start_date }} — {{ lease.end_date }}</TableCell>
                            <TableCell class="text-right">{{ formatCurrency(lease.amount) }}</TableCell>
                        </TableRow>
                        <TableRow v-if="recentLeases.length === 0">
                            <TableCell :colspan="5" class="text-muted-foreground text-center">{{ t('app.dashboard.noLeasesYet') }}</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>

        <!-- Recent Requests -->
        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.dashboard.recentServiceRequests') }}</CardTitle>
            </CardHeader>
            <CardContent>
                <div v-if="!recentRequests" class="space-y-3">
                    <Skeleton v-for="i in 5" :key="i" class="h-10 w-full" />
                </div>
                <Table v-else>
                    <TableHeader>
                        <TableRow>
                            <TableHead>{{ t('app.dashboard.id') }}</TableHead>
                            <TableHead>{{ t('app.dashboard.category') }}</TableHead>
                            <TableHead>{{ t('app.dashboard.status') }}</TableHead>
                            <TableHead>{{ t('app.dashboard.priority') }}</TableHead>
                            <TableHead>{{ t('app.dashboard.date') }}</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="req in recentRequests" :key="req.id">
                            <TableCell>
                                <a :href="`/requests/${req.id}`" class="text-primary hover:underline">#{{ req.id }}</a>
                            </TableCell>
                            <TableCell>{{ req.category ?? t('app.common.notAvailable') }}</TableCell>
                            <TableCell><Badge variant="secondary">{{ req.status ?? t('app.common.notAvailable') }}</Badge></TableCell>
                            <TableCell><Badge :variant="priorityVariant(req.priority)">{{ req.priority ?? t('app.common.notAvailable') }}</Badge></TableCell>
                            <TableCell>{{ req.created_at }}</TableCell>
                        </TableRow>
                        <TableRow v-if="recentRequests.length === 0">
                            <TableCell :colspan="5" class="text-muted-foreground text-center">{{ t('app.dashboard.noRequestsYet') }}</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    </div>
</template>
