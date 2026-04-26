<script setup lang="ts">
import { Head, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import { index as quotesIndex, show as quotesShow, storeConversion as quotesStoreConversion } from '@/actions/App/Http/Controllers/Leasing/QuoteController';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();

type QuoteDetail = {
    id: number;
    quote_number: string | null;
    unit: { id: number; name: string } | null;
    contact: { id: number; first_name: string; last_name: string } | null;
    status: { id: number; name: string; name_en: string | null } | null;
    duration_months: number;
    start_date: string;
    rent_amount: string;
    security_deposit: string;
    special_conditions: { en?: string; ar?: string } | null;
};

type UnitCategory = { id: number; name: string; name_en: string | null; icon: string | null };
type RentalContractType = { id: number; name: string; name_en: string | null };
type PaymentSchedule = { id: number; name: string; name_en: string | null; parent_id: number | null };
type Unit = { id: number; name: string };
type Resident = { id: number; first_name: string; last_name: string };
type Admin = { id: number; first_name: string; last_name: string };

const props = defineProps<{
    quote: QuoteDetail;
    unitCategories: UnitCategory[];
    rentalContractTypes: RentalContractType[];
    paymentSchedules: PaymentSchedule[];
    units: Unit[];
    residents: Resident[];
    admins: Admin[];
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.quotes.pageTitle'), href: quotesIndex.url() },
            { title: props.quote.quote_number ?? `#${props.quote.id}`, href: quotesShow.url(props.quote.id) },
            { title: t('app.quotes.convert.title'), href: '#' },
        ],
    });
});

// Pre-fill from quote data
const form = useForm({
    autoGenerateLeaseNumber: true,
    contract_number: '',
    tenant_id: props.quote.contact?.id ? String(props.quote.contact.id) : '',
    lease_unit_type_id: '',
    rental_contract_type_id: '',
    payment_schedule_id: '',
    deal_owner_id: '',
    start_date: props.quote.start_date ?? '',
    end_date: '',
    handover_date: props.quote.start_date ?? '',
    tenant_type: 'individual',
    rental_type: 'total',
    rental_total_amount: props.quote.rent_amount ?? '',
    security_deposit_amount: props.quote.security_deposit ?? '',
    terms_conditions: props.quote.special_conditions?.en ?? '',
    number_of_months: props.quote.duration_months ? String(props.quote.duration_months) : '',
    number_of_years: '0',
    number_of_days: '0',
    unit_id: props.quote.unit?.id ? String(props.quote.unit.id) : '',
});

const rentChangedFromQuote = computed(() => {
    const quoteAmount = parseFloat(props.quote.rent_amount);
    const formAmount = parseFloat(String(form.rental_total_amount));

    return ! isNaN(quoteAmount) && ! isNaN(formAmount) && quoteAmount !== formAmount;
});

function submit(action: 'save' | 'kyc'): void {
    form.transform((data) => ({
        ...data,
        _action: action,
    })).post(quotesStoreConversion.url(props.quote.id));
}
</script>

