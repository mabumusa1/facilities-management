<script setup lang="ts">
import { Head, setLayoutProps, useForm } from '@inertiajs/vue3';
import { ref, watchEffect } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { useI18n } from '@/composables/useI18n';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { index as quotesIndex, create as quotesCreate, store as quotesStore } from '@/actions/App/Http/Controllers/Leasing/QuoteController';

const { t } = useI18n();

type ContactOption = { id: number; first_name: string; last_name: string };
type UnitOption = { id: number; name: string };
type ContractTypeOption = { id: number; name_en: string | null; name_ar: string | null };
type PaymentFrequencyOption = { id: number; name: string; name_en: string | null; name_ar: string | null };

const props = defineProps<{
    units: UnitOption[];
    contacts: ContactOption[];
    contractTypes: ContractTypeOption[];
    paymentFrequencies: PaymentFrequencyOption[];
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.quotes.pageTitle'), href: quotesIndex.url() },
            { title: t('app.quotes.create.pageTitle'), href: quotesCreate.url() },
        ],
    });
});

type AdditionalCharge = {
    label: { en: string; ar: string };
    amount: string;
};

const form = useForm({
    unit_id: '',
    contact_id: '',
    contract_type_id: '',
    duration_months: '',
    start_date: '',
    rent_amount: '',
    payment_frequency_id: '',
    security_deposit: '',
    valid_until: '',
    additional_charges: [] as AdditionalCharge[],
    special_conditions: { en: '', ar: '' },
    action: 'save_draft' as 'save_draft' | 'send',
});

const additionalCharges = ref<AdditionalCharge[]>([]);

function addCharge() {
    additionalCharges.value.push({ label: { en: '', ar: '' }, amount: '' });
}

function removeCharge(index: number) {
    additionalCharges.value.splice(index, 1);
}

function submit(action: 'save_draft' | 'send') {
    form.additional_charges = additionalCharges.value;
    form.action = action;
    form.post(quotesStore.url());
}
</script>

