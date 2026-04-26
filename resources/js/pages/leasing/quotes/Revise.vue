<script setup lang="ts">
import { Head, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, ref, watchEffect } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { useI18n } from '@/composables/useI18n';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { Badge } from '@/components/ui/badge';
import {
    index as quotesIndex,
    show as quotesShow,
    storeRevision as quotesStoreRevision,
} from '@/actions/App/Http/Controllers/Leasing/QuoteController';

const { t } = useI18n();

type ContactOption = { id: number; first_name: string; last_name: string };
type UnitOption = { id: number; name: string };
type ContractTypeOption = { id: number; name_en: string | null; name_ar: string | null };
type PaymentFrequencyOption = { id: number; name: string; name_en: string | null; name_ar: string | null };

type DiffValue = {
    old: string | number | null | Record<string, unknown>;
    new: string | number | null | Record<string, unknown>;
};

type QuoteDetail = {
    id: number;
    quote_number: string | null;
    unit: { id: number; name: string } | null;
    contact: { id: number; first_name: string; last_name: string } | null;
    contract_type: { id: number; name_en: string | null; name_ar: string | null } | null;
    status: { id: number; name: string; name_en: string | null } | null;
    payment_frequency: { id: number; name: string; name_en: string | null } | null;
    duration_months: number;
    start_date: string;
    rent_amount: string;
    security_deposit: string;
    valid_until: string;
    special_conditions: { en?: string; ar?: string } | null;
    additional_charges: { label: { en: string; ar: string }; amount: string | number }[] | null;
    version: number;
};

const props = defineProps<{
    quote: QuoteDetail;
    diff: Record<string, DiffValue>;
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
            {
                title: props.quote.quote_number ?? `#${props.quote.id}`,
                href: quotesShow.url(props.quote.id),
            },
            { title: t('app.quotes.revise.pageTitle'), href: '#' },
        ],
    });
});

type AdditionalCharge = {
    label: { en: string; ar: string };
    amount: string;
};

const additionalCharges = ref<AdditionalCharge[]>(
    (props.quote.additional_charges ?? []).map((c) => ({
        label: { en: c.label.en, ar: c.label.ar },
        amount: String(c.amount),
    })),
);

const subjectPrefixMode = ref<'updated_quote' | 'revised_offer' | 'custom'>('updated_quote');
const customSubjectPrefix = ref('');

const resolvedSubjectPrefix = computed(() => {
    if (subjectPrefixMode.value === 'updated_quote') {
        return t('app.quotes.revise.subjectPrefixUpdatedQuote');
    }
    if (subjectPrefixMode.value === 'revised_offer') {
        return t('app.quotes.revise.subjectPrefixRevisedOffer');
    }
    return customSubjectPrefix.value;
});

const form = useForm({
    unit_id: String(props.quote.unit?.id ?? ''),
    contact_id: String(props.quote.contact?.id ?? ''),
    contract_type_id: props.quote.contract_type ? String(props.quote.contract_type.id) : '',
    duration_months: String(props.quote.duration_months),
    start_date: props.quote.start_date,
    rent_amount: props.quote.rent_amount,
    payment_frequency_id: String(props.quote.payment_frequency?.id ?? ''),
    security_deposit: props.quote.security_deposit,
    valid_until: props.quote.valid_until
        ? props.quote.valid_until.substring(0, 10)
        : '',
    additional_charges: [] as AdditionalCharge[],
    special_conditions: {
        en: props.quote.special_conditions?.en ?? '',
        ar: props.quote.special_conditions?.ar ?? '',
    },
    revision_note: '',
    email_subject_prefix: '',
});

function isChanged(field: string): boolean {
    const d = props.diff[field];
    if (! d) {
        return false;
    }
    return String(d.old) !== String(d.new);
}

function addCharge() {
    additionalCharges.value.push({ label: { en: '', ar: '' }, amount: '' });
}

function removeCharge(index: number) {
    additionalCharges.value.splice(index, 1);
}

function submit() {
    form.additional_charges = additionalCharges.value;
    form.email_subject_prefix = resolvedSubjectPrefix.value;
    form.post(quotesStoreRevision.url(props.quote.id));
}
</script>

