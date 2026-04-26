<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';
import type { Facility } from '@/types';

const props = defineProps<{
    facility: Facility;
    bookHref: string;
}>();

const { t, isArabic } = useI18n();

function localizedName(facility: Facility): string {
    if (isArabic.value) {
        return facility.name_ar ?? facility.name_en ?? facility.name;
    }
    return facility.name_en ?? facility.name_ar ?? facility.name;
}

function pricingBadge(facility: Facility): string {
    if (!facility.pricing_mode || facility.pricing_mode === 'free') {
        return t('app.facilities.resident.free');
    }
    const amount = facility.booking_fee ?? '0';
    if (facility.pricing_mode === 'per_session') {
        return t('app.facilities.resident.pricePerSession', { n: amount });
    }
    return t('app.facilities.resident.pricePerHour', { n: amount });
}
</script>

<template>
    <div class="bg-card border-border flex items-center justify-between rounded-xl border p-4 shadow-sm">
        <div class="flex min-w-0 flex-col gap-1">
            <span class="text-foreground truncate font-semibold">{{ localizedName(facility) }}</span>
            <span v-if="facility.type" class="text-muted-foreground text-sm capitalize">
                {{ facility.type }}
                <template v-if="facility.capacity">
                    &middot; {{ t('app.facilities.resident.capacityLabel', { n: facility.capacity }) }}
                </template>
            </span>
            <div class="mt-1 flex flex-wrap items-center gap-2">
                <span
                    class="rounded-full px-2 py-0.5 text-xs font-medium"
                    :class="
                        !facility.pricing_mode || facility.pricing_mode === 'free'
                            ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                            : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'
                    "
                >
                    {{ pricingBadge(facility) }}
                </span>
                <span
                    v-if="facility.contract_required"
                    class="rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400"
                >
                    {{ t('app.facilities.resident.contractRequired') }}
                </span>
            </div>
        </div>
        <Link
            :href="bookHref"
            class="bg-primary text-primary-foreground hover:bg-primary/90 ml-4 shrink-0 rounded-lg px-4 py-2 text-sm font-medium transition-colors"
        >
            {{ t('app.facilities.resident.book') }}
        </Link>
    </div>
</template>
