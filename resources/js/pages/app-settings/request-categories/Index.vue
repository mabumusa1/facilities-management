<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { RequestCategory } from '@/types';

defineProps<{
    categories: (RequestCategory & { subcategories_count?: number })[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'App Settings', href: '/app-settings/request-categories' },
            { title: 'Request Categories', href: '/app-settings/request-categories' },
        ],
    },
});

function deleteCategory(id: number) {
    if (confirm('Are you sure you want to delete this category?')) {
        router.delete(`/app-settings/request-categories/${id}`);
    }
}
</script>

<template>
    <Head title="Request Categories" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <Heading variant="small" title="Request Categories" description="Manage service request categories and subcategories." />
            <Button as-child><Link href="/app-settings/request-categories/create">New Category</Link></Button>
        </div>

        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>Name (EN)</TableHead>
                    <TableHead>Name (AR)</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Subcategories</TableHead>
                    <TableHead class="text-right">Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="cat in categories" :key="cat.id">
                    <TableCell>{{ cat.name_en ?? cat.name }}</TableCell>
                    <TableCell>{{ cat.name_ar ?? '—' }}</TableCell>
                    <TableCell><Badge :variant="(cat as any).status !== false ? 'default' : 'secondary'">{{ (cat as any).status !== false ? 'Active' : 'Inactive' }}</Badge></TableCell>
                    <TableCell>{{ cat.subcategories_count ?? 0 }}</TableCell>
                    <TableCell class="text-right space-x-2">
                        <Button variant="outline" size="sm" as-child><Link :href="`/app-settings/request-categories/${cat.id}/edit`">Edit</Link></Button>
                        <Button variant="destructive" size="sm" @click="deleteCategory(cat.id)">Delete</Button>
                    </TableCell>
                </TableRow>
                <TableRow v-if="categories.length === 0">
                    <TableCell :colspan="5" class="text-muted-foreground text-center">No categories yet.</TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>
