<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Lease } from '@/types';

const props = defineProps<{ lease: Lease }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Leases', href: '/leases' }, { title: 'Details', href: '#' }] } });

function deleteLease() { if (confirm('Are you sure?')) { router.delete(`/leases/${props.lease.id}`); } }
</script>

<template>
    <Head :title="`Lease ${lease.contract_number}`" />
    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">{{ lease.contract_number }}</h2>
                <p class="text-muted-foreground text-sm">{{ lease.tenant?.name ?? `${lease.tenant?.first_name} ${lease.tenant?.last_name}` }}</p>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child><Link :href="`/leases/${lease.id}/edit`">Edit</Link></Button>
                <Button variant="destructive" @click="deleteLease">Delete</Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-4">
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Status</CardTitle></CardHeader><CardContent><Badge>{{ lease.status?.name ?? '—' }}</Badge></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Total Amount</CardTitle></CardHeader><CardContent><div class="text-2xl font-bold">{{ lease.rental_total_amount }}</div></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Unpaid</CardTitle></CardHeader><CardContent><div class="text-2xl font-bold text-destructive">{{ lease.total_unpaid_amount ?? '0' }}</div></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">Type</CardTitle></CardHeader><CardContent><Badge variant="secondary">{{ lease.tenant_type }}</Badge></CardContent></Card>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>Duration</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">Start</span><span>{{ lease.start_date }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">End</span><span>{{ lease.end_date }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Handover</span><span>{{ lease.handover_date }}</span></div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>Financial</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">Rental Type</span><span>{{ lease.rental_type }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Security Deposit</span><span>{{ lease.security_deposit_amount ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Sub Lease</span><Badge :variant="lease.is_sub_lease ? 'default' : 'secondary'">{{ lease.is_sub_lease ? 'Yes' : 'No' }}</Badge></div>
                </CardContent>
            </Card>
        </div>

        <Card v-if="lease.units && lease.units.length > 0">
            <CardHeader><CardTitle>Units</CardTitle></CardHeader>
            <CardContent>
                <div class="space-y-2">
                    <Link v-for="unit in lease.units" :key="unit.id" :href="`/units/${unit.id}`" class="flex items-center justify-between rounded-md border p-3 hover:bg-muted/50">
                        <span class="font-medium">{{ unit.name }}</span>
                    </Link>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