<template>
    <Head :title="t('app.quotes.create.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            variant="small"
            :title="t('app.quotes.create.heading')"
            :description="t('app.quotes.create.description')"
        />

        <form class="max-w-3xl space-y-8" @submit.prevent>
            <!-- Unit & Contact -->
            <section class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="unit_id">{{ t('app.quotes.create.unitLabel') }}</Label>
                        <Select v-model="form.unit_id">
                            <SelectTrigger id="unit_id">
                                <SelectValue :placeholder="t('app.quotes.create.selectUnit')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="unit in props.units"
                                    :key="unit.id"
                                    :value="String(unit.id)"
                                >
                                    {{ unit.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.unit_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="contact_id">{{ t('app.quotes.create.residentLabel') }}</Label>
                        <Select v-model="form.contact_id">
                            <SelectTrigger id="contact_id">
                                <SelectValue :placeholder="t('app.quotes.create.selectContact')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="contact in props.contacts"
                                    :key="contact.id"
                                    :value="String(contact.id)"
                                >
                                    {{ contact.first_name }} {{ contact.last_name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.contact_id" />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="contract_type_id">{{ t('app.quotes.create.contractType') }}</Label>
                        <Select v-model="form.contract_type_id">
                            <SelectTrigger id="contract_type_id">
                                <SelectValue :placeholder="t('app.quotes.create.selectContractType')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="ct in props.contractTypes"
                                    :key="ct.id"
                                    :value="String(ct.id)"
                                >
                                    {{ ct.name_en ?? ct.name_ar }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.contract_type_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="duration_months">{{ t('app.quotes.create.duration') }}</Label>
                        <Input
                            id="duration_months"
                            v-model="form.duration_months"
                            type="number"
                            min="1"
                            :placeholder="t('app.quotes.create.durationPlaceholder')"
                        />
                        <InputError :message="form.errors.duration_months" />
                    </div>
                </div>
            </section>

            <!-- Financial Terms -->
            <section class="space-y-4">
                <h3 class="text-base font-semibold">{{ t('app.quotes.create.financialTerms') }}</h3>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="rent_amount">{{ t('app.quotes.create.rentAmount') }}</Label>
                        <Input
                            id="rent_amount"
                            v-model="form.rent_amount"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                        />
                        <InputError :message="form.errors.rent_amount" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="payment_frequency_id">{{ t('app.quotes.create.paymentFrequency') }}</Label>
                        <Select v-model="form.payment_frequency_id">
                            <SelectTrigger id="payment_frequency_id">
                                <SelectValue :placeholder="t('app.quotes.create.selectFrequency')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="freq in props.paymentFrequencies"
                                    :key="freq.id"
                                    :value="String(freq.id)"
                                >
                                    {{ freq.name_en ?? freq.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.payment_frequency_id" />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="security_deposit">{{ t('app.quotes.create.securityDeposit') }}</Label>
                        <Input
                            id="security_deposit"
                            v-model="form.security_deposit"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                        />
                        <InputError :message="form.errors.security_deposit" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="valid_until">{{ t('app.quotes.create.validUntil') }}</Label>
                        <Input
                            id="valid_until"
                            v-model="form.valid_until"
                            type="date"
                        />
                        <InputError :message="form.errors.valid_until" />
                    </div>
                </div>

                <div class="grid gap-2 sm:max-w-xs">
                    <Label for="start_date">{{ t('app.quotes.create.startDate') }}</Label>
                    <Input
                        id="start_date"
                        v-model="form.start_date"
                        type="date"
                    />
                    <InputError :message="form.errors.start_date" />
                </div>
            </section>

            <!-- Special Conditions -->
            <section class="space-y-4">
                <div class="grid gap-2">
                    <Label for="special_conditions_en">{{ t('app.quotes.create.specialConditionsEn') }}</Label>
                    <Textarea
                        id="special_conditions_en"
                        v-model="form.special_conditions.en"
                        rows="3"
                    />
                    <InputError :message="form.errors['special_conditions.en']" />
                </div>

                <div class="grid gap-2" dir="rtl">
                    <Label for="special_conditions_ar">{{ t('app.quotes.create.specialConditionsAr') }}</Label>
                    <Textarea
                        id="special_conditions_ar"
                        v-model="form.special_conditions.ar"
                        rows="3"
                        dir="rtl"
                    />
                    <InputError :message="form.errors['special_conditions.ar']" />
                </div>
            </section>

            <!-- Additional Charges -->
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold">{{ t('app.quotes.create.additionalCharges') }}</h3>
                    <Button type="button" variant="outline" size="sm" @click="addCharge">
                        + {{ t('app.quotes.create.addCharge') }}
                    </Button>
                </div>

                <div v-if="additionalCharges.length > 0" class="space-y-3">
                    <div
                        v-for="(charge, index) in additionalCharges"
                        :key="index"
                        class="grid grid-cols-[1fr_1fr_auto_auto] items-end gap-3 rounded-md border p-3"
                    >
                        <div class="grid gap-1">
                            <Label :for="`charge-label-en-${index}`">{{ t('app.quotes.create.chargeLabelEn') }}</Label>
                            <Input
                                :id="`charge-label-en-${index}`"
                                v-model="charge.label.en"
                                :placeholder="t('app.quotes.create.chargeLabelPlaceholder')"
                            />
                        </div>

                        <div class="grid gap-1">
                            <Label :for="`charge-label-ar-${index}`">{{ t('app.quotes.create.chargeLabelAr') }}</Label>
                            <Input
                                :id="`charge-label-ar-${index}`"
                                v-model="charge.label.ar"
                                dir="rtl"
                                :placeholder="t('app.quotes.create.chargeLabelPlaceholder')"
                            />
                        </div>

                        <div class="grid gap-1">
                            <Label :for="`charge-amount-${index}`">{{ t('app.quotes.create.chargeAmount') }}</Label>
                            <Input
                                :id="`charge-amount-${index}`"
                                v-model="charge.amount"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="0.00"
                            />
                        </div>

                        <Button
                            type="button"
                            variant="ghost"
                            size="sm"
                            :aria-label="`Remove charge ${charge.label.en || index + 1}`"
                            @click="removeCharge(index)"
                        >
                            ✕
                        </Button>
                    </div>
                </div>

                <InputError :message="form.errors.additional_charges" />
            </section>

            <!-- Actions -->
            <div class="flex items-center gap-3 pt-2">
                <Button
                    type="button"
                    variant="outline"
                    :disabled="form.processing"
                    @click="submit('save_draft')"
                >
                    {{ t('app.quotes.create.saveDraft') }}
                </Button>

                <Button
                    type="button"
                    :disabled="form.processing"
                    @click="submit('send')"
                >
                    {{ t('app.quotes.create.send') }}
                </Button>
            </div>

            <div v-if="form.errors.action" class="text-destructive text-sm">
                {{ form.errors.action }}
            </div>
        </form>
    </div>
</template>
