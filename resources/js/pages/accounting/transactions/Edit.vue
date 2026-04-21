<script setup lang="ts">
import { Head, setLayoutProps, useForm } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { useI18n } from '@/composables/useI18n';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import type { Transaction, Lease, Unit, Status, Resident, Owner } from '@/types';

const props = defineProps<{
    transaction: Transaction;
    leases: Pick<Lease, 'id' | 'contract_number'>[];
    units: Pick<Unit, 'id' | 'name'>[];
    statuses: Pick<Status, 'id' | 'name' | 'name_en'>[];
    tenants: Pick<Resident, 'id' | 'first_name' | 'last_name'>[];
    owners: Pick<Owner, 'id' | 'first_name' | 'last_name'>[];
    transactionCategories: { id: number; name: string; name_en: string | null }[];
    transactionTypes: { id: number; name: string; name_en: string | null }[];
}>();

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.transactions.pageTitle'), href: '/transactions' },
            { title: t('app.transactions.edit.breadcrumb'), href: '#' },
        ],
    });
});

const form = useForm({
    status_id: String(props.transaction.status_id),
    amount: props.transaction.amount,
    tax_amount: props.transaction.tax_amount ?? '',
    due_date: props.transaction.due_date,
    notes: props.transaction.notes ?? '',
});

function submit() {
    form.put(`/transactions/${props.transaction.id}`);
}
</script>

<template>
    <Head :title="t('app.transactions.edit.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            variant="small"
            :title="t('app.transactions.edit.heading')"
            :description="t('app.transactions.edit.description')"
        />

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label>{{ t('app.transactions.status') }}</Label>
                    <Select v-model="form.status_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.transactions.edit.selectStatus')" />
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
                    <Label for="due_date">{{ t('app.transactions.table.dueDate') }}</Label>
                    <Input id="due_date" v-model="form.due_date" type="date" />
                    <InputError :message="form.errors.due_date" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="amount">{{ t('app.transactions.table.amount') }}</Label>
                    <Input id="amount" v-model="form.amount" type="number" step="0.01" required />
                    <InputError :message="form.errors.amount" />
                </div>

                <div class="grid gap-2">
                    <Label for="tax_amount">{{ t('app.transactions.show.tax') }}</Label>
                    <Input id="tax_amount" v-model="form.tax_amount" type="number" step="0.01" />
                    <InputError :message="form.errors.tax_amount" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="notes">{{ t('app.transactions.edit.notes') }}</Label>
                <Textarea id="notes" v-model="form.notes" :placeholder="t('app.transactions.edit.notesPlaceholder')" />
                <InputError :message="form.errors.notes" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">{{ t('app.transactions.edit.updateButton') }}</Button>
            </div>
        </form>
    </div>
</template>
