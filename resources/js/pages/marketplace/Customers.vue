<script setup lang="ts">
import { Head, Link, setLayoutProps, useForm } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.marketplace'), href: '/marketplace' },
            { title: t('app.navigation.customers'), href: '/marketplace/customers' },
        ],
    });
});

const props = defineProps<{
    leads: {
        data: Array<{
            id: number;
            name: string | null;
            phone_number: string | null;
            email: string | null;
            interested: string | null;
        }>;
    };
}>();

const salesLeadForm = useForm({
    name: '',
    phone_number: '',
    email: '',
});

const propertyLeadForm = useForm({
    name: '',
    phone_number: '',
    email: '',
    interested: 'rent',
});

function submitSalesLead() {
    salesLeadForm.post('/marketplace/customers/sales-lead', {
        preserveScroll: true,
        onSuccess: () => salesLeadForm.reset(),
    });
}

function submitPropertyLead() {
    propertyLeadForm.post('/marketplace/customers/property-lead', {
        preserveScroll: true,
        onSuccess: () => propertyLeadForm.reset(),
    });
}
</script>

<template>
    <Head :title="t('app.marketplace.customers.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading variant="small" :title="t('app.marketplace.customers.heading')" :description="t('app.marketplace.customers.description')" />
            <Button variant="outline" as-child>
                <Link href="/marketplace">{{ t('app.marketplace.common.backToOverview') }}</Link>
            </Button>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('app.marketplace.customers.createSalesLead') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <form class="space-y-3" @submit.prevent="submitSalesLead">
                        <div class="grid gap-2">
                            <Label for="sales-name">{{ t('app.marketplace.common.name') }}</Label>
                            <Input id="sales-name" v-model="salesLeadForm.name" />
                            <InputError :message="salesLeadForm.errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="sales-phone">{{ t('app.marketplace.common.phone') }}</Label>
                            <Input id="sales-phone" v-model="salesLeadForm.phone_number" />
                            <InputError :message="salesLeadForm.errors.phone_number" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="sales-email">{{ t('app.marketplace.common.email') }}</Label>
                            <Input id="sales-email" v-model="salesLeadForm.email" type="email" />
                            <InputError :message="salesLeadForm.errors.email" />
                        </div>
                        <Button :disabled="salesLeadForm.processing">{{ t('app.marketplace.customers.createSalesLead') }}</Button>
                    </form>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>{{ t('app.marketplace.customers.createPropertyLead') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <form class="space-y-3" @submit.prevent="submitPropertyLead">
                        <div class="grid gap-2">
                            <Label for="property-name">{{ t('app.marketplace.common.name') }}</Label>
                            <Input id="property-name" v-model="propertyLeadForm.name" />
                            <InputError :message="propertyLeadForm.errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="property-phone">{{ t('app.marketplace.common.phone') }}</Label>
                            <Input id="property-phone" v-model="propertyLeadForm.phone_number" />
                            <InputError :message="propertyLeadForm.errors.phone_number" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="property-email">{{ t('app.marketplace.common.email') }}</Label>
                            <Input id="property-email" v-model="propertyLeadForm.email" type="email" />
                            <InputError :message="propertyLeadForm.errors.email" />
                        </div>
                        <Button :disabled="propertyLeadForm.processing">{{ t('app.marketplace.customers.createPropertyLead') }}</Button>
                    </form>
                </CardContent>
            </Card>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.marketplace.customers.leads') }}</CardTitle>
            </CardHeader>
            <CardContent class="space-y-2">
                <div v-for="lead in props.leads.data" :key="lead.id" class="rounded-md border p-3 text-sm">
                    <p class="font-medium">{{ lead.name ?? t('app.marketplace.customers.leadFallback', { id: lead.id }) }}</p>
                    <p>{{ t('app.marketplace.common.phone') }}: {{ lead.phone_number ?? t('app.common.notAvailable') }}</p>
                    <p>{{ t('app.marketplace.common.email') }}: {{ lead.email ?? t('app.common.notAvailable') }}</p>
                    <p>{{ t('app.marketplace.customers.interest') }}: {{ lead.interested ?? t('app.common.notAvailable') }}</p>
                </div>
                <p v-if="props.leads.data.length === 0" class="text-muted-foreground text-sm">{{ t('app.marketplace.customers.noLeads') }}</p>
            </CardContent>
        </Card>
    </div>
</template>
