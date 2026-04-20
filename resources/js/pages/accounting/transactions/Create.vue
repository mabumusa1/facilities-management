<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

defineOptions({ layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Transactions', href: '/transactions' }, { title: 'New Transaction', href: '/transactions/create' }] } });

const form = useForm({ lease_id: '', unit_id: '', category_id: '', type_id: '', status_id: '', assignee_id: '', amount: '', tax_amount: '0', due_date: '', notes: '' });

function submit() { form.post('/transactions'); }
</script>

<template>
    <Head title="New Transaction" />
    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" title="Create Transaction" description="Record a new financial transaction." />
        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2"><Label for="lease_id">Lease ID</Label><Input id="lease_id" v-model="form.lease_id" type="number" placeholder="Lease ID" /><InputError :message="form.errors.lease_id" /></div>
                <div class="grid gap-2"><Label for="unit_id">Unit ID</Label><Input id="unit_id" v-model="form.unit_id" type="number" placeholder="Unit ID" /><InputError :message="form.errors.unit_id" /></div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2"><Label for="category_id">Category ID</Label><Input id="category_id" v-model="form.category_id" type="number" required /><InputError :message="form.errors.category_id" /></div>
                <div class="grid gap-2"><Label for="type_id">Type ID</Label><Input id="type_id" v-model="form.type_id" type="number" required /><InputError :message="form.errors.type_id" /></div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2"><Label for="assignee_id">Assignee ID</Label><Input id="assignee_id" v-model="form.assignee_id" type="number" required placeholder="Tenant or Owner ID" /><InputError :message="form.errors.assignee_id" /></div>
                <div class="grid gap-2"><Label for="status_id">Status ID</Label><Input id="status_id" v-model="form.status_id" type="number" required /><InputError :message="form.errors.status_id" /></div>
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="grid gap-2"><Label for="amount">Amount</Label><Input id="amount" v-model="form.amount" type="number" step="0.01" min="0" required /><InputError :message="form.errors.amount" /></div>
                <div class="grid gap-2"><Label for="tax_amount">Tax Amount</Label><Input id="tax_amount" v-model="form.tax_amount" type="number" step="0.01" min="0" /><InputError :message="form.errors.tax_amount" /></div>
                <div class="grid gap-2"><Label for="due_date">Due Date</Label><Input id="due_date" v-model="form.due_date" type="date" required /><InputError :message="form.errors.due_date" /></div>
            </div>
            <div class="flex items-center gap-4"><Button :disabled="form.processing">Create Transaction</Button></div>
        </form>
    </div>
</template>
