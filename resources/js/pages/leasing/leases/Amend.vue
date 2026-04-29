<script setup lang="ts">
import { Head, Link, useForm, setLayoutProps } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import { storeAmendment as storeAmendmentAction } from '@/actions/App/Http/Controllers/Leasing/LeaseController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { useI18n } from '@/composables/useI18n';
import type { Lease, Unit } from '@/types';

const props = defineProps<{
    lease: Lease;
    units: Pick<Unit, 'id' | 'name'>[];
    rentalContractTypes: { id: number; name: string; name_ar: string | null; name_en: string | null }[];
    paymentSchedules: { id: number; name: string; name_ar: string | null; name_en: string | null; parent_id: number | null }[];
}>();

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.leases.pageTitle'), href: '/leases' },
            { title: props.lease.contract_number, href: `/leases/${props.lease.id}` },
            { title: t('app.leases.amend.breadcrumb'), href: '#' },
        ],
    });
});

// ── Form ───────────────────────────────────────────────────────────────────────
const currentUnits = computed(() =>
    (props.lease.units ?? []).map((u) => ({
        id: u.id,
        name: u.name,
        rental_amount: u.pivot?.annual_rental_amount ?? '',
    })),
);

const form = useForm({
    end_date: props.lease.end_date ?? '',
    rental_total_amount: props.lease.rental_total_amount ?? '',
    rental_contract_type_id: props.lease.rental_contract_type_id
        ? String(props.lease.rental_contract_type_id)
        : '',
    payment_schedule_id: props.lease.payment_schedule_id
        ? String(props.lease.payment_schedule_id)
        : '',
    security_deposit_amount: props.lease.security_deposit_amount ?? '',
    terms_conditions: props.lease.terms_conditions ?? '',
    units: currentUnits.value.map((u) => ({ id: u.id, rental_amount: u.rental_amount })),
    reason: '',
    generate_addendum: false,
});

function submit() {
    form.post(storeAmendmentAction.url(props.lease.id));
}

// ── Diff preview ───────────────────────────────────────────────────────────────
type DiffRow = {
    field: string;
    current: string;
    next: string;
    changed: boolean;
};

const diffRows = computed<DiffRow[]>(() => {
    const rows: DiffRow[] = [];

    const currentEndDate = props.lease.end_date ?? '—';
    const newEndDate = form.end_date;
    rows.push({
        field: t('app.leases.create.endDate'),
        current: currentEndDate,
        next: newEndDate || currentEndDate,
        changed: newEndDate !== '' && newEndDate !== currentEndDate,
    });

    const currentAmount = String(props.lease.rental_total_amount ?? '');
    const newAmount = String(form.rental_total_amount);
    rows.push({
        field: t('app.leases.create.totalAmount'),
        current: currentAmount,
        next: newAmount || currentAmount,
        changed: newAmount !== '' && newAmount !== currentAmount,
    });

    const currentContractType =
        props.rentalContractTypes.find((r) => r.id === props.lease.rental_contract_type_id)?.name_en ??
        props.rentalContractTypes.find((r) => r.id === props.lease.rental_contract_type_id)?.name ??
        '—';
    const newContractTypeId = form.rental_contract_type_id ? Number(form.rental_contract_type_id) : null;
    const newContractType =
        (newContractTypeId
            ? (props.rentalContractTypes.find((r) => r.id === newContractTypeId)?.name_en ??
               props.rentalContractTypes.find((r) => r.id === newContractTypeId)?.name)
            : null) ?? currentContractType;
    rows.push({
        field: t('app.leases.create.contractType'),
        current: currentContractType,
        next: newContractType,
        changed:
            newContractTypeId !== null &&
            newContractTypeId !== props.lease.rental_contract_type_id,
    });

    const currentSchedule =
        props.paymentSchedules.find((p) => p.id === props.lease.payment_schedule_id)?.name_en ??
        props.paymentSchedules.find((p) => p.id === props.lease.payment_schedule_id)?.name ??
        '—';
    const newScheduleId = form.payment_schedule_id ? Number(form.payment_schedule_id) : null;
    const newSchedule =
        (newScheduleId
            ? (props.paymentSchedules.find((p) => p.id === newScheduleId)?.name_en ??
               props.paymentSchedules.find((p) => p.id === newScheduleId)?.name)
            : null) ?? currentSchedule;
    rows.push({
        field: t('app.leases.create.paymentSchedule'),
        current: currentSchedule,
        next: newSchedule,
        changed:
            newScheduleId !== null &&
            newScheduleId !== props.lease.payment_schedule_id,
    });

    return rows;
});
</script>

