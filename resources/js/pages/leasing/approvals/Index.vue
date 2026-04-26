<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { reactive, watchEffect } from 'vue';
import { index as approvalsIndex } from '@/actions/App/Http/Controllers/Leasing/ApprovalController';
import { show as leaseShow } from '@/actions/App/Http/Controllers/Leasing/LeaseController';
import PageHeader from '@/components/PageHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { useI18n } from '@/composables/useI18n';
import type { Lease, Paginated } from '@/types';

const { t } = useI18n();

const props = defineProps<{
    leases: Paginated<Lease>;
    filters: {
        search: string;
        community_id: string | null;
    };
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.leases.pageTitle'), href: '/leases' },
            { title: t('app.leases.approval.queueTitle'), href: '#' },
        ],
    });
});

const filters = reactive({
    search: props.filters.search ?? '',
});

function applyFilters() {
    router.get(approvalsIndex.url(), { ...filters }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function tenantName(lease: Lease): string {
    if (lease.tenant?.name) {
        return lease.tenant.name;
    }
    const first = lease.tenant?.first_name ?? '';
    const last = lease.tenant?.last_name ?? '';
    return `${first} ${last}`.trim();
}
</script>

<template>
    <div>
        <Head :title="t('app.leases.approval.queueTitle')" />

        <PageHeader :title="t('app.leases.approval.queueTitle')" />

        <div class="p-4 space-y-4">
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('app.leases.approval.queueTitle') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="mb-4 flex gap-2">
                        <Input
                            v-model="filters.search"
                            :placeholder="t('app.leases.searchPlaceholder')"
                            class="max-w-sm"
                            @keyup.enter="applyFilters"
                        />
                        <Button variant="secondary" @click="applyFilters">
                            {{ t('app.leases.search') }}
                        </Button>
                    </div>

                    <div v-if="leases.data.length === 0" class="py-12 text-center text-muted-foreground">
                        {{ t('app.leases.approval.noPending') }}
                    </div>

                    <div v-else class="space-y-2">
                        <div
                            v-for="lease in leases.data"
                            :key="lease.id"
                            class="flex items-center justify-between rounded-md border p-4 hover:bg-muted/30"
                        >
                            <div class="space-y-1">
                                <div class="font-medium">{{ lease.contract_number }}</div>
                                <div class="text-muted-foreground text-sm">{{ tenantName(lease) }}</div>
                                <div v-if="lease.kyc_submitted_at" class="text-muted-foreground text-xs">
                                    {{ t('app.leases.approval.timeline') }}: {{ lease.kyc_submitted_at }}
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <Badge>{{ lease.status?.name ?? '—' }}</Badge>
                                <Button as-child size="sm">
                                    <Link :href="leaseShow.url(lease.id)">
                                        {{ t('app.leases.approval.review') }} →
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    </div>

                    <div v-if="leases.last_page > 1" class="mt-4 flex items-center justify-between text-sm text-muted-foreground">
                        <span>
                            {{ t('app.leases.showingSummary', { from: leases.from, to: leases.to, total: leases.total }) }}
                        </span>
                        <div class="flex gap-2">
                            <Button
                                v-if="leases.prev_page_url"
                                variant="outline"
                                size="sm"
                                @click="router.get(leases.prev_page_url)"
                            >
                                &larr;
                            </Button>
                            <Button
                                v-if="leases.next_page_url"
                                variant="outline"
                                size="sm"
                                @click="router.get(leases.next_page_url)"
                            >
                                &rarr;
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
