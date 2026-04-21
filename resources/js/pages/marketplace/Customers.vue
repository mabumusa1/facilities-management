<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Marketplace', href: '/marketplace' },
            { title: 'Customers', href: '/marketplace/customers' },
        ],
    },
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
    <Head title="Marketplace Customers" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading variant="small" title="Marketplace Customers" description="Manage customer leads and create new sales/property leads." />
            <Button variant="outline" as-child>
                <Link href="/marketplace">Back to Overview</Link>
            </Button>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle>Create Sales Lead</CardTitle>
                </CardHeader>
                <CardContent>
                    <form class="space-y-3" @submit.prevent="submitSalesLead">
                        <div class="grid gap-2">
                            <Label for="sales-name">Name</Label>
                            <Input id="sales-name" v-model="salesLeadForm.name" />
                            <InputError :message="salesLeadForm.errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="sales-phone">Phone</Label>
                            <Input id="sales-phone" v-model="salesLeadForm.phone_number" />
                            <InputError :message="salesLeadForm.errors.phone_number" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="sales-email">Email</Label>
                            <Input id="sales-email" v-model="salesLeadForm.email" type="email" />
                            <InputError :message="salesLeadForm.errors.email" />
                        </div>
                        <Button :disabled="salesLeadForm.processing">Create Sales Lead</Button>
                    </form>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Create Property Lead</CardTitle>
                </CardHeader>
                <CardContent>
                    <form class="space-y-3" @submit.prevent="submitPropertyLead">
                        <div class="grid gap-2">
                            <Label for="property-name">Name</Label>
                            <Input id="property-name" v-model="propertyLeadForm.name" />
                            <InputError :message="propertyLeadForm.errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="property-phone">Phone</Label>
                            <Input id="property-phone" v-model="propertyLeadForm.phone_number" />
                            <InputError :message="propertyLeadForm.errors.phone_number" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="property-email">Email</Label>
                            <Input id="property-email" v-model="propertyLeadForm.email" type="email" />
                            <InputError :message="propertyLeadForm.errors.email" />
                        </div>
                        <Button :disabled="propertyLeadForm.processing">Create Property Lead</Button>
                    </form>
                </CardContent>
            </Card>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Leads</CardTitle>
            </CardHeader>
            <CardContent class="space-y-2">
                <div v-for="lead in props.leads.data" :key="lead.id" class="rounded-md border p-3 text-sm">
                    <p class="font-medium">{{ lead.name ?? `Lead #${lead.id}` }}</p>
                    <p>Phone: {{ lead.phone_number ?? 'N/A' }}</p>
                    <p>Email: {{ lead.email ?? 'N/A' }}</p>
                    <p>Interest: {{ lead.interested ?? 'N/A' }}</p>
                </div>
                <p v-if="props.leads.data.length === 0" class="text-muted-foreground text-sm">No leads yet.</p>
            </CardContent>
        </Card>
    </div>
</template>
