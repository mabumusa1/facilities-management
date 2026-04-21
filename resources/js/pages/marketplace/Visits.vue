<script setup lang="ts">
import { Head, Link, router, setLayoutProps, useForm } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.marketplace'), href: '/marketplace' },
            { title: t('app.navigation.visits'), href: '/marketplace/visits' },
        ],
    });
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
    <Head :title="t('app.marketplace.visits.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="t('app.marketplace.visits.heading')" :description="t('app.marketplace.visits.description')" />

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.marketplace.visits.scheduleVisit') }}</CardTitle>
            </CardHeader>
            <CardContent>
                <form class="grid gap-4 md:grid-cols-2" @submit.prevent="schedule">
                    <div class="grid gap-2">
                        <Label for="marketplace_unit_id">{{ t('app.navigation.listing') }}</Label>
                        <select id="marketplace_unit_id" v-model.number="scheduleForm.marketplace_unit_id" class="rounded-md border border-input bg-background px-3 py-2">
                            <option :value="0">{{ t('app.marketplace.visits.selectListing') }}</option>
                            <option v-for="listing in props.listings" :key="listing.id" :value="listing.id">
                                {{ listing.unit?.name ?? t('app.marketplace.common.listingFallback', { id: listing.id }) }}
                            </option>
                        </select>
                        <InputError :message="scheduleForm.errors.marketplace_unit_id" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="visitor_name">{{ t('app.marketplace.visits.visitorName') }}</Label>
                        <Input id="visitor_name" v-model="scheduleForm.visitor_name" />
                        <InputError :message="scheduleForm.errors.visitor_name" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="visitor_phone">{{ t('app.marketplace.visits.visitorPhone') }}</Label>
                        <Input id="visitor_phone" v-model="scheduleForm.visitor_phone" />
                        <InputError :message="scheduleForm.errors.visitor_phone" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="scheduled_at">{{ t('app.marketplace.visits.scheduledAt') }}</Label>
                        <Input id="scheduled_at" v-model="scheduleForm.scheduled_at" type="datetime-local" />
                        <InputError :message="scheduleForm.errors.scheduled_at" />
                    </div>
                    <div class="grid gap-2 md:col-span-2">
                        <Label for="visit_notes">{{ t('app.marketplace.visits.notes') }}</Label>
                        <Input id="visit_notes" v-model="scheduleForm.notes" />
                        <InputError :message="scheduleForm.errors.notes" />
                    </div>
                    <div class="md:col-span-2">
                        <Button :disabled="scheduleForm.processing">{{ t('app.marketplace.visits.scheduleVisit') }}</Button>
                    </div>
                </form>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.marketplace.visits.visits') }}</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3">
                <div v-for="visit in props.visits.data" :key="visit.id" class="rounded-md border p-3">
                    <div class="flex items-start justify-between gap-4">
                        <div class="text-sm">
                            <p class="font-medium">{{ visit.visitor_name ?? t('app.marketplace.visits.visitFallback', { id: visit.id }) }}</p>
                            <p>{{ t('app.marketplace.common.phone') }}: {{ visit.visitor_phone ?? t('app.common.notAvailable') }}</p>
                            <p>{{ t('app.marketplace.common.unit') }}: {{ visit.marketplace_unit?.unit?.name ?? t('app.common.notAvailable') }}</p>
                            <p>{{ t('app.marketplace.visits.scheduled') }}: {{ visit.scheduled_at ?? t('app.common.notAvailable') }}</p>
                            <p>{{ t('app.marketplace.visits.notes') }}: {{ visit.notes ?? t('app.common.notAvailable') }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <Badge variant="secondary">{{ visit.status?.name_en ?? visit.status?.name ?? t('app.marketplace.visits.unknownStatus') }}</Badge>
                            <div class="flex gap-2">
                                <Button size="sm" variant="outline" as-child>
                                    <Link :href="`/marketplace/visits/${visit.id}`">{{ t('app.marketplace.visits.details') }}</Link>
                                </Button>
                                <Button size="sm" variant="outline" @click="sendContract(visit.id)">{{ t('app.marketplace.visits.sendContract') }}</Button>
                                <Button size="sm" variant="destructive" @click="cancelVisit(visit.id)">{{ t('app.marketplace.visits.cancel') }}</Button>
                            </div>
                        </div>
                    </div>
                </div>
                <p v-if="props.visits.data.length === 0" class="text-muted-foreground text-sm">{{ t('app.marketplace.visits.noVisits') }}</p>
            </CardContent>
        </Card>
    </div>
</template>
