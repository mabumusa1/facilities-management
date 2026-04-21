<script setup lang="ts">
import { Head, router, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useI18n } from '@/composables/useI18n';
import type { Transaction } from '@/types';

const props = defineProps<{ transaction: Transaction }>();
const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.transactions.pageTitle'), href: '/transactions' },
            { title: t('app.transactions.show.breadcrumb'), href: '#' },
        ],
    });
});

function deleteTransaction() {
    if (confirm(t('app.transactions.show.confirmDeletePrompt'))) {
        router.delete(`/transactions/${props.transaction.id}`);
    }
}
</script>

<template>
    <Head :title="t('app.transactions.show.pageTitle', { id: transaction.id })" />
    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">{{ t('app.transactions.show.pageTitle', { id: transaction.id }) }}</h2>
                <p class="text-muted-foreground text-sm">{{ transaction.category?.name ?? '—' }} &middot; {{ transaction.type?.name ?? '—' }}</p>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child><a :href="`/transactions/${transaction.id}/edit`">{{ t('app.actions.edit') }}</a></Button>
                <Button variant="destructive" @click="deleteTransaction">{{ t('app.actions.delete') }}</Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-4">
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.transactions.show.amount') }}</CardTitle></CardHeader><CardContent><div class="text-2xl font-bold">{{ transaction.amount }}</div></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.transactions.show.tax') }}</CardTitle></CardHeader><CardContent><div class="text-2xl font-bold">{{ transaction.tax_amount ?? '0' }}</div></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.transactions.show.status') }}</CardTitle></CardHeader><CardContent><Badge>{{ transaction.status?.name ?? '—' }}</Badge></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.transactions.show.due') }}</CardTitle></CardHeader><CardContent><span>{{ transaction.due_date }}</span></CardContent></Card>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>{{ t('app.transactions.show.related') }}</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.transactions.show.lease') }}</span><span>{{ transaction.lease?.contract_number ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.transactions.show.unit') }}</span><span>{{ transaction.unit?.name ?? '—' }}</span></div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>{{ t('app.transactions.show.dates') }}</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.transactions.show.paid') }}</span><span>{{ transaction.paid_date ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.transactions.show.created') }}</span><span>{{ transaction.created_at }}</span></div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
