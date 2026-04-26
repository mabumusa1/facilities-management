<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { convert as quotesConvert, index as quotesIndex, preview as quotesPreview, send as quotesSend } from '@/actions/App/Http/Controllers/Leasing/QuoteController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();

type AdditionalCharge = {
    label: { en: string; ar: string };
    amount: string | number;
};

type QuoteDetail = {
    id: number;
    quote_number: string | null;
    public_token: string | null;
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
    version: number;
    created_at: string;
};

const props = defineProps<{
    quote: QuoteDetail;
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.quotes.pageTitle'), href: quotesIndex.url() },
            { title: props.quote.quote_number ?? `#${props.quote.id}`, href: '#' },
        ],
    });
});

function sendQuote() {
    if (confirm(t('app.quotes.show.confirmSend'))) {
        router.post(quotesSend.url(props.quote.id));
    }
}

function previewUrl(): string | null {
    if (! props.quote.public_token) {
        return null;
    }

    return quotesPreview.url(props.quote.public_token);
}
</script>

<template>
    <Head :title="t('app.quotes.show.pageTitle', { number: quote.quote_number ?? String(quote.id) })" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">
                    {{ quote.quote_number ?? `#${quote.id}` }}
                </h2>
                <p class="text-muted-foreground text-sm">
                    {{ quote.contact ? `${quote.contact.first_name} ${quote.contact.last_name}` : '—' }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <Badge v-if="quote.status" variant="secondary">
                    {{ quote.status.name_en ?? quote.status.name }}
                </Badge>

                <Link
                    v-if="quote.status?.name_en === 'accepted'"
                    :href="quotesConvert.url(quote.id)"
                    class="inline-flex items-center gap-1 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                >
                    {{ t('app.quotes.convert.title') }} &rarr;
                </Link>

                <Button
                    v-if="quote.status?.name_en === 'draft' || quote.status?.name_en === 'sent'"
                    @click="sendQuote"
                >
                    {{ t('app.quotes.create.send') }}
                </Button>

                <a
                    v-if="previewUrl()"
                    :href="previewUrl()!"
                    target="_blank"
                    class="inline-flex items-center rounded-md border px-4 py-2 text-sm font-medium hover:bg-accent"
                >
                    {{ t('app.quotes.show.previewLink') }}
                </a>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('app.quotes.show.quoteDetails') }}</CardTitle>
                </CardHeader>
                <CardContent class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">{{ t('app.quotes.create.unitLabel') }}</span>
                        <span>{{ quote.unit?.name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">{{ t('app.quotes.create.contractType') }}</span>
                        <span>{{ quote.contract_type?.name_en ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">{{ t('app.quotes.create.duration') }}</span>
                        <span>{{ quote.duration_months }} {{ t('app.quotes.show.months') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">{{ t('app.quotes.create.startDate') }}</span>
                        <span>{{ quote.start_date }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">{{ t('app.quotes.create.validUntil') }}</span>
                        <span>{{ quote.valid_until }}</span>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>{{ t('app.quotes.create.financialTerms') }}</CardTitle>
                </CardHeader>
                <CardContent class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">{{ t('app.quotes.create.rentAmount') }}</span>
                        <span>{{ quote.rent_amount }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">{{ t('app.quotes.create.paymentFrequency') }}</span>
                        <span>{{ quote.payment_frequency?.name_en ?? quote.payment_frequency?.name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">{{ t('app.quotes.create.securityDeposit') }}</span>
                        <span>{{ quote.security_deposit }}</span>
                    </div>
                </CardContent>
            </Card>
        </div>

        <Card v-if="quote.additional_charges && quote.additional_charges.length > 0">
            <CardHeader>
                <CardTitle>{{ t('app.quotes.create.additionalCharges') }}</CardTitle>
            </CardHeader>
            <CardContent>
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
            </CardContent>
        </Card>

        <Card v-if="quote.special_conditions?.en || quote.special_conditions?.ar">
            <CardHeader>
                <CardTitle>{{ t('app.quotes.show.specialConditions') }}</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4 text-sm">
                <div v-if="quote.special_conditions.en">
                    <p class="text-muted-foreground mb-1 font-medium">EN</p>
                    <p class="whitespace-pre-wrap">{{ quote.special_conditions.en }}</p>
                </div>
                <div v-if="quote.special_conditions.ar" dir="rtl">
                    <p class="text-muted-foreground mb-1 font-medium">AR</p>
                    <p class="whitespace-pre-wrap">{{ quote.special_conditions.ar }}</p>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
