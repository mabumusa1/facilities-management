<script setup lang="ts">
import { Head, router, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Facility } from '@/types';

const { isArabic, t } = useI18n();

const props = defineProps<{ facility: Facility }>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.facilities'), href: '/facilities' },
            { title: t('app.facilities.details'), href: '#' },
        ],
    });
});

function deleteFacility() { if (confirm(t('app.common.warning'))) { router.delete(`/facilities/${props.facility.id}`); } }

function localizedCategoryName(): string {
    if (!props.facility.category) {
        return t('app.common.notAvailable');
    }

    if (isArabic.value) {
        return props.facility.category.name_ar ?? props.facility.category.name ?? props.facility.category.name_en ?? t('app.common.notAvailable');
    }

    return props.facility.category.name_en ?? props.facility.category.name ?? props.facility.category.name_ar ?? t('app.common.notAvailable');
}
</script>

<template>
    <Head :title="facility.name" />
    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">{{ facility.name }}</h2>
                <p class="text-muted-foreground text-sm">{{ localizedCategoryName() }} &middot; {{ facility.community?.name }}</p>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child><a :href="`/facilities/${facility.id}/edit`">{{ t('app.actions.edit') }}</a></Button>
                <Button variant="destructive" @click="deleteFacility">{{ t('app.actions.delete') }}</Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.facilities.capacity') }}</CardTitle></CardHeader><CardContent><div class="text-2xl font-bold">{{ facility.capacity ?? t('app.common.notAvailable') }}</div></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.facilities.status') }}</CardTitle></CardHeader><CardContent><Badge :variant="facility.is_active ? 'default' : 'secondary'">{{ facility.is_active ? t('app.common.active') : t('app.common.inactive') }}</Badge></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.facilities.bookings') }}</CardTitle></CardHeader><CardContent><div class="text-2xl font-bold">{{ (facility as any).bookings_count ?? 0 }}</div></CardContent></Card>
        </div>

        <Card v-if="(facility as any).about">
            <CardHeader><CardTitle>{{ t('app.facilities.descriptionLabel') }}</CardTitle></CardHeader>
            <CardContent><p>{{ (facility as any).about }}</p></CardContent>
        </Card>
    </div>
</template>
