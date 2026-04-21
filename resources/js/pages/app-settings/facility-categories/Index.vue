<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useI18n } from '@/composables/useI18n';
import type { FacilityCategory } from '@/types';

const { t } = useI18n();

defineProps<{
    categories: (FacilityCategory & { facilities_count?: number })[];
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.appSettings.common.appSettings'), href: '/app-settings/facility-categories' },
            { title: t('app.appSettings.facilityCategories.pageTitle'), href: '/app-settings/facility-categories' },
        ],
    });
});

function deleteCategory(id: number) {
    if (confirm(t('app.appSettings.facilityCategories.deleteCategoryConfirm'))) {
        router.delete(`/app-settings/facility-categories/${id}`);
    }
}
</script>

<template>
    <Head :title="t('app.appSettings.facilityCategories.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <Heading variant="small" :title="t('app.appSettings.facilityCategories.heading')" :description="t('app.appSettings.facilityCategories.description')" />
            <Button as-child><Link href="/app-settings/facility-categories/create">{{ t('app.appSettings.facilityCategories.newCategory') }}</Link></Button>
        </div>

        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>{{ t('app.appSettings.facilityCategories.nameEn') }}</TableHead>
                    <TableHead>{{ t('app.appSettings.facilityCategories.nameAr') }}</TableHead>
                    <TableHead>{{ t('app.appSettings.common.status') }}</TableHead>
                    <TableHead>{{ t('app.appSettings.facilityCategories.facilities') }}</TableHead>
                    <TableHead class="text-right">{{ t('app.appSettings.common.actions') }}</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="cat in categories" :key="cat.id">
                    <TableCell>{{ cat.name_en ?? cat.name }}</TableCell>
                    <TableCell>{{ cat.name_ar ?? '—' }}</TableCell>
                    <TableCell><Badge :variant="(cat as any).status !== false ? 'default' : 'secondary'">{{ (cat as any).status !== false ? t('app.appSettings.common.active') : t('app.appSettings.common.inactive') }}</Badge></TableCell>
                    <TableCell>{{ cat.facilities_count ?? 0 }}</TableCell>
                    <TableCell class="text-right space-x-2">
                        <Button variant="outline" size="sm" as-child><Link :href="`/app-settings/facility-categories/${cat.id}/edit`">{{ t('app.appSettings.common.edit') }}</Link></Button>
                        <Button variant="destructive" size="sm" @click="deleteCategory(cat.id)">{{ t('app.appSettings.common.delete') }}</Button>
                    </TableCell>
                </TableRow>
                <TableRow v-if="categories.length === 0">
                    <TableCell :colspan="5" class="text-muted-foreground text-center">{{ t('app.appSettings.facilityCategories.noCategories') }}</TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>
