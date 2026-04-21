<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import type { Resident, Status, UnitCategory, Unit } from '@/types';

const props = defineProps<{
    tenants: Pick<Resident, 'id' | 'first_name' | 'last_name'>[];
    statuses: Pick<Status, 'id' | 'name' | 'name_en'>[];
    unitCategories: Pick<UnitCategory, 'id' | 'name' | 'name_en'>[];
    rentalContractTypes: { id: number; name: string; name_en: string | null }[];
    paymentSchedules: { id: number; name: string; name_en: string | null; parent_id: number | null }[];
    units: Pick<Unit, 'id' | 'name'>[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Leases', href: '/leases' },
            { title: 'New Lease', href: '/leases/create' },
        ],
    },
});

const form = useForm({
    contract_number: '',
    tenant_id: '',
    status_id: '30',
    lease_unit_type_id: '',
    rental_contract_type_id: '',
    payment_schedule_id: '',
    start_date: '',
    end_date: '',
    handover_date: '',
    tenant_type: 'individual',
    rental_type: 'total',
    rental_total_amount: '',
    security_deposit_amount: '',
    terms_conditions: '',
});

function submit() {
    form.post('/leases');
}
</script>

<template>
    <Head title="New Lease" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" title="Create Lease" description="Create a new rental agreement." />

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="contract_number">Contract Number</Label>
                    <Input id="contract_number" v-model="form.contract_number" required placeholder="e.g. LC-2024-001" />
                    <InputError :message="form.errors.contract_number" />
                </div>

                <div class="grid gap-2">
                    <Label>Tenant</Label>
                    <Select v-model="form.tenant_id">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select tenant" />
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
                    <Label>Status</Label>
                    <Select v-model="form.status_id">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select status" />
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
                    <Label>Tenant Type</Label>
                    <Select v-model="form.tenant_type">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="individual">Individual</SelectItem>
                            <SelectItem value="company">Company</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.tenant_type" />
                </div>

                <div class="grid gap-2">
                    <Label>Rental Type</Label>
                    <Select v-model="form.rental_type">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="total">Total</SelectItem>
                            <SelectItem value="detailed">Detailed</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.rental_type" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="grid gap-2">
                    <Label>Unit Category</Label>
                    <Select v-model="form.lease_unit_type_id">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select category" />
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
                    <Label>Contract Type</Label>
                    <Select v-model="form.rental_contract_type_id">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select contract type" />
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
                    <Label>Payment Schedule</Label>
                    <Select v-model="form.payment_schedule_id">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select schedule" />
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
                    <Label for="start_date">Start Date</Label>
                    <Input id="start_date" v-model="form.start_date" type="date" required />
                    <InputError :message="form.errors.start_date" />
                </div>

                <div class="grid gap-2">
                    <Label for="end_date">End Date</Label>
                    <Input id="end_date" v-model="form.end_date" type="date" required />
                    <InputError :message="form.errors.end_date" />
                </div>

                <div class="grid gap-2">
                    <Label for="handover_date">Handover Date</Label>
                    <Input id="handover_date" v-model="form.handover_date" type="date" required />
                    <InputError :message="form.errors.handover_date" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="rental_total_amount">Total Amount</Label>
                    <Input id="rental_total_amount" v-model="form.rental_total_amount" type="number" step="0.01" min="0" required placeholder="0.00" />
                    <InputError :message="form.errors.rental_total_amount" />
                </div>

                <div class="grid gap-2">
                    <Label for="security_deposit_amount">Security Deposit</Label>
                    <Input id="security_deposit_amount" v-model="form.security_deposit_amount" type="number" step="0.01" min="0" placeholder="0.00" />
                    <InputError :message="form.errors.security_deposit_amount" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="terms_conditions">Terms & Conditions</Label>
                <Textarea id="terms_conditions" v-model="form.terms_conditions" placeholder="Enter lease terms..." />
                <InputError :message="form.errors.terms_conditions" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">Create Lease</Button>
            </div>
        </form>
    </div>
</template>
