<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import type { Lease, Unit, Status, Resident, Owner } from '@/types';

const props = defineProps<{
    leases: Pick<Lease, 'id' | 'contract_number'>[];
    units: Pick<Unit, 'id' | 'name'>[];
    statuses: Pick<Status, 'id' | 'name' | 'name_en'>[];
    tenants: Pick<Resident, 'id' | 'first_name' | 'last_name'>[];
    owners: Pick<Owner, 'id' | 'first_name' | 'last_name'>[];
    transactionCategories: { id: number; name: string; name_en: string | null }[];
    transactionTypes: { id: number; name: string; name_en: string | null }[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Transactions', href: '/transactions' },
            { title: 'New Transaction', href: '/transactions/create' },
        ],
    },
});

const form = useForm({
    lease_id: '',
    unit_id: '',
    category_id: '1',
    type_id: '1',
    status_id: '',
    assignee_id: '',
    amount: '',
    tax_amount: '0',
    due_date: '',
    notes: '',
});

function submit() {
    form.post('/transactions');
}
</script>

<template>
    <Head title="New Transaction" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" title="Create Transaction" description="Record a new financial transaction." />

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label>Lease</Label>
                    <Select v-model="form.lease_id">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select lease (optional)" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="lease in leases" :key="lease.id" :value="String(lease.id)">
                                {{ lease.contract_number }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.lease_id" />
                </div>

                <div class="grid gap-2">
                    <Label>Unit</Label>
                    <Select v-model="form.unit_id">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select unit (optional)" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="unit in units" :key="unit.id" :value="String(unit.id)">
                                {{ unit.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.unit_id" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label>Assignee (Tenant/Owner)</Label>
                    <Select v-model="form.assignee_id">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select assignee" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem disabled value="__tenants__">— Tenants —</SelectItem>
                            <SelectItem v-for="tenant in tenants" :key="`t-${tenant.id}`" :value="String(tenant.id)">
                                {{ tenant.first_name }} {{ tenant.last_name }}
                            </SelectItem>
                            <SelectItem disabled value="__owners__">— Owners —</SelectItem>
                            <SelectItem v-for="owner in owners" :key="`o-${owner.id}`" :value="String(owner.id)">
                                {{ owner.first_name }} {{ owner.last_name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.assignee_id" />
                </div>

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
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label>Category</Label>
                    <Select v-model="form.category_id">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select category" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="cat in transactionCategories" :key="cat.id" :value="String(cat.id)">
                                {{ cat.name_en ?? cat.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.category_id" />
                </div>

                <div class="grid gap-2">
                    <Label>Type</Label>
                    <Select v-model="form.type_id">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="type in transactionTypes" :key="type.id" :value="String(type.id)">
                                {{ type.name_en ?? type.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.type_id" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="grid gap-2">
                    <Label for="amount">Amount</Label>
                    <Input id="amount" v-model="form.amount" type="number" step="0.01" min="0" required />
                    <InputError :message="form.errors.amount" />
                </div>

                <div class="grid gap-2">
                    <Label for="tax_amount">Tax Amount</Label>
                    <Input id="tax_amount" v-model="form.tax_amount" type="number" step="0.01" min="0" />
                    <InputError :message="form.errors.tax_amount" />
                </div>

                <div class="grid gap-2">
                    <Label for="due_date">Due Date</Label>
                    <Input id="due_date" v-model="form.due_date" type="date" required />
                    <InputError :message="form.errors.due_date" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="notes">Notes</Label>
                <Textarea id="notes" v-model="form.notes" placeholder="Additional notes..." />
                <InputError :message="form.errors.notes" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">Create Transaction</Button>
            </div>
        </form>
    </div>
</template>
