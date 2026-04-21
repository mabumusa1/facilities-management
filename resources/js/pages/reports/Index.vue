<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const props = defineProps<{
    reportMode: string;
    title: string;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Reports', href: '/dashboard/reports' },
        ],
    },
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
            <Heading variant="small" :title="props.title" :description="`Current mode: ${props.reportMode}`" />
            <div class="flex gap-2">
                <Button variant="outline" as-child>
                    <Link href="/dashboard/power-bi-reports">Power BI</Link>
                </Button>
                <Button variant="outline" as-child>
                    <Link href="/dashboard/system-reports">System Reports</Link>
                </Button>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Integrated Report Endpoints</CardTitle>
            </CardHeader>
            <CardContent>
                <ul class="list-disc space-y-1 pl-5 text-sm">
                    <li v-for="endpoint in reportEndpoints" :key="endpoint">{{ endpoint }}</li>
                </ul>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Report Views</CardTitle>
            </CardHeader>
            <CardContent class="flex flex-wrap gap-2">
                <Button variant="outline" as-child>
                    <Link href="/dashboard/reports">Reports</Link>
                </Button>
                <Button variant="outline" as-child>
                    <Link href="/dashboard/system-reports">System Reports</Link>
                </Button>
                <Button variant="outline" as-child>
                    <Link href="/dashboard/system-reports/lease">Lease Reports</Link>
                </Button>
                <Button variant="outline" as-child>
                    <Link href="/dashboard/system-reports/maintenance">Maintenance Reports</Link>
                </Button>
            </CardContent>
        </Card>
    </div>
</template>
