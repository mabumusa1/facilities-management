<script setup lang="ts">
import { Head, router, setLayoutProps } from '@inertiajs/vue3';
import { computed, ref, watchEffect } from 'vue';
import {
    finalize as finalizeAction,
    settlement as settlementAction,
} from '@/actions/App/Http/Controllers/Leasing/MoveOutController';
import { show as leasesShow } from '@/actions/App/Http/Controllers/Leasing/LeaseController';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { useI18n } from '@/composables/useI18n';

type Deduction = {
    id: number | null;
    label_en: string;
    label_ar: string;
    amount: string;
    reason: string;
};

type SettlementSummary = {
    security_deposit: number;
    total_deductions: number;
    net_amount: number;
    is_refund: boolean;
    is_charge: boolean;
    is_zero: boolean;
};

type TenantRef = {
    id: number;
    name: string;
};

type UnitRef = {
    id: number;
    name: string;
};

type LeaseRef = {
    id: number;
    contract_number: string;
    security_deposit_amount: string | null;
    tenant: TenantRef | null;
    units: UnitRef[];
};

type MoveOutRef = {
    id: number;
    move_out_date: string | null;
    deductions: Deduction[];
    summary: SettlementSummary;
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
            { title: t('app.moveout.settlement.title'), href: '#' },
        ],
    });
});

const showConfirmDialog = ref(false);
const processing = ref(false);
const generateStatement = ref(false);
const errors = ref<Record<string, string>>({});

const depositAmount = computed(() =>
    Number(props.moveOut.summary.security_deposit),
);
const totalDeductions = computed(() =>
    Number(props.moveOut.summary.total_deductions),
);
const netAmount = computed(() => Number(props.moveOut.summary.net_amount));
const isRefund = computed(() => props.moveOut.summary.is_refund);
const isCharge = computed(() => props.moveOut.summary.is_charge);
const absNet = computed(() => Math.abs(netAmount.value));

function finalizeMoveOut() {
    errors.value = {};
    processing.value = true;

    router.post(
        finalizeAction.url(props.lease.id, props.moveOut.id),
        { generate_statement: generateStatement.value },
        {
            onError: (errs) => {
                errors.value = errs;
            },
            onFinish: () => {
                processing.value = false;
                showConfirmDialog.value = false;
            },
        },
    );
}
</script>

