<script setup lang="ts">
import { Head, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { useI18n } from '@/composables/useI18n';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import type { Lease, Resident, Status, UnitCategory, Unit } from '@/types';

const props = defineProps<{
    lease: Lease;
    tenants: Pick<Resident, 'id' | 'first_name' | 'last_name'>[];
    statuses: Pick<Status, 'id' | 'name' | 'name_en'>[];
    unitCategories: Pick<UnitCategory, 'id' | 'name' | 'name_en'>[];
    rentalContractTypes: { id: number; name: string; name_en: string | null }[];
    paymentSchedules: { id: number; name: string; name_en: string | null; parent_id: number | null }[];
    units: Pick<Unit, 'id' | 'name'>[];
}>();

const { t } = useI18n();

const leaseTitle = computed(() => t('app.leases.edit.pageTitleWithContract', { contract: props.lease.contract_number }));

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.leases.pageTitle'), href: '/leases' },
            { title: t('app.leases.edit.breadcrumb'), href: '#' },
        ],
    });
});

const form = useForm({
    contract_number: props.lease.contract_number,
    tenant_id: String(props.lease.tenant_id),
    status_id: String(props.lease.status_id),
    lease_unit_type_id: String(props.lease.lease_unit_type_id),
    rental_contract_type_id: String(props.lease.rental_contract_type_id),
    payment_schedule_id: String(props.lease.payment_schedule_id),
    start_date: props.lease.start_date,
    end_date: props.lease.end_date,
    handover_date: props.lease.handover_date,
    tenant_type: props.lease.tenant_type,
    rental_type: props.lease.rental_type,
    rental_total_amount: props.lease.rental_total_amount,
    security_deposit_amount: props.lease.security_deposit_amount ?? '',
    terms_conditions: props.lease.terms_conditions ?? '',
});

function submit() {
    form.put(`/leases/${props.lease.id}`);
}
</script>

<template>
    <Head :title="leaseTitle" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="leaseTitle" :description="t('app.leases.edit.description')" />

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="contract_number">{{ t('app.leases.create.contractNumber') }}</Label>
                    <Input id="contract_number" v-model="form.contract_number" required />
                    <InputError :message="form.errors.contract_number" />
                </div>

                <div class="grid gap-2">
                    <Label>{{ t('app.leases.create.tenant') }}</Label>
                    <Select v-model="form.tenant_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.leases.create.selectTenant')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="tenant in tenants" :key="tenant.id" :value="String(tenant.id)">
                                {{ tenant.first_name }} {{ tenant.last_name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.tenant_id" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="grid gap-2">
                    <Label>{{ t('app.leases.create.status') }}</Label>
                    <Select v-model="form.status_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.leases.create.selectStatus')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="status in statuses" :key="status.id" :value="String(status.id)">
                                {{ status.name_en ?? status.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.status_id" />
                </div>

                <div class="grid gap-2">
                    <Label>{{ t('app.leases.create.tenantType') }}</Label>
                    <Select v-model="form.tenant_type">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.leases.create.selectType')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="individual">{{ t('app.leases.create.individual') }}</SelectItem>
                            <SelectItem value="company">{{ t('app.leases.create.company') }}</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.tenant_type" />
                </div>

                <div class="grid gap-2">
                    <Label>{{ t('app.leases.create.rentalType') }}</Label>
                    <Select v-model="form.rental_type">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.leases.create.selectType')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="total">{{ t('app.leases.create.total') }}</SelectItem>
                            <SelectItem value="detailed">{{ t('app.leases.create.detailed') }}</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.rental_type" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="grid gap-2">
                    <Label>{{ t('app.leases.create.unitCategory') }}</Label>
                    <Select v-model="form.lease_unit_type_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.leases.create.selectCategory')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="cat in unitCategories" :key="cat.id" :value="String(cat.id)">
                                {{ cat.name_en ?? cat.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.lease_unit_type_id" />
                </div>

                <div class="grid gap-2">
                    <Label>{{ t('app.leases.create.contractType') }}</Label>
                    <Select v-model="form.rental_contract_type_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.leases.create.selectContractType')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="type in rentalContractTypes" :key="type.id" :value="String(type.id)">
                                {{ type.name_en ?? type.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.rental_contract_type_id" />
                </div>

                <div class="grid gap-2">
                    <Label>{{ t('app.leases.create.paymentSchedule') }}</Label>
                    <Select v-model="form.payment_schedule_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.leases.create.selectSchedule')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="schedule in paymentSchedules" :key="schedule.id" :value="String(schedule.id)">
                                {{ schedule.name_en ?? schedule.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.payment_schedule_id" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="grid gap-2">
                    <Label for="start_date">{{ t('app.leases.create.startDate') }}</Label>
                    <Input id="start_date" v-model="form.start_date" type="date" required />
                    <InputError :message="form.errors.start_date" />
                </div>

                <div class="grid gap-2">
                    <Label for="end_date">{{ t('app.leases.create.endDate') }}</Label>
                    <Input id="end_date" v-model="form.end_date" type="date" required />
                    <InputError :message="form.errors.end_date" />
                </div>

                <div class="grid gap-2">
                    <Label for="handover_date">{{ t('app.leases.create.handoverDate') }}</Label>
                    <Input id="handover_date" v-model="form.handover_date" type="date" required />
                    <InputError :message="form.errors.handover_date" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="rental_total_amount">{{ t('app.leases.create.totalAmount') }}</Label>
                    <Input
                        id="rental_total_amount"
                        v-model="form.rental_total_amount"
                        type="number"
                        step="0.01"
                        min="0"
                        required
                        :placeholder="t('app.leases.create.amountPlaceholder')"
                    />
                    <InputError :message="form.errors.rental_total_amount" />
                </div>

                <div class="grid gap-2">
                    <Label for="security_deposit_amount">{{ t('app.leases.create.securityDeposit') }}</Label>
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
            </div>

            <div class="grid gap-2">
                <Label for="terms_conditions">{{ t('app.leases.create.termsAndConditions') }}</Label>
                <Textarea id="terms_conditions" v-model="form.terms_conditions" :placeholder="t('app.leases.create.termsPlaceholder')" />
                <InputError :message="form.errors.terms_conditions" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">{{ t('app.leases.edit.updateButton') }}</Button>
            </div>
        </form>
    </div>
</template>
