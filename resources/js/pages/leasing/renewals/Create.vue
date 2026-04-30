<script setup lang="ts">
import { Head, Link, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import { create as renewalCreate, store as renewalStore } from '@/actions/App/Http/Controllers/Leasing/LeaseRenewalController';
import { show as leaseShow } from '@/actions/App/Http/Controllers/Leasing/LeaseController';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();

type ContractType = { id: number; name: string; name_en: string | null; name_ar: string | null };

type Lease = {
    id: number;
    contract_number: string;
    tenant?: { first_name: string; last_name: string } | null;
    units?: Array<{ name: string }>;
    start_date: string;
    end_date: string;
    rental_total_amount: string;
    payment_schedule?: { name: string } | null;
    rental_contract_type?: { id: number; name: string } | null;
};

const props = defineProps<{
    lease: Lease;
    contractTypes: ContractType[];
    defaults: {
        new_start_date: string | null;
        duration_months: number;
        new_rent_amount: string;
        payment_frequency: string | null;
        contract_type_id: number | null;
        valid_until: string | null;
    };
}>();

const form = useForm({
    new_start_date: props.defaults.new_start_date ?? '',
    duration_months: String(props.defaults.duration_months ?? 12),
    new_rent_amount: props.defaults.new_rent_amount ?? '',
    payment_frequency: props.defaults.payment_frequency ?? '',
    contract_type_id: props.defaults.contract_type_id ? String(props.defaults.contract_type_id) : '',
    valid_until: props.defaults.valid_until ?? '',
    message_en: '',
    message_ar: '',
});

const rentDiff = computed(() => {
    const current = parseFloat(String(props.defaults.new_rent_amount ?? 0));
    const newRent = parseFloat(form.new_rent_amount);
    if (isNaN(current) || isNaN(newRent)) {
        return null;
    }

    return newRent - current;
});

const tenantName = computed(() => {
    if (! props.lease.tenant) {
        return '—';
    }

    return `${props.lease.tenant.first_name} ${props.lease.tenant.last_name}`.trim();
});

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.leases.pageTitle'), href: '/leases' },
            { title: props.lease.contract_number, href: leaseShow.url(props.lease.id) },
            { title: t('app.leases.renewal.createTitle'), href: renewalCreate.url(props.lease.id) },
        ],
    });
});

function submit() {
    form.post(renewalStore.url(props.lease.id));
}
</script>

