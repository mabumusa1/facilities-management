<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const { t } = useI18n();

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

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.marketplace'), href: '/marketplace' },
            { title: t('app.navigation.visits'), href: '/marketplace/visits' },
        ],
    });
});
</script>

<template>
    <Head :title="t('app.marketplace.visitShow.pageTitle', { id: props.visit.id })" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading variant="small" :title="t('app.marketplace.visitShow.heading', { id: props.visit.id })" :description="t('app.marketplace.visitShow.description')" />
            <div class="flex gap-2">
                <Button variant="outline" as-child>
                    <Link href="/marketplace/visits">{{ t('app.actions.back') }}</Link>
                </Button>
                <Button variant="outline" @click="sendContract">{{ t('app.marketplace.visits.sendContract') }}</Button>
                <Button variant="destructive" @click="cancelVisit">{{ t('app.marketplace.visitShow.cancelVisit') }}</Button>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.marketplace.visitShow.visitInformation') }}</CardTitle>
            </CardHeader>
            <CardContent class="grid gap-2 text-sm md:grid-cols-2">
                <p><span class="font-medium">{{ t('app.marketplace.visits.visitorName') }}:</span> {{ props.visit.visitor_name ?? t('app.common.notAvailable') }}</p>
                <p><span class="font-medium">{{ t('app.marketplace.visits.visitorPhone') }}:</span> {{ props.visit.visitor_phone ?? t('app.common.notAvailable') }}</p>
                <p><span class="font-medium">{{ t('app.marketplace.common.unit') }}:</span> {{ props.visit.marketplace_unit?.unit?.name ?? t('app.common.notAvailable') }}</p>
                <p><span class="font-medium">{{ t('app.marketplace.visits.scheduledAt') }}:</span> {{ props.visit.scheduled_at ?? t('app.common.notAvailable') }}</p>
                <p>
                    <span class="font-medium">{{ t('app.marketplace.common.status') }}:</span>
                    <Badge variant="secondary">{{ props.visit.status?.name_en ?? props.visit.status?.name ?? t('app.marketplace.visits.unknownStatus') }}</Badge>
                </p>
                <p class="md:col-span-2"><span class="font-medium">{{ t('app.marketplace.visits.notes') }}:</span> {{ props.visit.notes ?? t('app.common.notAvailable') }}</p>
            </CardContent>
        </Card>
    </div>
</template>
