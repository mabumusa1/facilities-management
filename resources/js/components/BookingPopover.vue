<script setup lang="ts">
import { useI18n } from '@/composables/useI18n';
import type { CalendarBooking } from '@/types';

// ─── Props & emits ────────────────────────────────────────────────────────────

const props = defineProps<{
    booking: CalendarBooking | null;
    loading: boolean;
}>();

const emit = defineEmits<{
    close: [];
}>();

const { t } = useI18n();

// ─── Duration helper ──────────────────────────────────────────────────────────

function formatDuration(start: string, end: string): string {
    const [sh, sm] = start.split(':').map(Number);
    const [eh, em] = end.split(':').map(Number);
    const mins = (eh * 60 + em) - (sh * 60 + (sm ?? 0));
    const h = Math.floor(mins / 60);
    const m = mins % 60;
    if (h > 0 && m > 0) { return `${h}h ${m}m`; }
    if (h > 0) { return `${h}h`; }
    return `${m}m`;
}

// ─── Keyboard trap ────────────────────────────────────────────────────────────

function onKeydown(e: KeyboardEvent): void {
    if (e.key === 'Escape') { emit('close'); }
}
</script>

<template>
    <!-- Backdrop -->
    <div
        class="fixed inset-0 z-40 bg-black/40"
        aria-hidden="true"
        @click="emit('close')"
    ></div>

    <!-- Popover panel -->
    <div
        role="dialog"
        :aria-label="booking ? `${t('app.facilities.calendar.bookingDetail')} — ${booking.booker_name || booking.facility_name}` : t('app.facilities.calendar.bookingDetail')"
        aria-modal="true"
        class="fixed inset-x-0 bottom-0 z-50 mx-auto max-w-sm rounded-t-xl bg-background p-4 shadow-xl sm:inset-auto sm:bottom-auto sm:left-1/2 sm:top-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 sm:rounded-xl"
        @keydown="onKeydown"
    >
        <!-- Header -->
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-base font-semibold">{{ t('app.facilities.calendar.bookingDetail') }}</h2>
            <button
                type="button"
                class="text-muted-foreground hover:text-foreground -mr-1 -mt-1 rounded p-1"
                :aria-label="t('app.facilities.calendar.cancel')"
                @click="emit('close')"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Loading skeleton -->
        <div v-if="loading" class="animate-pulse space-y-3">
            <div class="h-4 w-3/4 rounded bg-muted"></div>
            <div class="h-4 w-1/2 rounded bg-muted"></div>
            <div class="h-4 w-2/3 rounded bg-muted"></div>
            <div class="h-4 w-1/3 rounded bg-muted"></div>
        </div>

        <!-- Booking details -->
        <template v-else-if="booking">
            <dl class="mb-4 space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-muted-foreground">{{ t('app.facilities.calendar.resident') }}</dt>
                    <dd class="font-medium">{{ booking.booker_name || '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-muted-foreground">{{ t('app.facilities.calendar.facility') }}</dt>
                    <dd class="font-medium">{{ booking.facility_name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-muted-foreground">{{ t('app.facilities.calendar.date') }}</dt>
                    <dd class="font-medium">{{ booking.booking_date }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-muted-foreground">{{ t('app.facilities.calendar.time') }}</dt>
                    <dd class="font-medium">{{ booking.start_time }} – {{ booking.end_time }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-muted-foreground">{{ t('app.facilities.calendar.duration') }}</dt>
                    <dd class="font-medium">{{ formatDuration(booking.start_time, booking.end_time) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-muted-foreground">{{ t('app.facilities.calendar.status') }}</dt>
                    <dd>
                        <span class="rounded-full bg-muted px-2 py-0.5 text-xs font-medium">
                            {{ booking.status_name }}
                        </span>
                    </dd>
                </div>
            </dl>

            <!-- Actions -->
            <div class="flex flex-wrap items-center gap-2 border-t pt-3">
                <a
                    v-if="booking.can_update"
                    :href="`/facility-bookings/${booking.id}/edit`"
                    class="border-input bg-background hover:bg-muted inline-flex h-8 items-center rounded-md border px-3 text-xs font-medium"
                >
                    {{ t('app.facilities.calendar.edit') }}
                </a>

                <button
                    v-if="booking.can_checkin"
                    type="button"
                    class="bg-primary text-primary-foreground hover:bg-primary/90 inline-flex h-8 items-center rounded-md px-3 text-xs font-medium"
                    @click="emit('close')"
                >
                    {{ t('app.facilities.calendar.checkIn') }}
                </button>

                <button
                    v-if="booking.can_cancel"
                    type="button"
                    class="bg-destructive text-destructive-foreground hover:bg-destructive/90 inline-flex h-8 items-center rounded-md px-3 text-xs font-medium"
                    @click="emit('close')"
                >
                    {{ t('app.facilities.calendar.cancelBooking') }}
                </button>
            </div>
        </template>
    </div>
</template>