<template>
    <Head :title="t('app.leases.renewal.createTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">{{ t('app.leases.renewal.createTitle') }}</h2>
            </div>
            <Button variant="outline" as-child>
                <Link :href="leaseShow.url(lease.id)">{{ t('app.actions.cancel') }}</Link>
            </Button>
        </div>

        <!-- Source Lease Summary -->
        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.leases.renewal.sourceLease') }}</CardTitle>
            </CardHeader>
            <CardContent class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">{{ t('app.leases.show.tenant') }}</span>
                    <span>{{ tenantName }}</span>
                </div>
                <div v-if="lease.units && lease.units.length > 0" class="flex justify-between">
                    <span class="text-muted-foreground">{{ t('app.leases.show.units') }}</span>
                    <span>{{ lease.units.map(u => u.name).join(', ') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">{{ t('app.leases.renewal.currentTerm') }}</span>
                    <span>{{ lease.start_date }} → {{ lease.end_date }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">{{ t('app.leases.renewal.currentRent') }}</span>
                    <span>{{ t('app.leases.renewal.currentRentValue', { amount: lease.rental_total_amount, freq: lease.payment_schedule?.name ?? '' }) }}</span>
                </div>
            </CardContent>
        </Card>

        <!-- Renewal Terms Form -->
        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.leases.renewal.renewalTerms') }}</CardTitle>
            </CardHeader>
            <CardContent>
                <form class="grid gap-4 md:grid-cols-2" @submit.prevent="submit">
                    <!-- New Start Date -->
                    <div class="grid gap-2">
                        <Label for="new-start-date">
                            {{ t('app.leases.renewal.newStartDate') }}
                        </Label>
                        <Input
                            id="new-start-date"
                            v-model="form.new_start_date"
                            type="date"
                            :aria-describedby="'new-start-date-hint'"
                        />
                        <p id="new-start-date-hint" class="text-muted-foreground text-xs">
                            {{ t('app.leases.renewal.newStartDateHint') }}
                        </p>
                        <p v-if="form.errors.new_start_date" role="alert" class="text-destructive text-sm">
                            {{ form.errors.new_start_date }}
                        </p>
                    </div>

                    <!-- Duration -->
                    <div class="grid gap-2">
                        <Label for="duration-months">{{ t('app.leases.renewal.durationMonths') }} *</Label>
                        <Input
                            id="duration-months"
                            v-model="form.duration_months"
                            type="number"
                            min="1"
                            max="240"
                        />
                        <p v-if="form.errors.duration_months" role="alert" class="text-destructive text-sm">
                            {{ form.errors.duration_months }}
                        </p>
                    </div>

                    <!-- New Rent Amount -->
                    <div class="grid gap-2">
                        <Label for="new-rent-amount">{{ t('app.leases.renewal.newRent') }} *</Label>
                        <Input
                            id="new-rent-amount"
                            v-model="form.new_rent_amount"
                            type="number"
                            step="0.01"
                            min="0"
                        />
                        <p
                            v-if="rentDiff !== null"
                            class="text-xs"
                            :class="rentDiff >= 0 ? 'text-green-600' : 'text-destructive'"
                        >
                            {{ rentDiff >= 0 ? '+' : '' }}{{ rentDiff.toFixed(2) }}
                            {{ t('app.leases.renewal.rentDiffSuffix') }}
                        </p>
                        <p v-if="form.errors.new_rent_amount" role="alert" class="text-destructive text-sm">
                            {{ form.errors.new_rent_amount }}
                        </p>
                    </div>

                    <!-- Payment Frequency -->
                    <div class="grid gap-2">
                        <Label for="payment-frequency">{{ t('app.leases.renewal.paymentFrequency') }}</Label>
                        <Input
                            id="payment-frequency"
                            v-model="form.payment_frequency"
                            type="text"
                        />
                    </div>

                    <!-- Contract Type -->
                    <div class="grid gap-2">
                        <Label for="contract-type">{{ t('app.leases.renewal.contractType') }}</Label>
                        <select
                            id="contract-type"
                            v-model="form.contract_type_id"
                            class="rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="">— {{ t('app.common.select') }} —</option>
                            <option
                                v-for="ct in contractTypes"
                                :key="ct.id"
                                :value="String(ct.id)"
                            >
                                {{ ct.name_en ?? ct.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Valid Until -->
                    <div class="grid gap-2">
                        <Label for="valid-until">{{ t('app.leases.renewal.validUntil') }} *</Label>
                        <Input
                            id="valid-until"
                            v-model="form.valid_until"
                            type="date"
                        />
                        <p v-if="form.errors.valid_until" role="alert" class="text-destructive text-sm">
                            {{ form.errors.valid_until }}
                        </p>
                    </div>

                    <!-- Messages — full width -->
                    <div class="grid gap-2 md:col-span-2">
                        <Label for="message-en">{{ t('app.leases.renewal.messageEn') }}</Label>
                        <Textarea
                            id="message-en"
                            v-model="form.message_en"
                            rows="4"
                        />
                    </div>

                    <div class="grid gap-2 md:col-span-2">
                        <Label for="message-ar" dir="rtl">{{ t('app.leases.renewal.messageAr') }}</Label>
                        <Textarea
                            id="message-ar"
                            v-model="form.message_ar"
                            rows="4"
                            dir="rtl"
                        />
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2 md:col-span-2">
                        <Button
                            type="submit"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? t('app.common.saving') : t('app.leases.renewal.saveDraft') }}
                        </Button>
                        <Button type="button" variant="outline" as-child>
                            <Link :href="leaseShow.url(lease.id)">{{ t('app.actions.cancel') }}</Link>
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
