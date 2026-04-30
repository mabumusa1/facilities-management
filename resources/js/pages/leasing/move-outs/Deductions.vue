<script setup lang="ts">
import { Head, router, setLayoutProps } from '@inertiajs/vue3';
import { computed, ref, watchEffect } from 'vue';
import {
    saveDeductions as saveDeductionsAction,
} from '@/actions/App/Http/Controllers/Leasing/MoveOutController';
import { show as leasesShow } from '@/actions/App/Http/Controllers/Leasing/LeaseController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useI18n } from '@/composables/useI18n';

type ReasonOption = { value: string; label: string };

type Deduction = {
    id: number | null;
    label_en: string;
    label_ar: string;
    amount: string;
    reason: string;
};

type Summary = {
    security_deposit: number;
    total_deductions: number;
    refund_amount: number;
    exceeds_deposit: boolean;
};

type MoveOutDetail = {
    id: number;
    deductions: Deduction[];
    summary: Summary;
};

type LeaseRef = {
    id: number;
    contract_number: string;
    security_deposit_amount: string | null;
};

const props = defineProps<{
    lease: LeaseRef;
    moveOut: MoveOutDetail;
    reasons: ReasonOption[];
}>();

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.leases.pageTitle'), href: '/leases' },
            { title: props.lease.contract_number, href: leasesShow.url(props.lease.id) },
            { title: t('app.moveout.deductions.title'), href: '#' },
        ],
    });
});

const deductions = ref<Deduction[]>(
    props.moveOut.deductions.map((d) => ({ ...d })),
);

const errors = ref<Record<string, string>>({});
const processing = ref(false);
const showAddDialog = ref(false);
const exceedWarningAcknowledged = ref(false);

const newDeduction = ref<Omit<Deduction, 'id'>>({
    label_en: '',
    label_ar: '',
    amount: '',
    reason: '',
});

const totalDeductions = computed(() =>
    deductions.value.reduce((sum, d) => sum + parseFloat(d.amount || '0'), 0),
);

const securityDeposit = computed(() => props.moveOut.summary.security_deposit);

const refundAmount = computed(() => securityDeposit.value - totalDeductions.value);

const exceedsDeposit = computed(() => totalDeductions.value > securityDeposit.value);

function openAddDialog() {
    newDeduction.value = { label_en: '', label_ar: '', amount: '', reason: '' };
    showAddDialog.value = true;
}

function addDeduction() {
    deductions.value.push({
        id: null,
        label_en: newDeduction.value.label_en,
        label_ar: newDeduction.value.label_ar,
        amount: newDeduction.value.amount,
        reason: newDeduction.value.reason,
    });
    showAddDialog.value = false;
}

function removeDeduction(index: number) {
    deductions.value.splice(index, 1);
}

function saveDeductions() {
    if (exceedsDeposit.value && ! exceedWarningAcknowledged.value) {
        exceedWarningAcknowledged.value = true;
        return;
    }

    errors.value = {};
    processing.value = true;

    router.post(
        saveDeductionsAction.url(props.lease.id, props.moveOut.id),
        { deductions: deductions.value },
        {
            onError: (errs) => {
                errors.value = errs;
            },
            onFinish: () => {
                processing.value = false;
            },
        },
    );
}
</script>

