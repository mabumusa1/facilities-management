<script setup lang="ts">
import { Head, Link, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const { t } = useI18n();

const props = defineProps<{
    reportMode: string;
    title: string;
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.reports'), href: '/dashboard/reports' },
        ],
    });
});

const reportEndpoints = [
    '/report/load',
    '/report/prepare',
    '/report/render',
    '/report/pages',
    '/report/pages/active',
    '/report/filters',
    '/report/bookmarks',
    '/report/settings',
    '/report/print',
    '/report/refresh',
    '/report/save',
    '/report/saveAs',
    '/report/theme',
    '/report/zoom',
];
</script>

<template>
    <Head :title="props.title" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading variant="small" :title="props.title" :description="t('app.reports.currentMode', { mode: props.reportMode })" />
            <div class="flex gap-2">
                <Button variant="outline" as-child>
                    <Link href="/dashboard/power-bi-reports">{{ t('app.navigation.powerBiReports') }}</Link>
                </Button>
                <Button variant="outline" as-child>
                    <Link href="/dashboard/system-reports">{{ t('app.navigation.systemReports') }}</Link>
                </Button>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.reports.integratedEndpoints') }}</CardTitle>
            </CardHeader>
            <CardContent>
                <ul class="list-disc space-y-1 pl-5 text-sm">
                    <li v-for="endpoint in reportEndpoints" :key="endpoint">{{ endpoint }}</li>
                </ul>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.reports.reportViews') }}</CardTitle>
            </CardHeader>
            <CardContent class="flex flex-wrap gap-2">
                <Button variant="outline" as-child>
                    <Link href="/dashboard/reports">{{ t('app.navigation.reports') }}</Link>
                </Button>
                <Button variant="outline" as-child>
                    <Link href="/dashboard/system-reports">{{ t('app.navigation.systemReports') }}</Link>
                </Button>
                <Button variant="outline" as-child>
                    <Link href="/dashboard/system-reports/lease">{{ t('app.reports.leaseReports') }}</Link>
                </Button>
                <Button variant="outline" as-child>
                    <Link href="/dashboard/system-reports/maintenance">{{ t('app.reports.maintenanceReports') }}</Link>
                </Button>
            </CardContent>
        </Card>
    </div>
</template>
