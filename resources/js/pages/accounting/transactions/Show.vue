<script setup lang="ts">
import { Head, Link, router, setLayoutProps, useForm } from '@inertiajs/vue3';
import { downloadReceipt, sendReceipt as sendReceiptRoute } from '@/actions/App/Http/Controllers/Accounting/TransactionController';
import { computed, ref, watchEffect } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import WarningBanner from '@/components/WarningBanner.vue';
import { useI18n } from '@/composables/useI18n';
import type { Transaction } from '@/types';

interface Receipt {
    id: number;
    status: 'generated' | 'settings_incomplete';
    pdf_path: string | null;
    sent_at: string | null;
    sent_to_name: string | null;
    sent_to_email: string | null;
}

interface Assignee {
    id: number;
    first_name?: string;
    last_name?: string;
    name?: string;
    email?: string;
}

const props = defineProps<{
    transaction: Transaction & {
        receipt?: Receipt | null;
        assignee?: Assignee | null;
        payment_method?: string | null;
        reference_number?: string | null;
    };
    invoiceSettingComplete: boolean;
}>();

const { t } = useI18n();
const showSendDialog = ref(false);
const sendForm = useForm({});

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.transactions.pageTitle'), href: '/transactions' },
            { title: t('app.transactions.show.breadcrumb'), href: '#' },
        ],
    });
});

const receipt = computed(() => props.transaction.receipt ?? null);

const hasGeneratedReceipt = computed(
    () => receipt.value !== null && receipt.value.status === 'generated',
);

const receiptIsBlocked = computed(
    () => receipt.value === null || receipt.value.status === 'settings_incomplete',
);

const payerName = computed(() => {
    const a = props.transaction.assignee;

    if (! a) {
        return '—';
    }

    if (a.name) {
        return a.name;
    }

    return [a.first_name, a.last_name].filter(Boolean).join(' ') || '—';
});

const payerEmail = computed(() => props.transaction.assignee?.email ?? '—');

const sendButtonLabel = computed(() =>
    receipt.value?.sent_at
        ? t('app.transactions.show.resendReceipt')
        : t('app.transactions.show.sendReceipt'),
);

function deleteTransaction() {
    if (confirm(t('app.transactions.show.confirmDeletePrompt'))) {
        router.delete(`/transactions/${props.transaction.id}`);
    }
}

function confirmSend() {
    showSendDialog.value = false;
    sendForm.post(sendReceiptRoute.url(props.transaction.id));
}

function formatPaymentMethod(method: string | null | undefined): string {
    if (! method) {
        return '—';
    }

    const map: Record<string, string> = {
        cash: t('app.transactions.create.cash'),
        bank_transfer: t('app.transactions.create.bankTransfer'),
        cheque: t('app.transactions.create.cheque'),
    };

    return map[method] ?? method;
}
</script>

