<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();

type AdditionalCharge = {
    label: { en: string; ar: string };
    amount: string | number;
};

type QuotePreview = {
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
    additional_charges: AdditionalCharge[] | null;
};

defineProps<{
    quote: QuotePreview;
}>();
</script>

<template>
    <Head :title="t('app.quotes.preview.pageTitle', { number: quote.quote_number ?? String(quote.id) })" />

    <div class="min-h-screen bg-gray-50 py-10">
        <div class="mx-auto max-w-3xl rounded-lg bg-white p-8 shadow">
            <header class="mb-8 border-b pb-6">
                <h1
                    ref="headingRef"
                    tabindex="-1"
                    class="text-3xl font-bold tracking-tight"
                >
                    {{ t('app.quotes.preview.heading') }}
                </h1>
                <p class="text-muted-foreground mt-1 text-sm">
                    {{ t('app.quotes.preview.quoteNumber', { number: quote.quote_number ?? String(quote.id) }) }}
                </p>
            </header>

            <section class="mb-6 grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-muted-foreground text-xs font-semibold uppercase tracking-wide">
                        {{ t('app.quotes.create.residentLabel') }}
                    </p>
                    <p class="mt-1 font-medium">
                        {{ quote.contact ? `${quote.contact.first_name} ${quote.contact.last_name}` : '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-muted-foreground text-xs font-semibold uppercase tracking-wide">
                        {{ t('app.quotes.create.unitLabel') }}
                    </p>
                    <p class="mt-1 font-medium">{{ quote.unit?.name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-muted-foreground text-xs font-semibold uppercase tracking-wide">
                        {{ t('app.quotes.create.contractType') }}
                    </p>
                    <p class="mt-1 font-medium">{{ quote.contract_type?.name_en ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-muted-foreground text-xs font-semibold uppercase tracking-wide">
                        {{ t('app.quotes.create.duration') }}
                    </p>
                    <p class="mt-1 font-medium">{{ quote.duration_months }} {{ t('app.quotes.show.months') }}</p>
                </div>
                <div>
                    <p class="text-muted-foreground text-xs font-semibold uppercase tracking-wide">
                        {{ t('app.quotes.create.startDate') }}
                    </p>
                    <p class="mt-1 font-medium">{{ quote.start_date }}</p>
                </div>
                <div>
                    <p class="text-muted-foreground text-xs font-semibold uppercase tracking-wide">
                        {{ t('app.quotes.create.validUntil') }}
                    </p>
                    <p class="mt-1 font-medium">{{ quote.valid_until }}</p>
                </div>
            </section>

            <section class="mb-6 rounded-lg border bg-gray-50 p-4">
                <h2 class="mb-4 text-lg font-semibold">{{ t('app.quotes.create.financialTerms') }}</h2>
                <div class="grid gap-3 sm:grid-cols-3">
                    <div>
                        <p class="text-muted-foreground text-xs">{{ t('app.quotes.create.rentAmount') }}</p>
                        <p class="text-xl font-bold">{{ quote.rent_amount }}</p>
                    </div>
                    <div>
                        <p class="text-muted-foreground text-xs">{{ t('app.quotes.create.paymentFrequency') }}</p>
                        <p class="font-medium">
                            {{ quote.payment_frequency?.name_en ?? quote.payment_frequency?.name ?? '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-muted-foreground text-xs">{{ t('app.quotes.create.securityDeposit') }}</p>
                        <p class="font-medium">{{ quote.security_deposit }}</p>
                    </div>
                </div>
            </section>

            <section
                v-if="quote.additional_charges && quote.additional_charges.length > 0"
                class="mb-6"
            >
                <h2 class="mb-3 text-lg font-semibold">{{ t('app.quotes.create.additionalCharges') }}</h2>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="pb-2 text-start font-medium">{{ t('app.quotes.create.chargeLabelEn') }}</th>
                            <th class="pb-2 text-start font-medium">{{ t('app.quotes.create.chargeLabelAr') }}</th>
                            <th class="pb-2 text-end font-medium">{{ t('app.quotes.create.chargeAmount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(charge, index) in quote.additional_charges"
                            :key="index"
                            class="border-b last:border-0"
                        >
                            <td class="py-2">{{ charge.label.en }}</td>
                            <td class="py-2" dir="rtl">{{ charge.label.ar }}</td>
                            <td class="py-2 text-end">{{ charge.amount }}</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <section
                v-if="quote.special_conditions?.en || quote.special_conditions?.ar"
                class="mb-6"
            >
                <h2 class="mb-3 text-lg font-semibold">{{ t('app.quotes.show.specialConditions') }}</h2>
                <div v-if="quote.special_conditions.en" class="mb-4">
                    <p class="text-muted-foreground mb-1 text-xs font-semibold uppercase">EN</p>
                    <p class="whitespace-pre-wrap text-sm">{{ quote.special_conditions.en }}</p>
                </div>
                <div v-if="quote.special_conditions.ar" dir="rtl">
                    <p class="text-muted-foreground mb-1 text-xs font-semibold uppercase">AR</p>
                    <p class="whitespace-pre-wrap text-sm">{{ quote.special_conditions.ar }}</p>
                </div>
            </section>

            <footer class="border-t pt-4">
                <p
                    class="text-muted-foreground text-xs"
                    role="status"
                    aria-live="polite"
                >
                    {{ t('app.quotes.preview.footer') }}
                </p>
            </footer>
        </div>
    </div>
</template>
