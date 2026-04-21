<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

const props = defineProps<{
    visit: {
        id: number;
        visitor_name: string | null;
        visitor_phone: string | null;
        scheduled_at: string | null;
        notes: string | null;
        marketplace_unit?: { unit?: { name?: string | null } | null } | null;
        status?: { name?: string | null; name_en?: string | null } | null;
    };
}>();

const rejectForm = useForm({
    notes: '',
});

function approve() {
    router.post(`/visitor-access/${props.visit.id}/approve`);
}

function reject() {
    rejectForm.post(`/visitor-access/${props.visit.id}/reject`);
}

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Visitor Access', href: '/visitor-access/history' },
            { title: 'Details', href: '/visitor-access/history' },
        ],
    },
});
</script>

<template>
    <Head :title="`Visitor #${props.visit.id}`" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading variant="small" :title="`Visitor Access #${props.visit.id}`" description="Approve or reject visitor access request." />
            <Button variant="outline" as-child>
                <Link href="/visitor-access/history">Back</Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Request Details</CardTitle>
            </CardHeader>
            <CardContent class="grid gap-2 text-sm md:grid-cols-2">
                <p><span class="font-medium">Visitor:</span> {{ props.visit.visitor_name ?? 'N/A' }}</p>
                <p><span class="font-medium">Phone:</span> {{ props.visit.visitor_phone ?? 'N/A' }}</p>
                <p><span class="font-medium">Unit:</span> {{ props.visit.marketplace_unit?.unit?.name ?? 'N/A' }}</p>
                <p><span class="font-medium">Scheduled:</span> {{ props.visit.scheduled_at ?? 'N/A' }}</p>
                <p>
                    <span class="font-medium">Status:</span>
                    <Badge variant="secondary">{{ props.visit.status?.name_en ?? props.visit.status?.name ?? 'Unknown' }}</Badge>
                </p>
                <p class="md:col-span-2"><span class="font-medium">Notes:</span> {{ props.visit.notes ?? 'N/A' }}</p>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Actions</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="flex gap-2">
                    <Button @click="approve">Approve</Button>
                </div>

                <form class="space-y-2" @submit.prevent="reject">
                    <Label for="reject-notes">Reject Notes</Label>
                    <Textarea id="reject-notes" v-model="rejectForm.notes" rows="3" />
                    <InputError :message="rejectForm.errors.notes" />
                    <Button variant="destructive" :disabled="rejectForm.processing">Reject</Button>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
