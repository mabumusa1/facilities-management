<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Transaction } from '@/types';

const props = defineProps<{ transaction: Transaction }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Transactions', href: '/transactions' }, { title: 'Details', href: '#' }] } });

function deleteTransaction() { if (confirm('Are you sure?')) { router.delete(`/transactions/${props.transaction.id}`); } }
</script>

<template>
    <Head :title="`Transaction #${transaction.id}`" />
    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Transaction #{{ transaction.id }}</h2>
                <p class="text-muted-foreground text-sm">{{ transaction.category?.name ?? '—' }} &middot; {{ transaction.type?.name ?? '—' }}</p>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child><a :href="`/transactions/${transaction.id}/edit`">Edit</a></Button>
                <Button variant="destructive" @click="deleteTransaction">Delete</Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-4">
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Amount</CardTitle></CardHeader><CardContent><div class="text-2xl font-bold">{{ transaction.amount }}</div></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Tax</CardTitle></CardHeader><CardContent><div class="text-2xl font-bold">{{ transaction.tax_amount ?? '0' }}</div></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Status</CardTitle></CardHeader><CardContent><Badge>{{ transaction.status?.name ?? '—' }}</Badge></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Due</CardTitle></CardHeader><CardContent><span>{{ transaction.due_date }}</span></CardContent></Card>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>Related</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">Lease</span><span>{{ transaction.lease?.contract_number ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Unit</span><span>{{ transaction.unit?.name ?? '—' }}</span></div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>Dates</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">Paid</span><span>{{ transaction.paid_date ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Created</span><span>{{ transaction.created_at }}</span></div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
