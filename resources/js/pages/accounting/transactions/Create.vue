<script setup lang="ts">
import { Head, setLayoutProps, useForm } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import type { Lease, Unit, Status, Resident, Owner } from '@/types';

const { t } = useI18n();

const props = defineProps<{
    leases: Pick<Lease, 'id' | 'contract_number'>[];
    units: Pick<Unit, 'id' | 'name'>[];
    statuses: Pick<Status, 'id' | 'name' | 'name_en'>[];
    tenants: Pick<Resident, 'id' | 'first_name' | 'last_name'>[];
    owners: Pick<Owner, 'id' | 'first_name' | 'last_name'>[];
    transactionCategories: { id: number; name: string; name_en: string | null }[];
    transactionTypes: { id: number; name: string; name_en: string | null }[];
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.transactions'), href: '/transactions' },
            { title: t('app.transactions.create.pageTitle'), href: '/transactions/create' },
        ],
    });
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
    <Head :title="t('app.transactions.create.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="t('app.transactions.create.heading')" :description="t('app.transactions.create.description')" />

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label>{{ t('app.transactions.table.lease') }}</Label>
                    <Select v-model="form.lease_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.transactions.create.selectLease')" />
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
                    <Label>{{ t('app.transactions.table.unit') }}</Label>
                    <Select v-model="form.unit_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.transactions.create.selectUnit')" />
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
                    <Label>{{ t('app.transactions.create.assignee') }}</Label>
                    <Select v-model="form.assignee_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.transactions.create.selectAssignee')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem disabled value="__tenants__">{{ t('app.transactions.create.tenantsGroup') }}</SelectItem>
                            <SelectItem v-for="tenant in tenants" :key="`t-${tenant.id}`" :value="String(tenant.id)">
                                {{ tenant.first_name }} {{ tenant.last_name }}
                            </SelectItem>
                            <SelectItem disabled value="__owners__">{{ t('app.transactions.create.ownersGroup') }}</SelectItem>
                            <SelectItem v-for="owner in owners" :key="`o-${owner.id}`" :value="String(owner.id)">
                                {{ owner.first_name }} {{ owner.last_name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.assignee_id" />
                </div>

                <div class="grid gap-2">
                    <Label>{{ t('app.transactions.table.status') }}</Label>
                    <Select v-model="form.status_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.transactions.create.selectStatus')" />
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
                    <Label>{{ t('app.transactions.table.category') }}</Label>
                    <Select v-model="form.category_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.transactions.create.selectCategory')" />
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
                    <Label>{{ t('app.transactions.table.type') }}</Label>
                    <Select v-model="form.type_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.transactions.create.selectType')" />
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
                    <Label for="amount">{{ t('app.transactions.table.amount') }}</Label>
                    <Input id="amount" v-model="form.amount" type="number" step="0.01" min="0" required />
                    <InputError :message="form.errors.amount" />
                </div>

                <div class="grid gap-2">
                    <Label for="tax_amount">{{ t('app.transactions.create.taxAmount') }}</Label>
                    <Input id="tax_amount" v-model="form.tax_amount" type="number" step="0.01" min="0" />
                    <InputError :message="form.errors.tax_amount" />
                </div>

                <div class="grid gap-2">
                    <Label for="due_date">{{ t('app.transactions.table.dueDate') }}</Label>
                    <Input id="due_date" v-model="form.due_date" type="date" required />
                    <InputError :message="form.errors.due_date" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="notes">{{ t('app.transactions.edit.notes') }}</Label>
                <Textarea id="notes" v-model="form.notes" :placeholder="t('app.transactions.edit.notesPlaceholder')" />
                <InputError :message="form.errors.notes" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">{{ t('app.transactions.create.createButton') }}</Button>
            </div>
        </form>
    </div>
</template>
