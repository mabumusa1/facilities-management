<script setup lang="ts">
import { computed } from 'vue';
import { useI18n } from '@/composables/useI18n';
import type { FacilitySlot } from '@/types';

const props = defineProps<{
    slots: FacilitySlot[];
    selectedSlot: FacilitySlot | null;
    loading?: boolean;
}>();

const emit = defineEmits<{
    (e: 'select', slot: FacilitySlot): void;
}>();

const { t } = useI18n();

function slotHour(slot: FacilitySlot): number {
    return parseInt(slot.start.split(':')[0], 10);
}

const morningSlots = computed(() => props.slots.filter((s) => slotHour(s) < 12));
const afternoonSlots = computed(() => props.slots.filter((s) => slotHour(s) >= 12 && slotHour(s) < 17));
const eveningSlots = computed(() => props.slots.filter((s) => slotHour(s) >= 17));

function isSelected(slot: FacilitySlot): boolean {
    return (
        props.selectedSlot !== null &&
        props.selectedSlot?.start === slot.start &&
        props.selectedSlot?.end === slot.end
    );
}

function slotLabel(slot: FacilitySlot): string {
    const statusLabel =
        slot.status === 'available'
            ? t('app.facilities.resident.availableTap')
            : t('app.facilities.resident.booked');
    return t('app.facilities.resident.slotAriaLabel', { time: slot.start, status: statusLabel });
}

function handleSelect(slot: FacilitySlot): void {
    if (slot.status === 'available') {
        emit('select', slot);
    }
}
</script>

<template>
    <div class="flex flex-col gap-4">
        <!-- Skeleton loading -->
        <template v-if="loading">
            <div class="grid grid-cols-4 gap-2" role="status" :aria-label="t('app.facilities.resident.loadingSlots')">
                <div
                    v-for="i in 12"
                    :key="i"
                    class="bg-muted h-14 animate-pulse rounded-lg"
                />
            </div>
        </template>

        <template v-else>
            <!-- Morning section -->
            <template v-if="morningSlots.length">
                <p class="text-muted-foreground text-sm font-medium">
                    {{ t('app.facilities.resident.morning') }}
                </p>
                <div class="grid grid-cols-4 gap-2" role="group" :aria-label="t('app.facilities.resident.morning')">
                    <button
                        v-for="slot in morningSlots"
                        :key="slot.start"
                        type="button"
                        :aria-label="slotLabel(slot)"
                        :aria-disabled="slot.status !== 'available'"
                        :aria-pressed="isSelected(slot)"
                        class="flex min-h-[44px] flex-col items-center justify-center rounded-lg border px-1 py-2 text-xs font-medium transition-colors"
                        :class="{
                            'bg-primary text-primary-foreground border-primary ring-2 ring-offset-1': isSelected(slot),
                            'bg-background text-foreground border-border hover:bg-muted cursor-pointer':
                                slot.status === 'available' && !isSelected(slot),
                            'bg-muted text-muted-foreground border-muted cursor-not-allowed': slot.status !== 'available',
                        }"
                        :disabled="slot.status !== 'available'"
                        @click="handleSelect(slot)"
                    >
                        <span>{{ slot.start }}</span>
                        <span class="mt-0.5 text-[10px] opacity-75">
                            {{ slot.status === 'available' ? t('app.facilities.resident.available') : t('app.facilities.resident.booked') }}
                        </span>
                    </button>
                </div>
            </template>

            <!-- Afternoon section -->
            <template v-if="afternoonSlots.length">
                <p class="text-muted-foreground text-sm font-medium">
                    {{ t('app.facilities.resident.afternoon') }}
                </p>
                <div class="grid grid-cols-4 gap-2" role="group" :aria-label="t('app.facilities.resident.afternoon')">
                    <button
                        v-for="slot in afternoonSlots"
                        :key="slot.start"
                        type="button"
                        :aria-label="slotLabel(slot)"
                        :aria-disabled="slot.status !== 'available'"
                        :aria-pressed="isSelected(slot)"
                        class="flex min-h-[44px] flex-col items-center justify-center rounded-lg border px-1 py-2 text-xs font-medium transition-colors"
                        :class="{
                            'bg-primary text-primary-foreground border-primary ring-2 ring-offset-1': isSelected(slot),
                            'bg-background text-foreground border-border hover:bg-muted cursor-pointer':
                                slot.status === 'available' && !isSelected(slot),
                            'bg-muted text-muted-foreground border-muted cursor-not-allowed': slot.status !== 'available',
                        }"
                        :disabled="slot.status !== 'available'"
                        @click="handleSelect(slot)"
                    >
                        <span>{{ slot.start }}</span>
                        <span class="mt-0.5 text-[10px] opacity-75">
                            {{ slot.status === 'available' ? t('app.facilities.resident.available') : t('app.facilities.resident.booked') }}
                        </span>
                    </button>
                </div>
            </template>

            <!-- Evening section -->
            <template v-if="eveningSlots.length">
                <p class="text-muted-foreground text-sm font-medium">
                    {{ t('app.facilities.resident.evening') }}
                </p>
                <div class="grid grid-cols-4 gap-2" role="group" :aria-label="t('app.facilities.resident.evening')">
                    <button
                        v-for="slot in eveningSlots"
                        :key="slot.start"
                        type="button"
                        :aria-label="slotLabel(slot)"
                        :aria-disabled="slot.status !== 'available'"
                        :aria-pressed="isSelected(slot)"
                        class="flex min-h-[44px] flex-col items-center justify-center rounded-lg border px-1 py-2 text-xs font-medium transition-colors"
                        :class="{
                            'bg-primary text-primary-foreground border-primary ring-2 ring-offset-1': isSelected(slot),
                            'bg-background text-foreground border-border hover:bg-muted cursor-pointer':
                                slot.status === 'available' && !isSelected(slot),
                            'bg-muted text-muted-foreground border-muted cursor-not-allowed': slot.status !== 'available',
                        }"
                        :disabled="slot.status !== 'available'"
                        @click="handleSelect(slot)"
                    >
                        <span>{{ slot.start }}</span>
                        <span class="mt-0.5 text-[10px] opacity-75">
                            {{ slot.status === 'available' ? t('app.facilities.resident.available') : t('app.facilities.resident.booked') }}
                        </span>
                    </button>
                </div>
            </template>

            <!-- Empty state -->
            <p
                v-if="!morningSlots.length && !afternoonSlots.length && !eveningSlots.length"
                class="text-muted-foreground text-sm"
            >
                {{ t('app.facilities.resident.allSlotsFull') }}
            </p>
        </template>
    </div>
</template>
