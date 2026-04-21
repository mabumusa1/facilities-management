<script setup lang="ts">
import { Head, Link, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.visitorAccessHistory'), href: '/visitor-access/history' },
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
            marketplace_unit?: { unit?: { name?: string | null } | null } | null;
            status?: { name?: string | null; name_en?: string | null } | null;
        }>;
    };
}>();
</script>

<template>
    <Head :title="t('app.visitorAccess.history.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            variant="small"
            :title="t('app.visitorAccess.history.heading')"
            :description="t('app.visitorAccess.history.description')"
        />

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.visitorAccess.history.listTitle') }}</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3">
                <div v-for="visit in props.visits.data" :key="visit.id" class="flex items-center justify-between rounded-md border p-3">
                    <div class="text-sm">
                        <p class="font-medium">{{ visit.visitor_name ?? t('app.visitorAccess.history.visitFallback', { id: visit.id }) }}</p>
                        <p>{{ t('app.visitorAccess.history.phone') }}: {{ visit.visitor_phone ?? t('app.common.notAvailable') }}</p>
                        <p>{{ t('app.visitorAccess.history.unit') }}: {{ visit.marketplace_unit?.unit?.name ?? t('app.common.notAvailable') }}</p>
                        <p>{{ t('app.visitorAccess.history.scheduled') }}: {{ visit.scheduled_at ?? t('app.common.notAvailable') }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <Badge variant="secondary">{{ visit.status?.name_en ?? visit.status?.name ?? t('app.visitorAccess.history.unknownStatus') }}</Badge>
                        <Button size="sm" as-child>
                            <Link :href="`/visitor-access/visitor-details/${visit.id}`">{{ t('app.visitorAccess.history.details') }}</Link>
                        </Button>
                    </div>
                </div>
                <p v-if="props.visits.data.length === 0" class="text-muted-foreground text-sm">{{ t('app.visitorAccess.history.noRecords') }}</p>
            </CardContent>
        </Card>
    </div>
</template>
