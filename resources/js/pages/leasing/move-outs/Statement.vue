<script setup lang="ts">
import { Head, setLayoutProps } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import { show as leasesShow } from '@/actions/App/Http/Controllers/Leasing/LeaseController';
import { settlement as settlementAction } from '@/actions/App/Http/Controllers/Leasing/MoveOutController';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useI18n } from '@/composables/useI18n';

type Deduction = {
    id: number | null;
    label_en: string;
    label_ar: string;
    amount: string;
};

type StatementSummary = {
    security_deposit: number;
    total_deductions: number;
    net_amount: number;
    is_refund: boolean;
    is_charge: boolean;
    abs_net_amount: number;
};

type LeaseRef = {
    id: number;
    contract_number: string;
    tenant: { id: number; name: string } | null;
    units: { id: number; name: string }[];
};

type MoveOutRef = {
    id: number;
    move_out_date: string | null;
    settled_at: string | null;
    deductions: Deduction[];
    summary: StatementSummary;
};

const props = defineProps<{
    lease: LeaseRef;
    moveOut: MoveOutRef;
}>();

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.leases.pageTitle'), href: '/leases' },
            {
                title: props.lease.contract_number,
                href: leasesShow.url(props.lease.id),
            },
            {
                title: t('app.moveout.settlement.title'),
                href: settlementAction.url(props.lease.id, props.moveOut.id),
            },
            { title: t('app.moveout.settlement.statementTitle'), href: '#' },
        ],
    });
});

const depositAmount = computed(() =>
    Number(props.moveOut.summary.security_deposit),
);
const totalDeductions = computed(() =>
    Number(props.moveOut.summary.total_deductions),
);
const netAmount = computed(() => Number(props.moveOut.summary.abs_net_amount));
const isRefund = computed(() => props.moveOut.summary.is_refund);
const isCharge = computed(() => props.moveOut.summary.is_charge);

function printStatement() {
    window.print();
}
</script>

