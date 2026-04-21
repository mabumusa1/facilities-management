<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

type TemplateItem = {
    id: number;
    name: string;
    is_active: boolean;
    request_category?: { name?: string | null; name_en?: string | null } | null;
    community?: { name?: string | null } | null;
    building?: { name?: string | null } | null;
};

const props = defineProps<{
    templates: {
        data: TemplateItem[];
    };
}>();

function removeTemplate(id: number) {
    router.delete(`/settings/forms/${id}`);
}

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Settings', href: '/settings/invoice' },
            { title: 'Forms', href: '/settings/forms' },
        ],
    },
});
</script>

<template>
    <Head title="Settings - Forms" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading variant="small" title="Form Templates" description="Create and manage templates used by settings forms." />
            <Button as-child>
                <Link href="/settings/forms/create">Create Template</Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Templates</CardTitle>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Category</TableHead>
                            <TableHead>Community / Building</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="template in props.templates.data" :key="template.id">
                            <TableCell>{{ template.name }}</TableCell>
                            <TableCell>{{ template.request_category?.name_en ?? template.request_category?.name ?? 'N/A' }}</TableCell>
                            <TableCell>{{ template.community?.name ?? 'N/A' }} / {{ template.building?.name ?? 'N/A' }}</TableCell>
                            <TableCell>
                                <Badge :variant="template.is_active ? 'default' : 'secondary'">{{ template.is_active ? 'Active' : 'Inactive' }}</Badge>
                            </TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Button size="sm" variant="outline" as-child>
                                        <Link :href="`/settings/forms/preview/${template.id}`">Preview</Link>
                                    </Button>
                                    <Button size="sm" variant="outline" as-child>
                                        <Link :href="`/settings/forms/${template.id}/edit`">Edit</Link>
                                    </Button>
                                    <Button size="sm" variant="destructive" @click="removeTemplate(template.id)">Delete</Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.templates.data.length === 0">
                            <TableCell :colspan="5" class="text-muted-foreground text-center">No form templates found.</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    </div>
</template>
