<script setup lang="ts">
import { Head, Link, router, setLayoutProps, useForm } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

const { t } = useI18n();

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

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.visitorAccessHistory'), href: '/visitor-access/history' },
            { title: t('app.visitorAccess.details.breadcrumb'), href: '/visitor-access/history' },
        ],
    });
});
</script>

<template>
    <Head :title="t('app.visitorAccess.details.pageTitle', { id: props.visit.id })" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading
                variant="small"
                :title="t('app.visitorAccess.details.heading', { id: props.visit.id })"
                :description="t('app.visitorAccess.details.description')"
            />
            <Button variant="outline" as-child>
                <Link href="/visitor-access/history">{{ t('app.actions.back') }}</Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.visitorAccess.details.requestDetails') }}</CardTitle>
            </CardHeader>
            <CardContent class="grid gap-2 text-sm md:grid-cols-2">
                <p><span class="font-medium">{{ t('app.visitorAccess.details.visitor') }}:</span> {{ props.visit.visitor_name ?? t('app.common.notAvailable') }}</p>
                <p><span class="font-medium">{{ t('app.visitorAccess.details.phone') }}:</span> {{ props.visit.visitor_phone ?? t('app.common.notAvailable') }}</p>
                <p><span class="font-medium">{{ t('app.visitorAccess.details.unit') }}:</span> {{ props.visit.marketplace_unit?.unit?.name ?? t('app.common.notAvailable') }}</p>
                <p><span class="font-medium">{{ t('app.visitorAccess.details.scheduled') }}:</span> {{ props.visit.scheduled_at ?? t('app.common.notAvailable') }}</p>
                <p>
                    <span class="font-medium">{{ t('app.visitorAccess.details.status') }}:</span>
                    <Badge variant="secondary">{{ props.visit.status?.name_en ?? props.visit.status?.name ?? t('app.visitorAccess.history.unknownStatus') }}</Badge>
                </p>
                <p class="md:col-span-2"><span class="font-medium">{{ t('app.visitorAccess.details.notes') }}:</span> {{ props.visit.notes ?? t('app.common.notAvailable') }}</p>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.visitorAccess.details.actions') }}</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="flex gap-2">
                    <Button @click="approve">{{ t('app.visitorAccess.details.approve') }}</Button>
                </div>

                <form class="space-y-2" @submit.prevent="reject">
                    <Label for="reject-notes">{{ t('app.visitorAccess.details.rejectNotes') }}</Label>
                    <Textarea id="reject-notes" v-model="rejectForm.notes" rows="3" />
                    <InputError :message="rejectForm.errors.notes" />
                    <Button variant="destructive" :disabled="rejectForm.processing">{{ t('app.visitorAccess.details.reject') }}</Button>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
