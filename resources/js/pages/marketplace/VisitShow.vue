<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const props = defineProps<{
    visit: {
        id: number;
        visitor_name: string | null;
        visitor_phone: string | null;
        scheduled_at: string | null;
        notes: string | null;
        status?: { name?: string | null; name_en?: string | null } | null;
        marketplace_unit?: { unit?: { name?: string | null } | null } | null;
    };
}>();

function cancelVisit() {
    router.post(`/marketplace/visits/${props.visit.id}/cancel`);
}

function sendContract() {
    router.post(`/marketplace/visits/${props.visit.id}/send-contract`);
}

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Marketplace', href: '/marketplace' },
            { title: 'Visits', href: '/marketplace/visits' },
        ],
    },
});
</script>

<template>
    <Head :title="`Visit #${props.visit.id}`" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading variant="small" :title="`Visit #${props.visit.id}`" description="Marketplace visit details." />
            <div class="flex gap-2">
                <Button variant="outline" as-child>
                    <Link href="/marketplace/visits">Back</Link>
                </Button>
                <Button variant="outline" @click="sendContract">Send Contract</Button>
                <Button variant="destructive" @click="cancelVisit">Cancel Visit</Button>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Visit Information</CardTitle>
            </CardHeader>
            <CardContent class="grid gap-2 text-sm md:grid-cols-2">
                <p><span class="font-medium">Visitor:</span> {{ props.visit.visitor_name ?? 'N/A' }}</p>
                <p><span class="font-medium">Phone:</span> {{ props.visit.visitor_phone ?? 'N/A' }}</p>
                <p><span class="font-medium">Unit:</span> {{ props.visit.marketplace_unit?.unit?.name ?? 'N/A' }}</p>
                <p><span class="font-medium">Scheduled At:</span> {{ props.visit.scheduled_at ?? 'N/A' }}</p>
                <p>
                    <span class="font-medium">Status:</span>
                    <Badge variant="secondary">{{ props.visit.status?.name_en ?? props.visit.status?.name ?? 'Unknown' }}</Badge>
                </p>
                <p class="md:col-span-2"><span class="font-medium">Notes:</span> {{ props.visit.notes ?? 'N/A' }}</p>
            </CardContent>
        </Card>
    </div>
</template>
