<script setup lang="ts">
import { Head, setLayoutProps, useForm } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

const { t } = useI18n();

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

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.appSettings'), href: '/app-settings/general' },
            { title: t('app.navigation.invoiceSettings'), href: '/app-settings/invoice' },
        ],
    });
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
    <Head :title="t('app.navigation.invoiceSettings')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="t('app.navigation.invoiceSettings')" :description="t('app.appSettings.shell.invoiceDescription')" />

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-2">
                <Label for="company_name">{{ t('app.appSettings.shell.companyName') }}</Label>
                <Input id="company_name" v-model="form.company_name" required :placeholder="t('app.appSettings.shell.companyNamePlaceholder')" />
                <InputError :message="form.errors.company_name" />
            </div>

            <div class="grid gap-2">
                <Label for="address">{{ t('app.appSettings.shell.address') }}</Label>
                <Textarea id="address" v-model="form.address" :placeholder="t('app.appSettings.shell.addressPlaceholder')" />
                <InputError :message="form.errors.address" />
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="grid gap-2">
                    <Label for="vat">{{ t('app.appSettings.shell.vat') }}</Label>
                    <Input id="vat" v-model="form.vat" type="number" step="0.01" min="0" max="100" placeholder="15.00" />
                    <InputError :message="form.errors.vat" />
                </div>
                <div class="grid gap-2">
                    <Label for="vat_number">{{ t('app.appSettings.shell.vatNumber') }}</Label>
                    <Input id="vat_number" v-model="form.vat_number" :placeholder="t('app.appSettings.shell.vatNumberPlaceholder')" />
                    <InputError :message="form.errors.vat_number" />
                </div>
                <div class="grid gap-2">
                    <Label for="cr_number">{{ t('app.appSettings.shell.crNumber') }}</Label>
                    <Input id="cr_number" v-model="form.cr_number" :placeholder="t('app.appSettings.shell.crNumberPlaceholder')" />
                    <InputError :message="form.errors.cr_number" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="instructions">{{ t('app.appSettings.shell.paymentInstructions') }}</Label>
                <Textarea id="instructions" v-model="form.instructions" :placeholder="t('app.appSettings.shell.paymentInstructionsPlaceholder')" />
                <InputError :message="form.errors.instructions" />
            </div>

            <div class="grid gap-2">
                <Label for="notes">{{ t('app.appSettings.shell.notes') }}</Label>
                <Textarea id="notes" v-model="form.notes" :placeholder="t('app.appSettings.shell.invoiceNotesPlaceholder')" />
                <InputError :message="form.errors.notes" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">{{ t('app.appSettings.shell.saveInvoiceSettings') }}</Button>
            </div>
        </form>
    </div>
</template>