<template>
    <Head :title="t('app.moveout.settlement.statementTitle')" />

    <div class="mx-auto max-w-3xl space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between print:hidden">
            <div class="space-y-1">
                <h1 class="text-2xl font-bold tracking-tight">
                    {{ t('app.moveout.settlement.statementTitle') }}
                </h1>
                <p class="text-sm text-muted-foreground">
                    {{
                        t('app.moveout.settlement.statementSubtitle', {
                            id: moveOut.id,
                        })
                    }}
                </p>
            </div>
            <Button variant="outline" @click="printStatement">
                {{ t('app.moveout.settlement.downloadPdf') }}
            </Button>
        </div>

        <!-- EN Statement -->
        <Card class="print:border-none print:shadow-none">
            <CardHeader>
                <CardTitle>{{
                    t('app.moveout.settlement.statementTitle')
                }}</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="space-y-4">
                    <!-- Lease & Unit Info -->
                    <dl class="grid grid-cols-2 gap-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-muted-foreground">
                                {{ t('app.leases.show.leaseNumber') }}
                            </dt>
                            <dd class="font-medium">
                                {{ lease.contract_number }}
                            </dd>
                        </div>
                        <div
                            v-if="lease.units.length > 0"
                            class="flex justify-between"
                        >
                            <dt class="text-muted-foreground">
                                {{ t('app.leases.show.units') }}
                            </dt>
                            <dd class="font-medium">
                                {{ lease.units.map((u) => u.name).join(', ') }}
                            </dd>
                        </div>
                        <div v-if="lease.tenant" class="flex justify-between">
                            <dt class="text-muted-foreground">
                                {{ t('app.leases.show.tenant') }}
                            </dt>
                            <dd class="font-medium">{{ lease.tenant.name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted-foreground">
                                {{ t('app.moveout.initiate.date') }}
                            </dt>
                            <dd class="font-medium">
                                {{
                                    moveOut.move_out_date ??
                                    t('app.common.notAvailable')
                                }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted-foreground">
                                {{ t('app.moveout.settlement.settlementDate') }}
                            </dt>
                            <dd class="font-medium">
                                {{
                                    moveOut.settled_at ??
                                    t('app.common.notAvailable')
                                }}
                            </dd>
                        </div>
                    </dl>

                    <!-- Deductions Table EN -->
                    <div class="border-t pt-4">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left">
                                    <th class="pe-4 pb-2">
                                        {{
                                            t('app.moveout.deductions.labelEn')
                                        }}
                                    </th>
                                    <th class="pe-4 pb-2 text-right">
                                        {{ t('app.moveout.deductions.amount') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="py-2 pe-4">
                                        {{
                                            t(
                                                'app.moveout.deductions.securityDepositLabel',
                                            )
                                        }}
                                    </td>
                                    <td class="py-2 pe-4 text-right">
                                        + {{ depositAmount.toLocaleString() }}
                                    </td>
                                </tr>
                                <tr
                                    v-for="(
                                        deduction, index
                                    ) in moveOut.deductions"
                                    :key="`en-${index}`"
                                    class="border-b last:border-0"
                                >
                                    <td class="py-2 pe-4">
                                        {{ deduction.label_en }}
                                    </td>
                                    <td class="py-2 pe-4 text-right">
                                        −
                                        {{
                                            Number(
                                                deduction.amount,
                                            ).toLocaleString()
                                        }}
                                    </td>
                                </tr>
                                <tr class="border-t-2 font-semibold">
                                    <td class="py-3 pe-4">
                                        {{
                                            isRefund
                                                ? t(
                                                      'app.moveout.settlement.refundTo',
                                                  )
                                                : t(
                                                      'app.moveout.settlement.outstandingCharge',
                                                  )
                                        }}
                                    </td>
                                    <td
                                        class="py-3 pe-4 text-right"
                                        :class="
                                            isCharge
                                                ? 'text-destructive'
                                                : 'text-green-600'
                                        "
                                    >
                                        {{ netAmount.toLocaleString() }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- AR Statement -->
        <Card class="print:border-none print:shadow-none" dir="rtl">
            <CardHeader>
                <CardTitle>{{
                    t('app.moveout.settlement.statementTitle')
                }}</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="space-y-4">
                    <!-- Lease & Unit Info -->
                    <dl class="grid grid-cols-2 gap-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-muted-foreground">
                                {{ t('app.leases.show.leaseNumber') }}
                            </dt>
                            <dd class="font-medium">
                                {{ lease.contract_number }}
                            </dd>
                        </div>
                        <div
                            v-if="lease.units.length > 0"
                            class="flex justify-between"
                        >
                            <dt class="text-muted-foreground">
                                {{ t('app.leases.show.units') }}
                            </dt>
                            <dd class="font-medium">
                                {{ lease.units.map((u) => u.name).join(', ') }}
                            </dd>
                        </div>
                        <div v-if="lease.tenant" class="flex justify-between">
                            <dt class="text-muted-foreground">
                                {{ t('app.leases.show.tenant') }}
                            </dt>
                            <dd class="font-medium">{{ lease.tenant.name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted-foreground">
                                {{ t('app.moveout.initiate.date') }}
                            </dt>
                            <dd class="font-medium">
                                {{
                                    moveOut.move_out_date ??
                                    t('app.common.notAvailable')
                                }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted-foreground">
                                {{ t('app.moveout.settlement.settlementDate') }}
                            </dt>
                            <dd class="font-medium">
                                {{
                                    moveOut.settled_at ??
                                    t('app.common.notAvailable')
                                }}
                            </dd>
                        </div>
                    </dl>

                    <!-- Deductions Table AR -->
                    <div class="border-t pt-4">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-right">
                                    <th class="ps-4 pb-2 text-right">
                                        {{ t('app.moveout.deductions.amount') }}
                                    </th>
                                    <th class="ps-4 pb-2">
                                        {{
                                            t('app.moveout.deductions.labelAr')
                                        }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="py-2 ps-4 text-right">
                                        + {{ depositAmount.toLocaleString() }}
                                    </td>
                                    <td class="py-2 ps-4">
                                        {{
                                            t(
                                                'app.moveout.deductions.securityDepositLabel',
                                            )
                                        }}
                                    </td>
                                </tr>
                                <tr
                                    v-for="(
                                        deduction, index
                                    ) in moveOut.deductions"
                                    :key="`ar-${index}`"
                                    class="border-b last:border-0"
                                >
                                    <td class="py-2 ps-4 text-right">
                                        −
                                        {{
                                            Number(
                                                deduction.amount,
                                            ).toLocaleString()
                                        }}
                                    </td>
                                    <td class="py-2 ps-4">
                                        {{ deduction.label_ar }}
                                    </td>
                                </tr>
                                <tr class="border-t-2 font-semibold">
                                    <td
                                        class="py-3 ps-4 text-right"
                                        :class="
                                            isCharge
                                                ? 'text-destructive'
                                                : 'text-green-600'
                                        "
                                    >
                                        {{ netAmount.toLocaleString() }}
                                    </td>
                                    <td class="py-3 ps-4">
                                        {{
                                            isRefund
                                                ? t(
                                                      'app.moveout.settlement.refundTo',
                                                  )
                                                : t(
                                                      'app.moveout.settlement.outstandingCharge',
                                                  )
                                        }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
