<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

const { t } = useI18n();

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

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.settings'), href: '/settings/invoice' },
            { title: t('app.navigation.settingsForms'), href: '/settings/forms' },
        ],
    });
});
</script>

<template>
    <Head :title="t('app.settingsForms.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading variant="small" :title="t('app.settingsForms.formTemplates')" :description="t('app.settingsForms.description')" />
            <Button as-child>
                <Link href="/settings/forms/create">{{ t('app.settingsForms.createTemplate') }}</Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.settingsForms.templates') }}</CardTitle>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>{{ t('app.settingsForms.name') }}</TableHead>
                            <TableHead>{{ t('app.settingsForms.category') }}</TableHead>
                            <TableHead>{{ t('app.settingsForms.communityBuilding') }}</TableHead>
                            <TableHead>{{ t('app.settingsForms.status') }}</TableHead>
                            <TableHead class="text-right">{{ t('app.settingsForms.actions') }}</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="template in props.templates.data" :key="template.id">
                            <TableCell>{{ template.name }}</TableCell>
                            <TableCell>{{ template.request_category?.name_en ?? template.request_category?.name ?? t('app.common.notAvailable') }}</TableCell>
                            <TableCell>{{ template.community?.name ?? t('app.common.notAvailable') }} / {{ template.building?.name ?? t('app.common.notAvailable') }}</TableCell>
                            <TableCell>
                                <Badge :variant="template.is_active ? 'default' : 'secondary'">{{ template.is_active ? t('app.common.active') : t('app.common.inactive') }}</Badge>
                            </TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Button size="sm" variant="outline" as-child>
                                        <Link :href="`/settings/forms/preview/${template.id}`">{{ t('app.settingsForms.preview') }}</Link>
                                    </Button>
                                    <Button size="sm" variant="outline" as-child>
                                        <Link :href="`/settings/forms/${template.id}/edit`">{{ t('app.actions.edit') }}</Link>
                                    </Button>
                                    <Button size="sm" variant="destructive" @click="removeTemplate(template.id)">{{ t('app.actions.delete') }}</Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.templates.data.length === 0">
                            <TableCell :colspan="5" class="text-muted-foreground text-center">{{ t('app.settingsForms.empty') }}</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    </div>
</template>
