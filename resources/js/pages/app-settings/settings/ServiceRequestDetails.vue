<script setup lang="ts">
import { Head, Link, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { edit as editRequestCategory } from '@/actions/App/Http/Controllers/AppSettings/RequestCategoryController';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { serviceRequest as serviceRequestSettings } from '@/routes/settings';

const { t } = useI18n();

type SettingsTab = {
    key: string;
    label: string;
    href: string;
};

type ServiceRequestSubcategory = {
    id: number;
    name: string;
    name_ar: string | null;
    name_en: string | null;
    status: boolean;
};

type ServiceRequestCategory = {
    id: number;
    name: string;
    name_ar: string | null;
    name_en: string | null;
    status: boolean;
    has_sub_categories: boolean;
    subcategories: ServiceRequestSubcategory[];
};

type ServiceSetting = {
    id: number;
    category_id: number;
    permissions: Record<string, boolean> | null;
    visibilities: Record<string, boolean> | null;
    submit_request_before_type: string | null;
    submit_request_before_value: number | null;
    capacity_type: string | null;
    capacity_value: number | null;
};

const props = defineProps<{
    tabs: SettingsTab[];
    requestType: string;
    categoryCode: string;
    category: ServiceRequestCategory;
    serviceSetting: ServiceSetting | null;
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.settings'), href: '/settings/invoice' },
            { title: t('app.appSettings.shell.serviceRequestTitle'), href: '/settings/service-request' },
            { title: t('app.appSettings.serviceRequestDetails.details'), href: '#' },
        ],
    });
});
</script>

<template>
    <Head :title="t('app.appSettings.serviceRequestDetails.pageTitle', { name: props.category.name_en ?? props.category.name })" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            variant="small"
            :title="t('app.appSettings.serviceRequestDetails.heading', { name: props.category.name_en ?? props.category.name })"
            :description="t('app.appSettings.serviceRequestDetails.description')"
        />

        <div class="flex flex-wrap gap-2">
            <Button
                v-for="tab in props.tabs"
                :key="tab.key"
                :variant="tab.key === 'service-request' ? 'default' : 'outline'"
                as-child
            >
                <Link :href="tab.href">{{ tab.label }}</Link>
            </Button>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <Badge variant="secondary">{{ t('app.appSettings.serviceRequestDetails.type') }}: {{ props.requestType }}</Badge>
            <Badge variant="secondary">{{ t('app.appSettings.serviceRequestDetails.categoryCode') }}: {{ props.categoryCode }}</Badge>
            <Badge :variant="props.category.status ? 'default' : 'secondary'">
                {{ props.category.status ? t('app.common.active') : t('app.common.inactive') }}
            </Badge>
        </div>

        <div class="flex gap-2">
            <Button variant="outline" as-child>
                <Link :href="serviceRequestSettings().url">{{ t('app.appSettings.serviceRequestDetails.backToSettings') }}</Link>
            </Button>
            <Button as-child>
                <Link :href="editRequestCategory(props.category.id).url">{{ t('app.appSettings.serviceRequestDetails.openCategoryEditor') }}</Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.appSettings.serviceRequestDetails.subcategories') }}</CardTitle>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>{{ t('app.appSettings.serviceRequestDetails.name') }}</TableHead>
                            <TableHead>{{ t('app.appSettings.serviceRequestDetails.status') }}</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="sub in props.category.subcategories" :key="sub.id">
                            <TableCell>{{ sub.name_en ?? sub.name }}</TableCell>
                            <TableCell>
                                <Badge :variant="sub.status ? 'default' : 'secondary'">
                                    {{ sub.status ? t('app.common.active') : t('app.common.inactive') }}
                                </Badge>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.category.subcategories.length === 0">
                            <TableCell :colspan="2" class="text-muted-foreground text-center">
                                {{ t('app.appSettings.serviceRequestDetails.noSubcategories') }}
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.appSettings.serviceRequestDetails.permissionsTitle') }}</CardTitle>
            </CardHeader>
            <CardContent>
                <div v-if="props.serviceSetting?.permissions" class="grid gap-2">
                    <div v-for="(value, key) in props.serviceSetting.permissions" :key="key" class="flex items-center justify-between rounded border p-2">
                        <span>{{ key }}</span>
                        <Badge :variant="value ? 'default' : 'secondary'">{{ value ? t('app.appSettings.serviceRequestDetails.enabled') : t('app.appSettings.serviceRequestDetails.disabled') }}</Badge>
                    </div>
                </div>
                <p v-else class="text-muted-foreground text-sm">{{ t('app.appSettings.serviceRequestDetails.noPermissions') }}</p>
            </CardContent>
        </Card>
    </div>
</template>
