<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useI18n } from '@/composables/useI18n';
import { activate, cancel, cancelNow, index, show } from '@/routes/admin/subscriptions';

const { t } = useI18n();

type Plan = {
    slug: string;
    name: string;
    price: number;
    currency: string;
    invoice_period: number;
    invoice_interval: string;
    trial_period: number;
    trial_interval: string;
};

type AccountSubscription = {
    id: number;
    name: string;
    domain: string | null;
    subscription: {
        id: number;
        active: boolean;
        canceled: boolean;
        ended: boolean;
        on_trial: boolean;
        starts_at: string | null;
        trial_ends_at: string | null;
        ends_at: string | null;
        canceled_at: string | null;
    } | null;
};

defineProps<{
    plan: Plan;
    accounts: AccountSubscription[];
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.accountSubscriptions'), href: index.url() },
        ],
    });
});

function activateSubscription(tenantId: number) {
    router.post(activate.url({ tenant: tenantId }), {}, { preserveScroll: true });
}

function cancelSubscription(tenantId: number) {
    router.post(cancel.url({ tenant: tenantId }), {}, { preserveScroll: true });
}

function cancelSubscriptionNow(tenantId: number) {
    router.post(cancelNow.url({ tenant: tenantId }), {}, { preserveScroll: true });
}

function accountStatus(account: AccountSubscription): string {
    if (!account.subscription) {
        return t('app.admin.subscriptions.notSubscribed');
    }

    if (account.subscription.active) {
        return account.subscription.on_trial
            ? t('app.admin.subscriptions.onTrial')
            : t('app.admin.subscriptions.active');
    }

    if (account.subscription.ended) {
        return t('app.admin.subscriptions.ended');
    }

    if (account.subscription.canceled) {
        return t('app.admin.subscriptions.canceled');
    }

    return t('app.admin.subscriptions.inactive');
}
</script>

<template>
    <Head :title="t('app.admin.subscriptions.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            variant="small"
            :title="t('app.admin.subscriptions.heading')"
            :description="t('app.admin.subscriptions.description')"
        />

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.admin.subscriptions.defaultPlan') }}</CardTitle>
            </CardHeader>
            <CardContent class="grid gap-2 text-sm text-muted-foreground md:grid-cols-2">
                <p>
                    <span class="font-semibold text-foreground">{{ t('app.admin.subscriptions.planName') }}:</span>
                    {{ plan.name }}
                </p>
                <p>
                    <span class="font-semibold text-foreground">{{ t('app.admin.subscriptions.price') }}:</span>
                    {{ plan.price }} {{ plan.currency }} / {{ plan.invoice_interval }}
                </p>
                <p>
                    <span class="font-semibold text-foreground">{{ t('app.admin.subscriptions.trial') }}:</span>
                    {{ plan.trial_period }} {{ plan.trial_interval }}
                </p>
                <p>
                    <span class="font-semibold text-foreground">{{ t('app.admin.subscriptions.slug') }}:</span>
                    {{ plan.slug }}
                </p>
            </CardContent>
        </Card>

        <div class="rounded-lg border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>{{ t('app.admin.subscriptions.account') }}</TableHead>
                        <TableHead>{{ t('app.admin.subscriptions.domain') }}</TableHead>
                        <TableHead>{{ t('app.admin.subscriptions.status') }}</TableHead>
                        <TableHead>{{ t('app.admin.subscriptions.periodEndsAt') }}</TableHead>
                        <TableHead>{{ t('app.admin.subscriptions.actions') }}</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="account in accounts" :key="account.id">
                        <TableCell>
                            <Link :href="show.url({ tenant: account.id })" class="text-primary hover:underline">
                                {{ account.name }}
                            </Link>
                        </TableCell>
                        <TableCell>{{ account.domain ?? '—' }}</TableCell>
                        <TableCell>
                            <Badge variant="secondary">{{ accountStatus(account) }}</Badge>
                        </TableCell>
                        <TableCell>{{ account.subscription?.ends_at ?? '—' }}</TableCell>
                        <TableCell>
                            <div class="flex flex-wrap gap-2">
                                <Button size="sm" @click="activateSubscription(account.id)">
                                    {{ t('app.admin.subscriptions.activate') }}
                                </Button>
                                <Button size="sm" variant="outline" @click="cancelSubscription(account.id)">
                                    {{ t('app.admin.subscriptions.cancelAtEnd') }}
                                </Button>
                                <Button size="sm" variant="destructive" @click="cancelSubscriptionNow(account.id)">
                                    {{ t('app.admin.subscriptions.cancelNow') }}
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="accounts.length === 0">
                        <TableCell :colspan="5" class="text-center text-muted-foreground">
                            {{ t('app.admin.subscriptions.empty') }}
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </div>
</template>
