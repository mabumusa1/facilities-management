<script setup lang="ts">
import { Head, Link, setLayoutProps, useForm } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { useI18n } from '@/composables/useI18n';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { Lease, Resident, Status } from '@/types';

const props = defineProps<{
    parentLease: Lease;
    statuses: Pick<Status, 'id' | 'name' | 'name_en'>[];
    tenants: Pick<Resident, 'id' | 'first_name' | 'last_name'>[];
}>();

const { t } = useI18n();

const form = useForm({
    contract_number: '',
    tenant_id: String(props.parentLease.tenant_id),
    status_id: String(props.parentLease.status_id),
    start_date: props.parentLease.start_date,
    end_date: props.parentLease.end_date,
    handover_date: props.parentLease.handover_date,
    rental_total_amount: props.parentLease.rental_total_amount,
    security_deposit_amount: props.parentLease.security_deposit_amount ?? '',
    legal_representative: props.parentLease.legal_representative ?? '',
    fit_out_status: props.parentLease.fit_out_status ?? '',
    terms_conditions: props.parentLease.terms_conditions ?? '',
});

function submit() {
    form.post(`/leases/${props.parentLease.id}/subleases`);
}

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.leases.pageTitle'), href: '/leases' },
            { title: t('app.leases.sublease.pageTitle'), href: '#' },
        ],
    });
});
</script>

<template>
    <Head :title="t('app.leases.sublease.pageTitleWithContract', { contract: parentLease.contract_number })" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-3">
            <Heading
                variant="small"
                :title="t('app.leases.sublease.pageTitleWithContract', { contract: parentLease.contract_number })"
                :description="t('app.leases.sublease.description')"
            />
            <Button variant="outline" as-child>
                <Link :href="`/leases/${parentLease.id}`">{{ t('app.actions.back') }}</Link>
            </Button>
        </div>

        <form @submit.prevent="submit" class="max-w-3xl space-y-6 rounded-lg border p-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="contract_number">{{ t('app.leases.sublease.contractNumber') }}</Label>
                    <Input
                        id="contract_number"
                        v-model="form.contract_number"
                        required
                        :placeholder="t('app.leases.sublease.contractPlaceholder')"
                    />
                    <InputError :message="form.errors.contract_number" />
                </div>

                <div class="grid gap-2">
                    <Label>{{ t('app.leases.sublease.tenant') }}</Label>
                    <Select v-model="form.tenant_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.leases.sublease.selectTenant')" />
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

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label>{{ t('app.leases.sublease.status') }}</Label>
                    <Select v-model="form.status_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.leases.sublease.selectStatus')" />
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
                    <Label for="rental_total_amount">{{ t('app.leases.sublease.totalAmount') }}</Label>
                    <Input id="rental_total_amount" v-model="form.rental_total_amount" type="number" step="0.01" min="0" required />
                    <InputError :message="form.errors.rental_total_amount" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="grid gap-2">
                    <Label for="start_date">{{ t('app.leases.sublease.startDate') }}</Label>
                    <Input id="start_date" v-model="form.start_date" type="date" required />
                    <InputError :message="form.errors.start_date" />
                </div>
                <div class="grid gap-2">
                    <Label for="end_date">{{ t('app.leases.sublease.endDate') }}</Label>
                    <Input id="end_date" v-model="form.end_date" type="date" required />
                    <InputError :message="form.errors.end_date" />
                </div>
                <div class="grid gap-2">
                    <Label for="handover_date">{{ t('app.leases.sublease.handoverDate') }}</Label>
                    <Input id="handover_date" v-model="form.handover_date" type="date" required />
                    <InputError :message="form.errors.handover_date" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="security_deposit_amount">{{ t('app.leases.sublease.securityDeposit') }}</Label>
                    <Input id="security_deposit_amount" v-model="form.security_deposit_amount" type="number" step="0.01" min="0" />
                    <InputError :message="form.errors.security_deposit_amount" />
                </div>
                <div class="grid gap-2">
                    <Label for="legal_representative">{{ t('app.leases.sublease.legalRepresentative') }}</Label>
                    <Input id="legal_representative" v-model="form.legal_representative" />
                    <InputError :message="form.errors.legal_representative" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="fit_out_status">{{ t('app.leases.sublease.fitOutStatus') }}</Label>
                <Input id="fit_out_status" v-model="form.fit_out_status" />
                <InputError :message="form.errors.fit_out_status" />
            </div>

            <div class="grid gap-2">
                <Label for="terms_conditions">{{ t('app.leases.sublease.termsAndConditions') }}</Label>
                <textarea
                    id="terms_conditions"
                    v-model="form.terms_conditions"
                    class="min-h-24 rounded-md border border-input bg-background px-3 py-2 text-sm"
                    :placeholder="t('app.leases.sublease.termsPlaceholder')"
                />
                <InputError :message="form.errors.terms_conditions" />
            </div>

            <div class="flex items-center gap-2">
                <Button :disabled="form.processing">{{ t('app.leases.sublease.createButton') }}</Button>
            </div>
        </form>
    </div>
</template>
