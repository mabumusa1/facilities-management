<script setup lang="ts">
import { Head, Link, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

const { t } = useI18n();

type FacilityItem = {
    id: number;
    name: string;
    category?: { name?: string | null; name_en?: string | null } | null;
    community?: { name?: string | null } | null;
    is_active: boolean;
    bookings_count: number;
};

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.settings'), href: '/settings/invoice' },
            { title: t('app.navigation.facilities'), href: '/settings/facilities' },
        ],
    });
});

const props = defineProps<{
    facilities: {
        data: FacilityItem[];
    };
}>();
</script>

<template>
    <Head :title="t('app.appSettings.facilities.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading
                variant="small"
                :title="t('app.appSettings.facilities.heading')"
                :description="t('app.appSettings.facilities.description')"
            />
            <Button as-child>
                <Link href="/settings/addNewFacility">{{ t('app.appSettings.facilities.addFacility') }}</Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.navigation.facilities') }}</CardTitle>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>{{ t('app.appSettings.facilities.name') }}</TableHead>
                            <TableHead>{{ t('app.appSettings.facilities.category') }}</TableHead>
                            <TableHead>{{ t('app.appSettings.facilities.community') }}</TableHead>
                            <TableHead>{{ t('app.appSettings.facilities.status') }}</TableHead>
                            <TableHead>{{ t('app.appSettings.facilities.bookings') }}</TableHead>
                            <TableHead class="text-right">{{ t('app.appSettings.facilities.actions') }}</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="facility in props.facilities.data" :key="facility.id">
                            <TableCell>{{ facility.name }}</TableCell>
                            <TableCell>{{ facility.category?.name_en ?? facility.category?.name ?? t('app.common.notAvailable') }}</TableCell>
                            <TableCell>{{ facility.community?.name ?? t('app.common.notAvailable') }}</TableCell>
                            <TableCell>
                                <Badge :variant="facility.is_active ? 'default' : 'secondary'">
                                    {{ facility.is_active ? t('app.common.active') : t('app.common.inactive') }}
                                </Badge>
                            </TableCell>
                            <TableCell>{{ facility.bookings_count }}</TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Button variant="outline" size="sm" as-child>
                                        <Link :href="`/settings/facility/${facility.id}`">{{ t('app.appSettings.facilities.view') }}</Link>
                                    </Button>
                                    <Button variant="outline" size="sm" as-child>
                                        <Link :href="`/settings/addNewFacility/${facility.id}`">{{ t('app.actions.edit') }}</Link>
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.facilities.data.length === 0">
                            <TableCell :colspan="6" class="text-muted-foreground text-center">{{ t('app.appSettings.facilities.empty') }}</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    </div>
</template>
