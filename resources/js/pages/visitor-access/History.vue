<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Visitor Access', href: '/visitor-access/history' },
        ],
    },
});

const props = defineProps<{
    visits: {
        data: Array<{
            id: number;
            visitor_name: string | null;
            visitor_phone: string | null;
            scheduled_at: string | null;
            marketplace_unit?: { unit?: { name?: string | null } | null } | null;
            status?: { name?: string | null; name_en?: string | null } | null;
        }>;
    };
}>();
</script>

<template>
    <Head title="Visitor Access History" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" title="Visitor Access History" description="Review all visitor access records and open visitor details." />

        <Card>
            <CardHeader>
                <CardTitle>History</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3">
                <div v-for="visit in props.visits.data" :key="visit.id" class="flex items-center justify-between rounded-md border p-3">
                    <div class="text-sm">
                        <p class="font-medium">{{ visit.visitor_name ?? `Visit #${visit.id}` }}</p>
                        <p>Phone: {{ visit.visitor_phone ?? 'N/A' }}</p>
                        <p>Unit: {{ visit.marketplace_unit?.unit?.name ?? 'N/A' }}</p>
                        <p>Scheduled: {{ visit.scheduled_at ?? 'N/A' }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <Badge variant="secondary">{{ visit.status?.name_en ?? visit.status?.name ?? 'Unknown' }}</Badge>
                        <Button size="sm" as-child>
                            <Link :href="`/visitor-access/visitor-details/${visit.id}`">Details</Link>
                        </Button>
                    </div>
                </div>
                <p v-if="props.visits.data.length === 0" class="text-muted-foreground text-sm">No visitor access records found.</p>
            </CardContent>
        </Card>
    </div>
</template>
