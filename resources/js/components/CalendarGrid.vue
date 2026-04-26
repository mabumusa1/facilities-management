<script setup lang="ts">
import { computed } from 'vue';
import type { CalendarBooking } from '@/types';

// ─── Props & emits ────────────────────────────────────────────────────────────

const props = defineProps<{
    weekDates: Date[];
    bookings: CalendarBooking[];
    loading: boolean;
    isArabic: boolean;
}>();

const emit = defineEmits<{
    bookingClick: [bookingId: number];
    slotClick: [opts: { date: string; start: string; end: string; facilityId?: number }];
}>();

// ─── Time rows ────────────────────────────────────────────────────────────────

/** Hours rendered: 06:00 – 23:00 (18 rows). */
const hours = computed<string[]>(() => {
    const result: string[] = [];
    for (let h = 6; h <= 23; h++) {
        result.push(String(h).padStart(2, '0') + ':00');
    }
    return result;
});

// ─── Date helpers ─────────────────────────────────────────────────────────────

function toDateString(d: Date): string {
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd}`;
}

function formatDayName(d: Date): string {
    return d.toLocaleDateString(props.isArabic ? 'ar-SA' : 'en-US', { weekday: 'short' });
}

function formatDayNumber(d: Date): string {
    return d.toLocaleDateString(props.isArabic ? 'ar-SA' : 'en-US', { day: 'numeric', month: 'short' });
}

// ─── Booking helpers ──────────────────────────────────────────────────────────

/** Map status_id to a Tailwind colour class. */
function statusColour(statusId: number): string {
    switch (statusId) {
        case 19: return 'bg-amber-400 text-amber-900';   // pending
        case 20: return 'bg-blue-500 text-white';         // booked/confirmed
        case 21: return 'bg-gray-400 text-white';         // rejected/completed
        case 22: return 'bg-red-400 text-white line-through'; // cancelled
        default: return 'bg-gray-300 text-gray-800';
    }
}

/**
 * Return bookings that fall on a given date string.
 * A booking is positioned in the row matching its start_time hour.
 */
function bookingsForDateAndHour(dateStr: string, hour: string): CalendarBooking[] {
    return props.bookings.filter((b) => {
        if (b.booking_date !== dateStr) { return false; }
        const bHour = b.start_time.slice(0, 5);
        return bHour === hour;
    });
}

/**
 * Calculate how many rows (hours) a booking spans.
 * Minimum 1 row.
 */
function bookingRowSpan(booking: CalendarBooking): number {
    const [sh, sm] = booking.start_time.split(':').map(Number);
    const [eh, em] = booking.end_time.split(':').map(Number);
    const startMins = sh * 60 + (sm ?? 0);
    const endMins = eh * 60 + (em ?? 0);
    const rows = Math.ceil((endMins - startMins) / 60);
    return Math.max(1, rows);
}

function buildAriaLabel(booking: CalendarBooking): string {
    const name = booking.booker_name || booking.facility_name;
    return `${name}, ${booking.facility_name}, ${booking.start_time}–${booking.end_time}, ${booking.status_name}`;
}

function onSlotClick(date: Date, hour: string): void {
    const [h] = hour.split(':').map(Number);
    const start = String(h).padStart(2, '0') + ':00';
    const end = String(h + 1).padStart(2, '0') + ':00';
    emit('slotClick', { date: toDateString(date), start, end });
}
</script>

<template>
    <div class="relative overflow-auto rounded-md border">
        <!-- Loading overlay -->
        <div
            v-if="loading"
            class="bg-background/60 absolute inset-0 z-10 flex items-center justify-center"
            aria-live="polite"
        >
            <div class="border-primary h-8 w-8 animate-spin rounded-full border-4 border-t-transparent"></div>
        </div>

        <table class="w-full min-w-[640px] border-collapse text-sm">
            <thead>
                <tr>
                    <!-- Time column header -->
                    <th scope="col" class="bg-muted w-16 border-b border-r px-2 py-2 text-center font-medium"></th>

                    <!-- Day column headers -->
                    <th
                        v-for="date in weekDates"
                        :key="toDateString(date)"
                        scope="col"
                        class="bg-muted border-b border-r px-2 py-2 text-center font-medium last:border-r-0"
                    >
                        <div class="text-muted-foreground text-xs">{{ formatDayName(date) }}</div>
                        <div class="text-foreground text-sm font-semibold">{{ formatDayNumber(date) }}</div>
                    </th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="hour in hours" :key="hour">
                    <!-- Time label -->
                    <th
                        scope="row"
                        class="text-muted-foreground border-b border-r px-2 py-1 text-right text-xs font-normal"
                    >
                        {{ hour }}
                    </th>

                    <!-- Day cells -->
                    <td
                        v-for="date in weekDates"
                        :key="toDateString(date)"
                        class="border-b border-r p-0.5 align-top last:border-r-0"
                        style="height: 48px; min-width: 80px;"
                    >
                        <!-- Booking blocks that start in this hour -->
                        <template v-if="bookingsForDateAndHour(toDateString(date), hour).length">
                            <button
                                v-for="booking in bookingsForDateAndHour(toDateString(date), hour)"
                                :key="booking.id"
                                type="button"
                                :class="[
                                    'flex w-full flex-col overflow-hidden rounded px-1 py-0.5 text-left text-xs font-medium transition-opacity hover:opacity-80 focus:outline-none focus:ring-2 focus:ring-ring',
                                    statusColour(booking.status_id),
                                ]"
                                :style="{ minHeight: `${bookingRowSpan(booking) * 48 - 4}px` }"
                                :aria-label="buildAriaLabel(booking)"
                                @click="emit('bookingClick', booking.id)"
                            >
                                <span class="truncate leading-tight">{{ booking.booker_name || booking.facility_name }}</span>
                                <span class="truncate leading-tight opacity-90">
                                    {{ booking.start_time }}–{{ booking.end_time }}
                                </span>
                                <span class="truncate leading-tight opacity-75">{{ booking.status_name }}</span>
                            </button>
                        </template>

                        <!-- Empty slot: click to create -->
                        <template v-else>
                            <button
                                type="button"
                                class="h-full w-full cursor-pointer rounded opacity-0 transition-opacity hover:opacity-100 hover:bg-muted hover:border hover:border-dashed hover:border-muted-foreground"
                                :aria-label="`Create booking on ${toDateString(date)} at ${hour}`"
                                @click="onSlotClick(date, hour)"
                            >&nbsp;</button>
                        </template>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
