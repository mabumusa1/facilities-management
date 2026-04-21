<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { edit as editRequestCategory } from '@/actions/App/Http/Controllers/AppSettings/RequestCategoryController';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { serviceRequest as serviceRequestSettings } from '@/routes/settings';

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

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Settings', href: '/settings/invoice' },
            { title: 'Service Request', href: '/settings/service-request' },
            { title: 'Details', href: '#' },
        ],
    },
});
</script>

<template>
    <Head :title="`Service Request Details - ${props.category.name_en ?? props.category.name}`" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            variant="small"
            :title="`Service Request Details: ${props.category.name_en ?? props.category.name}`"
            description="Review category configuration, subcategories, and effective service settings."
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
            <Badge variant="secondary">Type: {{ props.requestType }}</Badge>
            <Badge variant="secondary">Category Code: {{ props.categoryCode }}</Badge>
            <Badge :variant="props.category.status ? 'default' : 'secondary'">
                {{ props.category.status ? 'Active' : 'Inactive' }}
            </Badge>
        </div>

        <div class="flex gap-2">
            <Button variant="outline" as-child>
                <Link :href="serviceRequestSettings().url">Back To Service Request Settings</Link>
            </Button>
            <Button as-child>
                <Link :href="editRequestCategory(props.category.id).url">Open Category Editor</Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Subcategories</CardTitle>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Status</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="sub in props.category.subcategories" :key="sub.id">
                            <TableCell>{{ sub.name_en ?? sub.name }}</TableCell>
                            <TableCell>
                                <Badge :variant="sub.status ? 'default' : 'secondary'">
                                    {{ sub.status ? 'Active' : 'Inactive' }}
                                </Badge>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.category.subcategories.length === 0">
                            <TableCell :colspan="2" class="text-muted-foreground text-center">
                                No subcategories configured.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Service Setting Permissions</CardTitle>
            </CardHeader>
            <CardContent>
                <div v-if="props.serviceSetting?.permissions" class="grid gap-2">
                    <div v-for="(value, key) in props.serviceSetting.permissions" :key="key" class="flex items-center justify-between rounded border p-2">
                        <span>{{ key }}</span>
                        <Badge :variant="value ? 'default' : 'secondary'">{{ value ? 'Enabled' : 'Disabled' }}</Badge>
                    </div>
                </div>
                <p v-else class="text-muted-foreground text-sm">No permissions configured for this category yet.</p>
            </CardContent>
        </Card>
    </div>
</template>
