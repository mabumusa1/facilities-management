<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Marketplace', href: '/marketplace' },
            { title: 'Visits', href: '/marketplace/visits' },
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
            notes: string | null;
            marketplace_unit?: { unit?: { name?: string | null } | null } | null;
            status?: { name?: string | null; name_en?: string | null } | null;
        }>;
    };
    listings: Array<{
        id: number;
        unit?: { name?: string | null } | null;
    }>;
}>();

const scheduleForm = useForm({
    marketplace_unit_id: 0,
    visitor_name: '',
    visitor_phone: '',
    scheduled_at: '',
    notes: '',
});

function schedule() {
    scheduleForm.post('/marketplace/visits/schedule', {
        preserveScroll: true,
        onSuccess: () => scheduleForm.reset(),
    });
}

function cancelVisit(id: number) {
    router.post(`/marketplace/visits/${id}/cancel`);
}

function sendContract(id: number) {
    router.post(`/marketplace/visits/${id}/send-contract`);
}
</script>

<template>
    <Head title="Marketplace Visits" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" title="Marketplace Visits" description="Schedule and manage property viewings." />

        <Card>
            <CardHeader>
                <CardTitle>Schedule Visit</CardTitle>
            </CardHeader>
            <CardContent>
                <form class="grid gap-4 md:grid-cols-2" @submit.prevent="schedule">
                    <div class="grid gap-2">
                        <Label for="marketplace_unit_id">Listing</Label>
                        <select id="marketplace_unit_id" v-model.number="scheduleForm.marketplace_unit_id" class="rounded-md border border-input bg-background px-3 py-2">
                            <option :value="0">Select listing</option>
                            <option v-for="listing in props.listings" :key="listing.id" :value="listing.id">
                                {{ listing.unit?.name ?? `Listing #${listing.id}` }}
                            </option>
                        </select>
                        <InputError :message="scheduleForm.errors.marketplace_unit_id" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="visitor_name">Visitor Name</Label>
                        <Input id="visitor_name" v-model="scheduleForm.visitor_name" />
                        <InputError :message="scheduleForm.errors.visitor_name" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="visitor_phone">Visitor Phone</Label>
                        <Input id="visitor_phone" v-model="scheduleForm.visitor_phone" />
                        <InputError :message="scheduleForm.errors.visitor_phone" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="scheduled_at">Scheduled At</Label>
                        <Input id="scheduled_at" v-model="scheduleForm.scheduled_at" type="datetime-local" />
                        <InputError :message="scheduleForm.errors.scheduled_at" />
                    </div>
                    <div class="grid gap-2 md:col-span-2">
                        <Label for="visit_notes">Notes</Label>
                        <Input id="visit_notes" v-model="scheduleForm.notes" />
                        <InputError :message="scheduleForm.errors.notes" />
                    </div>
                    <div class="md:col-span-2">
                        <Button :disabled="scheduleForm.processing">Schedule Visit</Button>
                    </div>
                </form>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Visits</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3">
                <div v-for="visit in props.visits.data" :key="visit.id" class="rounded-md border p-3">
                    <div class="flex items-start justify-between gap-4">
                        <div class="text-sm">
                            <p class="font-medium">{{ visit.visitor_name ?? `Visit #${visit.id}` }}</p>
                            <p>Phone: {{ visit.visitor_phone ?? 'N/A' }}</p>
                            <p>Unit: {{ visit.marketplace_unit?.unit?.name ?? 'N/A' }}</p>
                            <p>Scheduled: {{ visit.scheduled_at ?? 'N/A' }}</p>
                            <p>Notes: {{ visit.notes ?? 'N/A' }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <Badge variant="secondary">{{ visit.status?.name_en ?? visit.status?.name ?? 'Unknown' }}</Badge>
                            <div class="flex gap-2">
                                <Button size="sm" variant="outline" as-child>
                                    <Link :href="`/marketplace/visits/${visit.id}`">Details</Link>
                                </Button>
                                <Button size="sm" variant="outline" @click="sendContract(visit.id)">Send Contract</Button>
                                <Button size="sm" variant="destructive" @click="cancelVisit(visit.id)">Cancel</Button>
                            </div>
                        </div>
                    </div>
                </div>
                <p v-if="props.visits.data.length === 0" class="text-muted-foreground text-sm">No visits yet.</p>
            </CardContent>
        </Card>
    </div>
</template>
