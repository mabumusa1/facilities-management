<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import {
    convert as quotesConvert,
    index as quotesIndex,
    show as quotesShow,
    send as quotesSend,
    preview as quotesPreview,
    revise as quotesRevise,
    reject as quotesReject,
    expire as quotesExpire,
} from '@/actions/App/Http/Controllers/Leasing/QuoteController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();

// Terminal status name_en values — used only for the readonly message (not for action gating).
const TERMINAL_STATUS_ACCEPTED = 'accepted';
const TERMINAL_STATUS_REJECTED = 'rejected';
const TERMINAL_STATUS_EXPIRED = 'expired';

type AdditionalCharge = {
    label: { en: string; ar: string };
    amount: string | number;
};

type RevisionItem = {
    id: number;
    quote_number: string | null;
    version: number;
    status: { id: number; name: string; name_en: string | null } | null;
    created_at: string;
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
    revisions: RevisionItem[];
    parent_quote: RevisionItem | null;
};

const props = defineProps<{
    quote: QuoteDetail;
    can: {
        send: boolean;
        revise: boolean;
        reject: boolean;
        expire: boolean;
    };
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

const statusNameEn = computed(() => props.quote.status?.name_en ?? '');

const readonlyMessage = computed(() => {
    if (statusNameEn.value === TERMINAL_STATUS_ACCEPTED) {
        return t('app.quotes.show.acceptedReadonly');
    }
    if (statusNameEn.value === TERMINAL_STATUS_EXPIRED) {
        return t('app.quotes.show.expiredReadonly');
    }
    if (statusNameEn.value === TERMINAL_STATUS_REJECTED) {
        return t('app.quotes.show.rejectedReadonly');
    }
    return null;
});

const hasRevisionHistory = computed(() =>
    props.quote.parent_quote !== null || props.quote.revisions.length > 0,
);

const latestRevisionVersion = computed(() => {
    if (props.quote.revisions.length > 0) {
        return Math.max(...props.quote.revisions.map((r) => r.version));
    }
    return props.quote.version;
});

function sendQuote() {
    if (confirm(t('app.quotes.show.confirmSend'))) {
        router.post(quotesSend.url(props.quote.id));
    }
}

function rejectQuote() {
    if (confirm(t('app.quotes.show.confirmReject'))) {
        router.post(quotesReject.url(props.quote.id));
    }
}

function expireQuote() {
    if (confirm(t('app.quotes.show.confirmExpire'))) {
        router.patch(quotesExpire.url(props.quote.id));
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

            <div class="flex flex-wrap items-center gap-2">
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
                    v-if="can.send"
                    @click="sendQuote"
                >
                    {{ t('app.quotes.create.send') }}
                </Button>

                <Button
                    v-if="can.revise"
                    variant="outline"
                    as="a"
                    :href="quotesRevise.url(quote.id)"
                >
                    {{ t('app.quotes.action.revise') }}
                </Button>

                <Button
                    v-if="can.reject"
                    variant="destructive"
                    @click="rejectQuote"
                >
                    {{ t('app.quotes.action.markRejected') }}
                </Button>

                <Button
                    v-if="can.expire"
                    variant="ghost"
                    @click="expireQuote"
                >
                    {{ t('app.quotes.action.expireQuote') }}
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

        <!-- Read-only banner for terminal statuses -->
        <div
            v-if="readonlyMessage"
            role="status"
            class="bg-muted rounded-md p-3 text-sm"
        >
            {{ readonlyMessage }}
        </div>

        <!-- Revision history -->
        <Card v-if="hasRevisionHistory">
            <CardHeader>
                <CardTitle>{{ t('app.quotes.show.revisionHistory') }}</CardTitle>
            </CardHeader>
            <CardContent class="text-sm">
                <p class="text-muted-foreground mb-3 text-xs">
                    {{ t('app.quotes.show.latestVisible', { version: latestRevisionVersion }) }}
                </p>

                <table class="w-full">
                    <tbody>
                        <!-- Parent quote row if this is a revision -->
                        <tr
                            v-if="quote.parent_quote"
                            class="hover:bg-muted/50 border-b"
                            tabindex="0"
                        >
                            <td class="py-2 font-medium">v{{ quote.parent_quote.version }}</td>
                            <td class="py-2">
                                <Badge variant="secondary">
                                    {{ quote.parent_quote.status?.name_en ?? quote.parent_quote.status?.name ?? '—' }}
                                </Badge>
                            </td>
                            <td class="py-2">{{ quote.parent_quote.created_at }}</td>
                            <td class="py-2 text-end">
                                <Link
                                    :href="quotesShow.url(quote.parent_quote.id)"
                                    class="text-primary text-xs underline"
                                >
                                    {{ quote.parent_quote.quote_number ?? `#${quote.parent_quote.id}` }}
                                </Link>
                            </td>
                        </tr>

                        <!-- Current quote row -->
                        <tr class="bg-muted/30 border-b font-medium" tabindex="0">
                            <td class="py-2">v{{ quote.version }}</td>
                            <td class="py-2">
                                <Badge variant="secondary">
                                    {{ quote.status?.name_en ?? quote.status?.name ?? '—' }}
                                </Badge>
                            </td>
                            <td class="py-2">{{ quote.created_at }}</td>
                            <td class="py-2 text-end text-xs">
                                {{ quote.quote_number ?? `#${quote.id}` }}
                            </td>
                        </tr>

                        <!-- Child revisions -->
                        <tr
                            v-for="revision in quote.revisions"
                            :key="revision.id"
                            class="hover:bg-muted/50 border-b"
                            tabindex="0"
                        >
                            <td class="py-2 font-medium">v{{ revision.version }}</td>
                            <td class="py-2">
                                <Badge variant="secondary">
                                    {{ revision.status?.name_en ?? revision.status?.name ?? '—' }}
                                </Badge>
                            </td>
                            <td class="py-2">{{ revision.created_at }}</td>
                            <td class="py-2 text-end">
                                <Link
                                    :href="quotesShow.url(revision.id)"
                                    class="text-primary text-xs underline"
                                >
                                    {{ revision.quote_number ?? `#${revision.id}` }}
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </div>
</template>