<template>
    <Head :title="t('app.quotes.revise.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            variant="small"
            :title="t('app.quotes.revise.heading', { number: quote.quote_number ?? String(quote.id) })"
            :description="t('app.quotes.revise.description')"
        />

        <form class="max-w-3xl space-y-8" @submit.prevent="submit">
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
            </section>

            <!-- Financial Terms -->
            <section class="space-y-4">
                <h3 class="text-sm font-semibold">{{ t('app.quotes.create.financialTerms') }}</h3>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <div class="flex items-center gap-2">
                            <Label for="rent_amount">{{ t('app.quotes.create.rentAmount') }}</Label>
                            <Badge
                                v-if="isChanged('rent_amount')"
                                variant="outline"
                                class="text-xs"
                                :aria-label="t('app.quotes.revise.changedBadge')"
                            >
                                {{ t('app.quotes.revise.changedBadge') }}
                            </Badge>
                            <Badge
                                v-else
                                variant="secondary"
                                class="text-xs"
                                :aria-label="t('app.quotes.revise.unchangedBadge')"
                            >
                                {{ t('app.quotes.revise.unchangedBadge') }}
                            </Badge>
                        </div>
                        <Input
                            id="rent_amount"
                            v-model="form.rent_amount"
                            type="number"
                            step="0.01"
                            min="0"
                            :aria-label="
                                isChanged('rent_amount')
                                    ? `${t('app.quotes.create.rentAmount')}, ${t('app.quotes.revise.changedBadge')}`
                                    : t('app.quotes.create.rentAmount')
                            "
                        />
                        <p
                            v-if="isChanged('rent_amount') && diff.rent_amount"
                            class="text-muted-foreground text-xs"
                        >
                            {{ t('app.quotes.revise.wasValue', { value: diff.rent_amount.old }) }}
                        </p>
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
                        <div class="flex items-center gap-2">
                            <Label for="security_deposit">{{ t('app.quotes.create.securityDeposit') }}</Label>
                            <Badge
                                v-if="isChanged('security_deposit')"
                                variant="outline"
                                class="text-xs"
                            >
                                {{ t('app.quotes.revise.changedBadge') }}
                            </Badge>
                        </div>
                        <Input
                            id="security_deposit"
                            v-model="form.security_deposit"
                            type="number"
                            step="0.01"
                            min="0"
                        />
                        <p
                            v-if="isChanged('security_deposit') && diff.security_deposit"
                            class="text-muted-foreground text-xs"
                        >
                            {{ t('app.quotes.revise.wasValue', { value: diff.security_deposit.old }) }}
                        </p>
                        <InputError :message="form.errors.security_deposit" />
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

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="start_date">{{ t('app.quotes.create.startDate') }}</Label>
                        <Input id="start_date" v-model="form.start_date" type="date" />
                        <InputError :message="form.errors.start_date" />
                    </div>

                    <div class="grid gap-2">
                        <div class="flex items-center gap-2">
                            <Label for="valid_until">{{ t('app.quotes.create.validUntil') }}</Label>
                            <Badge
                                v-if="isChanged('valid_until')"
                                variant="outline"
                                class="text-xs"
                            >
                                {{ t('app.quotes.revise.changedBadge') }}
                            </Badge>
                        </div>
                        <Input id="valid_until" v-model="form.valid_until" type="date" />
                        <InputError :message="form.errors.valid_until" />
                    </div>
                </div>
            </section>

            <!-- Special Conditions -->
            <section class="space-y-4">
                <div class="grid gap-2">
                    <div class="flex items-center gap-2">
                        <Label for="special_conditions_en">{{ t('app.quotes.create.specialConditionsEn') }}</Label>
                        <Badge
                            v-if="isChanged('special_conditions')"
                            variant="outline"
                            class="text-xs"
                        >
                            {{ t('app.quotes.revise.changedBadge') }}
                        </Badge>
                    </div>
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
                    />
                    <InputError :message="form.errors['special_conditions.ar']" />
                </div>
            </section>

            <!-- Revision Note & Email Subject -->
            <section class="space-y-4">
                <div class="grid gap-2">
                    <Label for="revision_note">{{ t('app.quotes.revise.revisionNote') }}</Label>
                    <Textarea
                        id="revision_note"
                        v-model="form.revision_note"
                        rows="3"
                        :placeholder="t('app.quotes.revise.revisionNotePlaceholder')"
                    />
                    <InputError :message="form.errors.revision_note" />
                </div>

                <div class="grid gap-2">
                    <Label>{{ t('app.quotes.revise.subjectPrefixLabel') }}</Label>
                    <div class="flex flex-col gap-2">
                        <label class="flex items-center gap-2 text-sm">
                            <input
                                v-model="subjectPrefixMode"
                                type="radio"
                                value="updated_quote"
                                name="subject_prefix_mode"
                            />
                            {{ t('app.quotes.revise.subjectPrefixUpdatedQuote') }}
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input
                                v-model="subjectPrefixMode"
                                type="radio"
                                value="revised_offer"
                                name="subject_prefix_mode"
                            />
                            {{ t('app.quotes.revise.subjectPrefixRevisedOffer') }}
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input
                                v-model="subjectPrefixMode"
                                type="radio"
                                value="custom"
                                name="subject_prefix_mode"
                            />
                            {{ t('app.quotes.revise.subjectPrefixCustom') }}:
                            <Input
                                v-if="subjectPrefixMode === 'custom'"
                                v-model="customSubjectPrefix"
                                type="text"
                                class="ml-2 max-w-xs"
                            />
                        </label>
                    </div>
                    <InputError :message="form.errors.email_subject_prefix" />
                </div>
            </section>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <Button type="submit" :disabled="form.processing">
                    {{ t('app.quotes.revise.saveRevision') }}
                </Button>
                <Button
                    type="button"
                    variant="ghost"
                    :href="quotesShow.url(quote.id)"
                    as="a"
                >
                    {{ t('app.quotes.revise.cancel') }}
                </Button>
            </div>
        </form>
    </div>
</template>