<template>
    <Head :title="t('app.moveout.settlement.title')" />

    <div class="space-y-6">
        <Heading
            :title="t('app.moveout.settlement.title')"
            :description="lease.contract_number"
        />

        <Card>
            <CardHeader>
                <CardTitle>{{
                    t('app.moveout.settlement.summaryTitle')
                }}</CardTitle>
            </CardHeader>
            <CardContent>
                <dl class="grid gap-3 text-sm sm:grid-cols-2">
                    <div class="flex justify-between">
                        <dt class="text-muted-foreground">
                            {{ t('app.leases.show.leaseNumber') }}
                        </dt>
                        <dd class="font-medium">{{ lease.contract_number }}</dd>
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
                    <div v-if="lease.tenant" class="flex justify-between">
                        <dt class="text-muted-foreground">
                            {{ t('app.leases.show.tenant') }}
                        </dt>
                        <dd class="font-medium">{{ lease.tenant.name }}</dd>
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
                </dl>
            </CardContent>
        </Card>

        <!-- Financial Breakdown -->
        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.moveout.deductions.title') }}</CardTitle>
            </CardHeader>
            <CardContent>
                <table
                    v-if="moveOut.deductions.length > 0"
                    class="w-full text-sm"
                >
                    <thead>
                        <tr class="border-b text-left">
                            <th class="pe-4 pb-2">#</th>
                            <th class="pe-4 pb-2">
                                {{ t('app.moveout.deductions.labelEn') }}
                            </th>
                            <th class="pe-4 pb-2">
                                {{ t('app.moveout.deductions.amount') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(deduction, index) in moveOut.deductions"
                            :key="index"
                            class="border-b last:border-0"
                        >
                            <td class="py-2 pe-4 text-muted-foreground">
                                {{ index + 1 }}
                            </td>
                            <td class="py-2 pe-4">{{ deduction.label_en }}</td>
                            <td class="py-2 pe-4">
                                −
                                {{ Number(deduction.amount).toLocaleString() }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p v-else class="py-4 text-sm text-muted-foreground">
                    {{ t('app.moveout.deductions.noDeductions') }}
                </p>

                <div class="mt-4 border-t pt-4">
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt>
                                {{
                                    t(
                                        'app.moveout.deductions.securityDepositLabel',
                                    )
                                }}
                            </dt>
                            <dd class="font-medium">
                                + {{ depositAmount.toLocaleString() }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt>
                                {{
                                    t('app.moveout.deductions.totalDeductions')
                                }}
                            </dt>
                            <dd class="font-medium text-destructive">
                                − {{ totalDeductions.toLocaleString() }}
                            </dd>
                        </div>
                        <hr />
                        <div
                            class="flex justify-between text-base font-semibold"
                        >
                            <dt v-if="isRefund">
                                {{ t('app.moveout.settlement.refundTo') }}
                            </dt>
                            <dt v-else-if="isCharge">
                                {{
                                    t(
                                        'app.moveout.settlement.outstandingCharge',
                                    )
                                }}
                            </dt>
                            <dt v-else>
                                {{ t('app.moveout.settlement.noBalance') }}
                            </dt>
                            <dd :class="isCharge ? 'text-destructive' : ''">
                                {{ absNet.toLocaleString() }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </CardContent>
        </Card>

        <!-- Actions on Finalize -->
        <Card>
            <CardHeader>
                <CardTitle>{{
                    t('app.moveout.settlement.actionsOnFinalize')
                }}</CardTitle>
            </CardHeader>
            <CardContent>
                <ul
                    class="space-y-2 text-sm"
                    aria-label="{{ t('app.moveout.settlement.actionsOnFinalize') }}"
                >
                    <li v-if="isRefund" class="flex items-start gap-2">
                        <span aria-hidden="true" class="mt-0.5 text-green-600"
                            >✓</span
                        >
                        <span>{{
                            t('app.moveout.settlement.createRefund', {
                                amount: absNet.toLocaleString(),
                            })
                        }}</span>
                    </li>
                    <li v-if="isCharge" class="flex items-start gap-2">
                        <span aria-hidden="true" class="mt-0.5 text-amber-600"
                            >✓</span
                        >
                        <span>{{
                            t('app.moveout.settlement.createCharge', {
                                amount: absNet.toLocaleString(),
                            })
                        }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span aria-hidden="true" class="mt-0.5 text-blue-600"
                            >✓</span
                        >
                        <span>{{
                            t('app.moveout.settlement.voidSchedules')
                        }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span aria-hidden="true" class="mt-0.5 text-purple-600"
                            >✓</span
                        >
                        <span>{{
                            t('app.moveout.settlement.leaseTerminated')
                        }}</span>
                    </li>
                    <li
                        v-for="unit in lease.units"
                        :key="unit.id"
                        class="flex items-start gap-2"
                    >
                        <span aria-hidden="true" class="mt-0.5 text-green-600"
                            >✓</span
                        >
                        <span>{{
                            t('app.moveout.settlement.unitAvailable', {
                                name: unit.name,
                            })
                        }}</span>
                    </li>
                </ul>

                <div
                    v-if="errors.finalize"
                    class="mt-3 rounded-md bg-destructive/10 p-3 text-sm text-destructive"
                    role="alert"
                >
                    {{ errors.finalize }}
                </div>
            </CardContent>
        </Card>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-3">
            <Button
                type="button"
                variant="default"
                size="lg"
                class="bg-green-600 hover:bg-green-700"
                :disabled="processing"
                @click="showConfirmDialog = true"
            >
                {{ t('app.moveout.settlement.finalize') }}
            </Button>
        </div>

        <!-- Confirmation Dialog -->
        <Dialog v-model:open="showConfirmDialog">
            <DialogContent
                aria-modal="true"
                :aria-labelledby="'confirm-dialog-title'"
            >
                <DialogHeader>
                    <DialogTitle id="confirm-dialog-title">
                        {{ t('app.moveout.settlement.confirmTitle') }}
                    </DialogTitle>
                    <DialogDescription>
                        {{ t('app.moveout.settlement.confirmDesc') }}
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-3 py-4">
                    <p class="text-sm font-medium">
                        {{ t('app.moveout.settlement.actionsOnFinalize') }}:
                    </p>
                    <ul class="space-y-2 text-sm text-muted-foreground">
                        <li v-if="isRefund" class="flex items-start gap-2">
                            <span>•</span>
                            <span>{{
                                t('app.moveout.settlement.createRefund', {
                                    amount: absNet.toLocaleString(),
                                })
                            }}</span>
                        </li>
                        <li v-if="isCharge" class="flex items-start gap-2">
                            <span>•</span>
                            <span>{{
                                t('app.moveout.settlement.createCharge', {
                                    amount: absNet.toLocaleString(),
                                })
                            }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span>•</span>
                            <span>{{
                                t('app.moveout.settlement.leaseTerminated')
                            }}</span>
                        </li>
                        <li
                            v-for="unit in lease.units"
                            :key="unit.id"
                            class="flex items-start gap-2"
                        >
                            <span>•</span>
                            <span>{{
                                t('app.moveout.settlement.unitAvailable', {
                                    name: unit.name,
                                })
                            }}</span>
                        </li>
                    </ul>

                    <div class="flex items-center gap-2 pt-2">
                        <Checkbox
                            id="generate_statement"
                            v-model:checked="generateStatement"
                        />
                        <label
                            for="generate_statement"
                            class="cursor-pointer text-sm"
                        >
                            {{ t('app.moveout.settlement.generateStatement') }}
                        </label>
                    </div>
                </div>

                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        :disabled="processing"
                        @click="showConfirmDialog = false"
                    >
                        {{ t('app.common.cancel') }}
                    </Button>
                    <Button
                        type="button"
                        variant="default"
                        :disabled="processing"
                        @click="finalizeMoveOut"
                    >
                        {{
                            processing
                                ? t('app.common.processing')
                                : t('app.moveout.settlement.confirmButton')
                        }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
