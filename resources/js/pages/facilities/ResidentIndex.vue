<script setup lang="ts">
import { watchEffect } from 'vue';
import { Head, setLayoutProps } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';
import FacilityCard from '@/components/FacilityCard.vue';
import type { Facility, Paginated } from '@/types';

const { t } = useI18n();

const props = defineProps<{
    facilities: Paginated<Facility>;
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.facilities.resident.heading'), href: '/facilities/resident' },
        ],
    });
});

function slotPickerHref(facility: Facility): string {
    return `/facilities/${facility.id}/slots-picker`;
}

</script>

<template>
    <Head :title="t('app.facilities.resident.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">{{ t('app.facilities.resident.heading') }}</h2>
            <p class="text-muted-foreground text-sm">{{ t('app.facilities.resident.description') }}</p>
        </div>

        <!-- Skeleton loading (deferred props empty state) -->
        <template v-if="!facilities">
            <div
                class="flex flex-col gap-3"
                role="status"
                :aria-label="t('app.facilities.resident.loadingFacilities')"
            >
                <div
                    v-for="i in 4"
                    :key="i"
                    class="bg-muted h-24 animate-pulse rounded-xl"
                />
            </div>
        </template>

        <!-- Empty state -->
        <template v-else-if="facilities.data.length === 0">
            <div class="text-muted-foreground flex flex-col items-center justify-center gap-2 py-16 text-sm">
                <span class="text-4xl">🏢</span>
                <p>{{ t('app.facilities.resident.noFacilitiesAvailable') }}</p>
            </div>
        </template>

        <!-- Facility cards -->
        <template v-else>
            <div class="flex flex-col gap-3" role="list" :aria-label="t('app.facilities.resident.heading')">
                <div v-for="facility in facilities.data" :key="facility.id" role="listitem">
                    <FacilityCard :facility="facility" :book-href="slotPickerHref(facility)" />
                </div>
            </div>
        </template>
    </div>
</template>
