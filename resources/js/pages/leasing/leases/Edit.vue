<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import type { Lease } from '@/types';

const props = defineProps<{
    lease: Lease;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Leases', href: '/leases' },
            { title: 'Edit', href: '#' },
        ],
    },
});

const form = useForm({
    contract_number: props.lease.contract_number,
    start_date: props.lease.start_date,
    end_date: props.lease.end_date,
    rental_total_amount: props.lease.rental_total_amount,
    security_deposit_amount: props.lease.security_deposit_amount ?? '',
    terms_conditions: props.lease.terms_conditions ?? '',
});

function submit() {
    form.put(`/leases/${props.lease.id}`);
}
</script>

<template>
    <Head :title="`Edit Lease ${lease.contract_number}`" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="`Edit Lease ${lease.contract_number}`" description="Update lease details." />

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-2">
                <Label for="contract_number">Contract Number</Label>
                <Input id="contract_number" v-model="form.contract_number" required />
                <InputError :message="form.errors.contract_number" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
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
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="rental_total_amount">Total Amount</Label>
                    <Input id="rental_total_amount" v-model="form.rental_total_amount" type="number" step="0.01" min="0" required />
                    <InputError :message="form.errors.rental_total_amount" />
                </div>

                <div class="grid gap-2">
                    <Label for="security_deposit_amount">Security Deposit</Label>
                    <Input id="security_deposit_amount" v-model="form.security_deposit_amount" type="number" step="0.01" min="0" />
                    <InputError :message="form.errors.security_deposit_amount" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="terms_conditions">Terms & Conditions</Label>
                <Textarea id="terms_conditions" v-model="form.terms_conditions" />
                <InputError :message="form.errors.terms_conditions" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">Update Lease</Button>
            </div>
        </form>
    </div>
</template>