<template>
    <Head :title="t('app.moveout.deductions.title')" />

    <div class="space-y-6">
        <Heading
            :title="t('app.moveout.deductions.title')"
            :description="lease.contract_number"
        />

        <!-- Exceed deposit warning -->
        <div
            v-if="exceedsDeposit"
            class="rounded-md border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-700 dark:bg-amber-950 dark:text-amber-200"
            role="alert"
        >
            {{ t('app.moveout.deductions.exceedWarning') }}
        </div>

        <!-- Deductions table -->
        <Card>
            <CardHeader class="flex flex-row items-center justify-between">
                <CardTitle>{{ t('app.moveout.deductions.title') }}</CardTitle>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="openAddDialog"
                >
                    + {{ t('app.moveout.deductions.add') }}
                </Button>
            </CardHeader>
            <CardContent>
                <div v-if="deductions.length === 0" class="py-8 text-center text-sm text-muted-foreground">
                    {{ t('app.common.noResults') }}
                </div>
                <table v-else class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="pb-2 pe-4">#</th>
                            <th class="pb-2 pe-4">{{ t('app.moveout.deductions.labelEn') }}</th>
                            <th class="pb-2 pe-4">{{ t('app.moveout.deductions.labelAr') }}</th>
                            <th class="pb-2 pe-4">{{ t('app.moveout.deductions.amount') }}</th>
                            <th class="pb-2 pe-4">{{ t('app.moveout.deductions.reason') }}</th>
                            <th class="pb-2" />
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(deduction, index) in deductions"
                            :key="index"
                            class="border-b last:border-0"
                        >
                            <td class="py-2 pe-4 text-muted-foreground">{{ index + 1 }}</td>
                            <td class="py-2 pe-4">{{ deduction.label_en }}</td>
                            <td class="py-2 pe-4" dir="rtl">{{ deduction.label_ar }}</td>
                            <td class="py-2 pe-4">{{ Number(deduction.amount).toLocaleString() }}</td>
                            <td class="py-2 pe-4 capitalize">{{ deduction.reason }}</td>
                            <td class="py-2">
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="sm"
                                    class="text-destructive"
                                    @click="removeDeduction(index)"
                                >
                                    ×
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <InputError v-if="errors['deductions']" :message="errors['deductions']" />
            </CardContent>
        </Card>

        <!-- Calculation summary -->
        <Card>
            <CardContent class="pt-6">
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt>{{ t('app.moveout.deductions.securityDepositLabel') }}</dt>
                        <dd class="font-medium">{{ Number(securityDeposit).toLocaleString() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>{{ t('app.moveout.deductions.totalDeductions') }}</dt>
                        <dd class="font-medium text-destructive">− {{ Number(totalDeductions).toLocaleString() }}</dd>
                    </div>
                    <hr />
                    <div class="flex justify-between font-medium">
                        <dt v-if="refundAmount >= 0">{{ t('app.moveout.deductions.refundAmount') }}</dt>
                        <dt v-else class="text-destructive">{{ t('app.moveout.deductions.outstandingCharge') }}</dt>
                        <dd :class="refundAmount < 0 ? 'text-destructive' : ''">
                            {{ Number(Math.abs(refundAmount)).toLocaleString() }}
                        </dd>
                    </div>
                </dl>
            </CardContent>
        </Card>

        <!-- Footer actions -->
        <div class="flex items-center justify-end gap-3">
            <Button
                type="button"
                :disabled="processing"
                @click="saveDeductions"
            >
                {{ exceedsDeposit && !exceedWarningAcknowledged
                    ? t('app.common.save')
                    : t('app.moveout.deductions.save') }}
            </Button>
        </div>

        <!-- Add Deduction Dialog -->
        <Dialog v-model:open="showAddDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ t('app.moveout.deductions.add') }}</DialogTitle>
                    <DialogDescription>{{ t('app.moveout.deductions.title') }}</DialogDescription>
                </DialogHeader>

                <div class="space-y-4">
                    <!-- Label EN -->
                    <div class="space-y-2">
                        <Label for="label_en">{{ t('app.moveout.deductions.labelEn') }} *</Label>
                        <Input
                            id="label_en"
                            v-model="newDeduction.label_en"
                        />
                    </div>

                    <!-- Label AR -->
                    <div class="space-y-2">
                        <Label for="label_ar">{{ t('app.moveout.deductions.labelAr') }} *</Label>
                        <Input
                            id="label_ar"
                            v-model="newDeduction.label_ar"
                            dir="rtl"
                        />
                    </div>

                    <!-- Amount -->
                    <div class="space-y-2">
                        <Label for="deduction_amount">{{ t('app.moveout.deductions.amount') }} *</Label>
                        <Input
                            id="deduction_amount"
                            v-model="newDeduction.amount"
                            type="number"
                            min="0"
                            step="0.01"
                        />
                    </div>

                    <!-- Reason -->
                    <div class="space-y-2">
                        <Label for="deduction_reason">{{ t('app.moveout.deductions.reason') }} *</Label>
                        <Select v-model="newDeduction.reason">
                            <SelectTrigger id="deduction_reason">
                                <SelectValue :placeholder="t('app.moveout.deductions.reason')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="opt in reasons"
                                    :key="opt.value"
                                    :value="opt.value"
                                >
                                    {{ opt.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        @click="showAddDialog = false"
                    >
                        {{ t('app.common.cancel') }}
                    </Button>
                    <Button
                        type="button"
                        :disabled="!newDeduction.label_en || !newDeduction.label_ar || !newDeduction.amount || !newDeduction.reason"
                        @click="addDeduction"
                    >
                        {{ t('app.moveout.deductions.add') }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