<template>
    <Head :title="t('app.transactions.show.pageTitle', { id: transaction.id })" />
    <div class="flex flex-col gap-6 p-4">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">
                    {{ t('app.transactions.show.pageTitle', { id: transaction.id }) }}
                </h2>
                <p class="text-muted-foreground text-sm">
                    {{ transaction.category?.name_en ?? transaction.category?.name ?? '—' }}
                    &middot;
                    {{ formatPaymentMethod((transaction as any).payment_method) }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child>
                    <a :href="`/transactions/${transaction.id}/edit`">{{ t('app.actions.edit') }}</a>
                </Button>
                <Button variant="destructive" @click="deleteTransaction">{{ t('app.actions.delete') }}</Button>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid gap-4 md:grid-cols-4">
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium">{{ t('app.transactions.show.amount') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ transaction.amount }}</div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium">{{ t('app.transactions.show.tax') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ transaction.tax_amount ?? '0' }}</div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium">{{ t('app.transactions.show.status') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <Badge>{{ transaction.status?.name_en ?? transaction.status?.name ?? '—' }}</Badge>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium">{{ t('app.transactions.show.due') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <span>{{ transaction.due_date }}</span>
                </CardContent>
            </Card>
        </div>

        <!-- Related + Payer -->
        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>{{ t('app.transactions.show.related') }}</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">{{ t('app.transactions.show.lease') }}</span>
                        <span>{{ transaction.lease?.contract_number ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">{{ t('app.transactions.show.unit') }}</span>
                        <span>{{ transaction.unit?.name ?? '—' }}</span>
                    </div>
                    <div v-if="(transaction as any).reference_number" class="flex justify-between">
                        <span class="text-muted-foreground">{{ t('app.transactions.show.referenceNumber') }}</span>
                        <span dir="ltr">{{ (transaction as any).reference_number }}</span>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>{{ t('app.transactions.show.payerCard') }}</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">{{ t('app.transactions.show.payerName') }}</span>
                        <span>{{ payerName }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">{{ t('app.transactions.show.paymentMethod') }}</span>
                        <span>{{ formatPaymentMethod((transaction as any).payment_method) }}</span>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Receipt Card -->
        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.transactions.show.receipt') }}</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
                <!-- Generated state -->
                <template v-if="hasGeneratedReceipt">
                    <div class="flex items-center gap-3">
                        <Badge>{{ t('app.transactions.show.receiptGenerated') }}</Badge>
                        <a
                            v-if="receipt?.pdf_path"
                            :href="downloadReceipt.url(transaction.id)"
                            class="text-sm underline underline-offset-4"
                            :aria-label="t('app.transactions.show.downloadPdf') + ' for Transaction #' + transaction.id"
                        >
                            {{ t('app.transactions.show.downloadPdf') }}
                        </a>
                    </div>

                    <p
                        v-if="receipt?.sent_at"
                        aria-live="polite"
                        class="text-muted-foreground text-sm"
                    >
                        {{ t('app.transactions.show.lastSent', { date: receipt.sent_at }) }}
                    </p>

                    <Button
                        :disabled="sendForm.processing"
                        @click="showSendDialog = true"
                    >
                        {{ sendButtonLabel }}
                    </Button>
                </template>

                <!-- Blocked / settings incomplete state -->
                <template v-else>
                    <div class="flex items-center gap-3">
                        <Badge variant="outline">{{ t('app.transactions.show.settingsIncomplete') }}</Badge>
                    </div>

                    <WarningBanner
                        :message="t('app.transactions.show.invoiceIncompleteMessage')"
                        :link-href="'/app-settings/invoice'"
                        :link-label="t('app.transactions.show.configureSettings')"
                    />

                    <Button
                        disabled
                        aria-disabled="true"
                        :title="t('app.transactions.show.invoiceIncompleteMessage')"
                    >
                        {{ t('app.transactions.show.sendReceipt') }}
                    </Button>
                </template>
            </CardContent>
        </Card>

        <!-- Dates -->
        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>{{ t('app.transactions.show.dates') }}</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">{{ t('app.transactions.show.paid') }}</span>
                        <span>{{ transaction.due_date ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">{{ t('app.transactions.show.created') }}</span>
                        <span>{{ transaction.created_at }}</span>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Send Receipt Dialog -->
        <Dialog v-model:open="showSendDialog">
            <DialogContent role="dialog" :aria-labelledby="'send-dialog-title'">
                <DialogHeader>
                    <DialogTitle id="send-dialog-title">
                        {{ t('app.transactions.show.sendReceiptTitle') }}
                    </DialogTitle>
                    <DialogDescription>
                        {{
                            t('app.transactions.show.sendReceiptBody', {
                                id: String(transaction.id),
                                name: payerName,
                                email: payerEmail,
                            })
                        }}
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="showSendDialog = false">
                        {{ t('app.transactions.show.sendReceiptCancel') }}
                    </Button>
                    <Button :disabled="sendForm.processing" @click="confirmSend">
                        {{ t('app.transactions.show.sendReceiptConfirm') }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