<template>
    <Head :title="t('app.leases.amend.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center gap-2">
            <Button variant="ghost" size="sm" as-child>
                <Link :href="`/leases/${lease.id}`">← {{ lease.contract_number }}</Link>
            </Button>
        </div>

        <Heading variant="small" :title="t('app.leases.amend.pageTitle')" :description="t('app.leases.amend.intro')" />

        <form class="max-w-2xl space-y-6" @submit.prevent="submit">
            <!-- Amendable fields -->
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="end_date">{{ t('app.leases.create.endDate') }}</Label>
                    <div class="text-muted-foreground mb-1 text-xs">
                        {{ t('app.leases.amend.currentLabel') }}: {{ lease.end_date ?? '—' }}
                    </div>
                    <Input
                        id="end_date"
                        v-model="form.end_date"
                        type="date"
                    />
                    <InputError :message="form.errors.end_date" />
                </div>

                <div class="grid gap-2">
                    <Label for="rental_total_amount">{{ t('app.leases.create.totalAmount') }}</Label>
                    <div class="text-muted-foreground mb-1 text-xs">
                        {{ t('app.leases.amend.currentLabel') }}: {{ lease.rental_total_amount ?? '—' }}
                    </div>
                    <Input
                        id="rental_total_amount"
                        v-model="form.rental_total_amount"
                        type="number"
                        step="0.01"
                        min="0"
                        :placeholder="t('app.leases.create.amountPlaceholder')"
                    />
                    <InputError :message="form.errors.rental_total_amount" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label>{{ t('app.leases.create.contractType') }}</Label>
                    <div class="text-muted-foreground mb-1 text-xs">
                        {{ t('app.leases.amend.currentLabel') }}:
                        {{
                            rentalContractTypes.find((r) => r.id === lease.rental_contract_type_id)?.name_en ??
                            rentalContractTypes.find((r) => r.id === lease.rental_contract_type_id)?.name ??
                            '—'
                        }}
                    </div>
                    <Select v-model="form.rental_contract_type_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.leases.create.selectContractType')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="type in rentalContractTypes"
                                :key="type.id"
                                :value="String(type.id)"
                            >
                                {{ type.name_en ?? type.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.rental_contract_type_id" />
                </div>

                <div class="grid gap-2">
                    <Label>{{ t('app.leases.create.paymentSchedule') }}</Label>
                    <div class="text-muted-foreground mb-1 text-xs">
                        {{ t('app.leases.amend.currentLabel') }}:
                        {{
                            paymentSchedules.find((p) => p.id === lease.payment_schedule_id)?.name_en ??
                            paymentSchedules.find((p) => p.id === lease.payment_schedule_id)?.name ??
                            '—'
                        }}
                    </div>
                    <Select v-model="form.payment_schedule_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.leases.create.selectSchedule')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="schedule in paymentSchedules"
                                :key="schedule.id"
                                :value="String(schedule.id)"
                            >
                                {{ schedule.name_en ?? schedule.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.payment_schedule_id" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="security_deposit_amount">{{ t('app.leases.create.securityDeposit') }}</Label>
                <div class="text-muted-foreground mb-1 text-xs">
                    {{ t('app.leases.amend.currentLabel') }}: {{ lease.security_deposit_amount ?? '—' }}
                </div>
                <Input
                    id="security_deposit_amount"
                    v-model="form.security_deposit_amount"
                    type="number"
                    step="0.01"
                    min="0"
                    :placeholder="t('app.leases.create.amountPlaceholder')"
                />
                <InputError :message="form.errors.security_deposit_amount" />
            </div>

            <div class="grid gap-2">
                <Label for="terms_conditions">{{ t('app.leases.create.termsAndConditions') }}</Label>
                <Textarea
                    id="terms_conditions"
                    v-model="form.terms_conditions"
                    :placeholder="t('app.leases.create.termsPlaceholder')"
                />
                <InputError :message="form.errors.terms_conditions" />
            </div>

            <!-- Diff preview -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-base">{{ t('app.leases.amend.diffSection') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <table class="w-full text-sm" aria-label="Amendment diff preview">
                        <thead>
                            <tr class="text-muted-foreground border-b text-xs">
                                <th class="pb-2 text-start font-medium">{{ t('app.common.field') }}</th>
                                <th class="pb-2 text-start font-medium">{{ t('app.leases.amend.currentLabel') }}</th>
                                <th class="pb-2 text-start font-medium">{{ t('app.leases.amend.newLabel') }}</th>
                                <th class="pb-2 text-start font-medium"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="row in diffRows"
                                :key="row.field"
                                class="border-b last:border-0"
                                :aria-label="`${row.field} ${row.changed ? 'changed from ' + row.current + ' to ' + row.next : 'unchanged'}`"
                            >
                                <td class="py-1.5 font-medium">{{ row.field }}</td>
                                <td class="text-muted-foreground py-1.5">{{ row.current }}</td>
                                <td class="py-1.5">{{ row.next }}</td>
                                <td class="py-1.5">
                                    <span
                                        v-if="row.changed"
                                        class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-900/30 dark:text-amber-300"
                                    >
                                        {{ t('app.leases.amend.changed') }}
                                    </span>
                                    <span
                                        v-else
                                        class="text-muted-foreground text-xs"
                                    >
                                        {{ t('app.leases.amend.unchanged') }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </CardContent>
            </Card>

            <!-- Reason -->
            <div class="grid gap-2">
                <Label for="reason">{{ t('app.leases.amend.reason') }} *</Label>
                <Textarea
                    id="reason"
                    v-model="form.reason"
                    :placeholder="t('app.leases.amend.reasonPlaceholder')"
                    aria-required="true"
                    rows="4"
                />
                <InputError :message="form.errors.reason" />
            </div>

            <!-- Addendum checkbox -->
            <div class="flex items-start gap-3">
                <input
                    id="generate_addendum"
                    v-model="form.generate_addendum"
                    type="checkbox"
                    class="mt-0.5 h-4 w-4 rounded border-gray-300"
                    aria-describedby="addendum-desc"
                />
                <div>
                    <Label for="generate_addendum" class="font-normal">
                        {{ t('app.leases.amend.generateAddendum') }}
                    </Label>
                    <p id="addendum-desc" class="text-muted-foreground text-xs">
                        {{ t('app.leases.amend.intro') }}
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-4">
                <Button type="submit" :disabled="form.processing">
                    {{ t('app.leases.amend.save') }}
                </Button>
                <Button variant="outline" type="button" as-child>
                    <Link :href="`/leases/${lease.id}`">{{ t('app.actions.cancel') }}</Link>
                </Button>
            </div>
        </form>
    </div>
</template>
