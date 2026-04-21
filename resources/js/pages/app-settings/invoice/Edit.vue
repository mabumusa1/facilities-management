<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

const props = defineProps<{
    invoiceSetting: {
        id?: number;
        company_name?: string;
        address?: string;
        vat?: string;
        vat_number?: string;
        cr_number?: string;
        instructions?: string;
        notes?: string;
    } | null;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'App Settings', href: '/app-settings/general' },
            { title: 'Invoice Settings', href: '/app-settings/invoice' },
        ],
    },
});

const form = useForm({
    company_name: props.invoiceSetting?.company_name ?? '',
    address: props.invoiceSetting?.address ?? '',
    vat: props.invoiceSetting?.vat ?? '',
    vat_number: props.invoiceSetting?.vat_number ?? '',
    cr_number: props.invoiceSetting?.cr_number ?? '',
    instructions: props.invoiceSetting?.instructions ?? '',
    notes: props.invoiceSetting?.notes ?? '',
});

function submit() {
    form.put('/app-settings/invoice');
}
</script>

<template>
    <Head title="Invoice Settings" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" title="Invoice Settings" description="Configure your company's invoice details." />

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-2">
                <Label for="company_name">Company Name</Label>
                <Input id="company_name" v-model="form.company_name" required placeholder="Your company name" />
                <InputError :message="form.errors.company_name" />
            </div>

            <div class="grid gap-2">
                <Label for="address">Address</Label>
                <Textarea id="address" v-model="form.address" placeholder="Company address" />
                <InputError :message="form.errors.address" />
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="grid gap-2">
                    <Label for="vat">VAT (%)</Label>
                    <Input id="vat" v-model="form.vat" type="number" step="0.01" min="0" max="100" placeholder="15.00" />
                    <InputError :message="form.errors.vat" />
                </div>
                <div class="grid gap-2">
                    <Label for="vat_number">VAT Number</Label>
                    <Input id="vat_number" v-model="form.vat_number" placeholder="VAT registration number" />
                    <InputError :message="form.errors.vat_number" />
                </div>
                <div class="grid gap-2">
                    <Label for="cr_number">CR Number</Label>
                    <Input id="cr_number" v-model="form.cr_number" placeholder="Commercial register number" />
                    <InputError :message="form.errors.cr_number" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="instructions">Payment Instructions</Label>
                <Textarea id="instructions" v-model="form.instructions" placeholder="Payment instructions shown on invoices..." />
                <InputError :message="form.errors.instructions" />
            </div>

            <div class="grid gap-2">
                <Label for="notes">Notes</Label>
                <Textarea id="notes" v-model="form.notes" placeholder="Additional notes for invoices..." />
                <InputError :message="form.errors.notes" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">Save Invoice Settings</Button>
            </div>
        </form>
    </div>
</template>
