<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useI18n } from '@/composables/useI18n';
import type { Lease } from '@/types';

const props = defineProps<{ lease: Lease }>();
const { t } = useI18n();

const tenantName = computed(() => {
    if (props.lease.tenant?.name) {
        return props.lease.tenant.name;
    }

    const firstName = props.lease.tenant?.first_name ?? '';
    const lastName = props.lease.tenant?.last_name ?? '';

    return `${firstName} ${lastName}`.trim();
});

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.leases.pageTitle'), href: '/leases' },
            { title: t('app.leases.show.breadcrumb'), href: '#' },
        ],
    });
});

function deleteLease() {
    if (confirm(t('app.leases.show.confirmDeletePrompt'))) {
        router.delete(`/leases/${props.lease.id}`);
    }
}
</script>

<template>
    <Head :title="t('app.leases.show.pageTitle', { contract: lease.contract_number })" />
    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">{{ lease.contract_number }}</h2>
                <p class="text-muted-foreground text-sm">{{ tenantName || t('app.common.notAvailable') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <Button v-if="!lease.is_sub_lease" variant="secondary" as-child>
                    <Link :href="`/leases/${lease.id}/subleases/create`">{{ t('app.leases.show.createSublease') }}</Link>
                </Button>
                <Button variant="outline" as-child><Link :href="`/leases/${lease.id}/edit`">{{ t('app.actions.edit') }}</Link></Button>
                <Button variant="destructive" @click="deleteLease">{{ t('app.actions.delete') }}</Button>
            </div>
        </div>

        <Card v-if="lease.is_sub_lease && lease.parent_lease">
            <CardHeader><CardTitle>{{ t('app.leases.show.parentLease') }}</CardTitle></CardHeader>
            <CardContent>
                <Link :href="`/leases/${lease.parent_lease.id}`" class="text-primary underline">
                    {{ lease.parent_lease.contract_number }}
                </Link>
            </CardContent>
        </Card>

        <div class="grid gap-4 md:grid-cols-4">
            <Card>
                <CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.leases.show.status') }}</CardTitle></CardHeader>
                <CardContent><Badge>{{ lease.status?.name ?? '—' }}</Badge></CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.leases.show.totalAmount') }}</CardTitle></CardHeader>
                <CardContent><div class="text-2xl font-bold">{{ lease.rental_total_amount }}</div></CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.leases.show.unpaid') }}</CardTitle></CardHeader>
                <CardContent><div class="text-2xl font-bold text-destructive">{{ lease.total_unpaid_amount ?? '0' }}</div></CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.leases.show.type') }}</CardTitle></CardHeader>
                <CardContent><Badge variant="secondary">{{ lease.tenant_type }}</Badge></CardContent>
            </Card>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>{{ t('app.leases.show.duration') }}</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.leases.show.start') }}</span><span>{{ lease.start_date }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.leases.show.end') }}</span><span>{{ lease.end_date }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.leases.show.handover') }}</span><span>{{ lease.handover_date }}</span></div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>{{ t('app.leases.show.financial') }}</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.leases.show.rentalType') }}</span><span>{{ lease.rental_type }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.leases.show.securityDeposit') }}</span><span>{{ lease.security_deposit_amount ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.leases.show.subLease') }}</span><Badge :variant="lease.is_sub_lease ? 'default' : 'secondary'">{{ lease.is_sub_lease ? t('app.common.yes') : t('app.common.no') }}</Badge></div>
                </CardContent>
            </Card>
        </div>

        <Card v-if="lease.units && lease.units.length > 0">
            <CardHeader><CardTitle>{{ t('app.leases.show.units') }}</CardTitle></CardHeader>
            <CardContent>
                <div class="space-y-2">
                    <Link v-for="unit in lease.units" :key="unit.id" :href="`/units/${unit.id}`" class="flex items-center justify-between rounded-md border p-3 hover:bg-muted/50">
                        <span class="font-medium">{{ unit.name }}</span>
                    </Link>
                </div>
            </CardContent>
        </Card>

        <Card v-if="lease.additional_fees && lease.additional_fees.length > 0">
            <CardHeader><CardTitle>{{ t('app.leases.show.additionalFees') }}</CardTitle></CardHeader>
            <CardContent>
                <div class="space-y-2">
                    <div v-for="fee in lease.additional_fees" :key="fee.id" class="flex items-center justify-between rounded-md border p-3">
                        <span class="font-medium">{{ fee.name ?? fee.description ?? `Fee #${fee.id}` }}</span>
                        <span class="text-muted-foreground text-sm">{{ fee.amount ?? '—' }}</span>
                    </div>
                </div>
            </CardContent>
        </Card>

        <Card v-if="lease.escalations && lease.escalations.length > 0">
            <CardHeader><CardTitle>{{ t('app.leases.show.escalations') }}</CardTitle></CardHeader>
            <CardContent>
                <div class="space-y-2">
                    <div v-for="esc in lease.escalations" :key="esc.id" class="flex items-center justify-between rounded-md border p-3">
                        <span class="font-medium">{{ esc.type ?? `Escalation #${esc.id}` }}</span>
                        <span class="text-muted-foreground text-sm">{{ esc.rate ?? esc.amount ?? '—' }}{{ esc.rate ? '%' : '' }}</span>
                    </div>
                </div>
            </CardContent>
        </Card>

        <Card v-if="lease.subleases && lease.subleases.length > 0">
            <CardHeader><CardTitle>{{ t('app.leases.show.subLeases') }}</CardTitle></CardHeader>
            <CardContent>
                <div class="space-y-2">
                    <Link
                        v-for="sublease in lease.subleases"
                        :key="sublease.id"
                        :href="`/leases/${sublease.id}`"
                        class="flex items-center justify-between rounded-md border p-3 hover:bg-muted/50"
                    >
                        <span class="font-medium">{{ sublease.contract_number }}</span>
                        <span class="text-muted-foreground text-sm">{{ sublease.status?.name_en ?? sublease.status?.name ?? '—' }}</span>
                    </Link>
                </div>
            </CardContent>
        </Card>

        <div class="grid gap-4 md:grid-cols-2">
            <Card v-if="lease.created_by">
                <CardHeader><CardTitle>{{ t('app.leases.show.createdBy') }}</CardTitle></CardHeader>
                <CardContent>
                    <span>{{ lease.created_by.first_name }} {{ lease.created_by.last_name }}</span>
                </CardContent>
            </Card>
            <Card v-if="lease.deal_owner">
                <CardHeader><CardTitle>{{ t('app.leases.show.dealOwner') }}</CardTitle></CardHeader>
                <CardContent>
                    <span>{{ lease.deal_owner.first_name }} {{ lease.deal_owner.last_name }}</span>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