<template>
    <Head :title="t('app.quotes.convert.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center gap-2">
            <h2 class="text-2xl font-bold tracking-tight">
                {{ t('app.quotes.convert.title') }}
            </h2>
        </div>

        <div
            class="rounded-md border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800 dark:border-blue-800 dark:bg-blue-950 dark:text-blue-200"
        >
            {{ t('app.quotes.convert.source', { id: quote.quote_number ?? String(quote.id) }) }}
        </div>

        <div
            v-if="rentChangedFromQuote"
            aria-live="polite"
            class="rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-200"
        >
            {{ t('app.quotes.convert.changesDetected') }}:
            {{ t('app.quotes.create.rentAmount') }} {{ quote.rent_amount }} &rarr; {{ form.rental_total_amount }}
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('app.quotes.convert.contractInfo') }}</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="flex items-center gap-2">
                        <input
                            id="autoGenerateLeaseNumber"
                            v-model="form.autoGenerateLeaseNumber"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300"
                        />
                        <Label for="autoGenerateLeaseNumber">{{ t('app.quotes.convert.autoGenerateNumber') }}</Label>
                    </div>

                    <div v-if="!form.autoGenerateLeaseNumber" class="space-y-1">
                        <Label for="contract_number">{{ t('app.leases.create.contractNumber') }}</Label>
                        <Input
                            id="contract_number"
                            v-model="form.contract_number"
                            type="text"
                            :class="{ 'border-red-500': form.errors.contract_number }"
                        />
                        <p v-if="form.errors.contract_number" class="text-sm text-red-500">{{ form.errors.contract_number }}</p>
                    </div>

                    <div class="space-y-1">
                        <Label for="unit_id">{{ t('app.quotes.create.unitLabel') }}</Label>
                        <Select v-model="form.unit_id">
                            <SelectTrigger :class="{ 'border-red-500': form.errors.unit_id }">
                                <SelectValue :placeholder="t('app.quotes.create.selectUnit')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="unit in units" :key="unit.id" :value="String(unit.id)">
                                    {{ unit.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="form.errors.unit_id" class="text-sm text-red-500">{{ form.errors.unit_id }}</p>
                    </div>

                    <div class="space-y-1">
                        <Label for="tenant_id">{{ t('app.quotes.create.residentLabel') }}</Label>
                        <Select v-model="form.tenant_id">
                            <SelectTrigger :class="{ 'border-red-500': form.errors.tenant_id }">
                                <SelectValue :placeholder="t('app.quotes.create.selectContact')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="resident in residents" :key="resident.id" :value="String(resident.id)">
                                    {{ resident.first_name }} {{ resident.last_name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="form.errors.tenant_id" class="text-sm text-red-500">{{ form.errors.tenant_id }}</p>
                    </div>

                    <div class="space-y-1">
                        <Label for="lease_unit_type_id">{{ t('app.leases.create.unitType') }}</Label>
                        <Select v-model="form.lease_unit_type_id">
                            <SelectTrigger :class="{ 'border-red-500': form.errors.lease_unit_type_id }">
                                <SelectValue :placeholder="t('app.leases.create.selectUnitType')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="cat in unitCategories" :key="cat.id" :value="String(cat.id)">
                                    {{ cat.name_en ?? cat.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="form.errors.lease_unit_type_id" class="text-sm text-red-500">{{ form.errors.lease_unit_type_id }}</p>
                    </div>

                    <div class="space-y-1">
                        <Label for="rental_contract_type_id">{{ t('app.quotes.create.contractType') }}</Label>
                        <Select v-model="form.rental_contract_type_id">
                            <SelectTrigger :class="{ 'border-red-500': form.errors.rental_contract_type_id }">
                                <SelectValue :placeholder="t('app.quotes.create.selectContractType')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="type in rentalContractTypes" :key="type.id" :value="String(type.id)">
                                    {{ type.name_en ?? type.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="form.errors.rental_contract_type_id" class="text-sm text-red-500">{{ form.errors.rental_contract_type_id }}</p>
                    </div>

                    <div class="space-y-1">
                        <Label for="payment_schedule_id">{{ t('app.leases.create.paymentSchedule') }}</Label>
                        <Select v-model="form.payment_schedule_id">
                            <SelectTrigger :class="{ 'border-red-500': form.errors.payment_schedule_id }">
                                <SelectValue :placeholder="t('app.leases.create.selectPaymentSchedule')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="schedule in paymentSchedules"
                                    :key="schedule.id"
                                    :value="String(schedule.id)"
                                >
                                    {{ schedule.name_en ?? schedule.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="form.errors.payment_schedule_id" class="text-sm text-red-500">{{ form.errors.payment_schedule_id }}</p>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <div class="space-y-1">
                            <Label for="number_of_years">{{ t('app.leases.create.years') }}</Label>
                            <Input id="number_of_years" v-model="form.number_of_years" type="number" min="0" />
                        </div>
                        <div class="space-y-1">
                            <Label for="number_of_months">{{ t('app.leases.create.months') }}</Label>
                            <Input id="number_of_months" v-model="form.number_of_months" type="number" min="0" />
                        </div>
                        <div class="space-y-1">
                            <Label for="number_of_days">{{ t('app.leases.create.days') }}</Label>
                            <Input id="number_of_days" v-model="form.number_of_days" type="number" min="0" />
                        </div>
                    </div>

                    <div class="space-y-1">
                        <Label for="start_date">{{ t('app.quotes.create.startDate') }}</Label>
                        <Input
                            id="start_date"
                            v-model="form.start_date"
                            type="date"
                            :class="{ 'border-red-500': form.errors.start_date }"
                        />
                        <p v-if="form.errors.start_date" class="text-sm text-red-500">{{ form.errors.start_date }}</p>
                    </div>

                    <div class="space-y-1">
                        <Label for="end_date">{{ t('app.leases.create.endDate') }}</Label>
                        <Input
                            id="end_date"
                            v-model="form.end_date"
                            type="date"
                            :class="{ 'border-red-500': form.errors.end_date }"
                        />
                        <p v-if="form.errors.end_date" class="text-sm text-red-500">{{ form.errors.end_date }}</p>
                    </div>

                    <div class="space-y-1">
                        <Label for="handover_date">{{ t('app.leases.create.handoverDate') }}</Label>
                        <Input
                            id="handover_date"
                            v-model="form.handover_date"
                            type="date"
                            :class="{ 'border-red-500': form.errors.handover_date }"
                        />
                        <p v-if="form.errors.handover_date" class="text-sm text-red-500">{{ form.errors.handover_date }}</p>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>{{ t('app.quotes.convert.financialTerms') }}</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="space-y-1">
                        <Label for="rental_total_amount">{{ t('app.quotes.create.rentAmount') }}</Label>
                        <Input
                            id="rental_total_amount"
                            v-model="form.rental_total_amount"
                            type="number"
                            min="0"
                            step="0.01"
                            :class="{ 'border-red-500': form.errors.rental_total_amount }"
                        />
                        <p v-if="form.errors.rental_total_amount" class="text-sm text-red-500">{{ form.errors.rental_total_amount }}</p>
                    </div>

                    <div class="space-y-1">
                        <Label for="security_deposit_amount">{{ t('app.quotes.create.securityDeposit') }}</Label>
                        <Input
                            id="security_deposit_amount"
                            v-model="form.security_deposit_amount"
                            type="number"
                            min="0"
                            step="0.01"
                        />
                    </div>

                    <div class="space-y-1">
                        <Label for="tenant_type">{{ t('app.leases.create.tenantType') }}</Label>
                        <Select v-model="form.tenant_type">
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="individual">{{ t('app.leases.create.individual') }}</SelectItem>
                                <SelectItem value="company">{{ t('app.leases.create.company') }}</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="space-y-1">
                        <Label for="rental_type">{{ t('app.leases.create.rentalType') }}</Label>
                        <Select v-model="form.rental_type">
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="total">{{ t('app.leases.create.total') }}</SelectItem>
                                <SelectItem value="detailed">{{ t('app.leases.create.detailed') }}</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="space-y-1">
                        <Label for="terms_conditions">{{ t('app.leases.create.termsConditions') }}</Label>
                        <textarea
                            id="terms_conditions"
                            v-model="form.terms_conditions"
                            rows="4"
                            class="border-input bg-background placeholder:text-muted-foreground focus-visible:ring-ring flex min-h-[60px] w-full rounded-md border px-3 py-2 text-sm shadow-sm focus-visible:ring-1 focus-visible:outline-none"
                        />
                    </div>
                </CardContent>
            </Card>
        </div>

        <div v-if="form.errors.quote" class="text-sm text-red-500">{{ form.errors.quote }}</div>

        <div class="flex items-center justify-end gap-3">
            <Button
                variant="outline"
                :disabled="form.processing"
                @click="submit('save')"
            >
                {{ t('app.quotes.convert.saveDraft') }}
            </Button>

            <Button
                :disabled="form.processing"
                @click="submit('kyc')"
            >
                {{ t('app.quotes.convert.saveGoToKyc') }}
            </Button>
        </div>
    </div>
</template>
