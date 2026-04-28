<script setup lang="ts">
import { useI18n } from '@/composables/useI18n';
import type { Feature } from '@/types';

const { t } = useI18n();

defineProps<{
    amenityOptions: Pick<Feature, 'id' | 'name' | 'name_en' | 'name_ar'>[];
    selectedIds: number[];
}>();

const emit = defineEmits<{
    'update:selectedIds': [value: number[]];
}>();

function toggleAmenity(id: number, currentIds: number[]): void {
    if (currentIds.includes(id)) {
        emit('update:selectedIds', currentIds.filter((a) => a !== id));
    } else {
        emit('update:selectedIds', [...currentIds, id]);
    }
}
</script>

<template>
    <fieldset class="space-y-3 rounded-lg border p-4">
        <legend class="px-1 text-sm font-semibold">
            {{ t('app.properties.units.edit.amenities.sectionTitle') }}
        </legend>

        <p class="text-muted-foreground text-sm">
            {{ t('app.properties.units.edit.amenities.instruction') }}
        </p>

        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
            <label
                v-for="amenity in amenityOptions"
                :key="amenity.id"
                class="flex cursor-pointer items-center gap-2"
            >
                <input
                    type="checkbox"
                    :value="amenity.id"
                    :checked="selectedIds.includes(amenity.id)"
                    class="h-4 w-4 rounded border"
                    @change="toggleAmenity(amenity.id, selectedIds)"
                />
                <span class="text-sm">{{ amenity.name_en ?? amenity.name }}</span>
            </label>
        </div>
    </fieldset>
</template>
