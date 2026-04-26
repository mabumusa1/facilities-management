<script setup lang="ts">
import { computed, ref, watchEffect } from 'vue';
import { Head, setLayoutProps } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';
import CalendarGrid from '@/components/CalendarGrid.vue';
import BookingPopover from '@/components/BookingPopover.vue';
import AdminBookingModal from '@/components/AdminBookingModal.vue';
import type { CalendarBooking, Facility } from '@/types';
import {
    bookings as calendarBookings,
    store as calendarStore,
    show as calendarShow,
} from '@/actions/App/Http/Controllers/Facilities/FacilityCalendarController';

// ─── Props ────────────────────────────────────────────────────────────────────

const props = defineProps<{
    facilities: Pick<Facility, 'id' | 'name' | 'name_en' | 'name_ar'>[];
    currentWeekStart: string;
}>();

// ─── i18n ─────────────────────────────────────────────────────────────────────

const { t, isArabic } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.facilities.calendar.heading'), href: '/facilities/calendar' },
        ],
    });
});

// ─── Week navigation ──────────────────────────────────────────────────────────

function parseDate(str: string): Date {
    const [y, m, d] = str.split('-').map(Number);
    return new Date(y, m - 1, d);
}

function toDateString(d: Date): string {
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd}`;
}

const weekStart = ref<Date>(parseDate(props.currentWeekStart));

const weekDates = computed<Date[]>(() => {
    const dates: Date[] = [];
    for (let i = 0; i < 7; i++) {
        const d = new Date(weekStart.value);
        d.setDate(weekStart.value.getDate() + i);
        dates.push(d);
    }
    return dates;
});

const weekLabel = computed<string>(() => {
    const start = weekDates.value[0];
    const end = weekDates.value[6];
    const locale = isArabic.value ? 'ar-SA' : 'en-US';
    const startStr = start.toLocaleDateString(locale, { day: 'numeric', month: 'short', year: 'numeric' });
    const endStr = end.toLocaleDateString(locale, { day: 'numeric', month: 'short', year: 'numeric' });
    return `${startStr} – ${endStr}`;
});

function prevWeek(): void {
    const d = new Date(weekStart.value);
    d.setDate(d.getDate() - 7);
    weekStart.value = d;
    loadBookings();
}

function nextWeek(): void {
    const d = new Date(weekStart.value);
    d.setDate(d.getDate() + 7);
    weekStart.value = d;
    loadBookings();
}

function goToday(): void {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    // Rewind to Sunday
    const day = today.getDay();
    today.setDate(today.getDate() - day);
    weekStart.value = today;
    loadBookings();
}

// ─── Facility filter ──────────────────────────────────────────────────────────

const selectedFacilityId = ref<number | null>(null);

// ─── Status filter ────────────────────────────────────────────────────────────

// Status IDs matching FacilityBookingStatus constants
const STATUS_PENDING = 19;
const STATUS_BOOKED = 20;
const STATUS_CANCELLED = 22;

// Map filter tab keys to status_id sets
type FilterKey = 'all' | 'confirmed' | 'checkedIn' | 'completed' | 'cancelled';

const activeFilter = ref<FilterKey>('all');

const filterTabs = computed<{ key: FilterKey; label: string }[]>(() => [
    { key: 'all', label: t('app.facilities.calendar.filterAll') },
    { key: 'confirmed', label: t('app.facilities.calendar.filterConfirmed') },
    { key: 'checkedIn', label: t('app.facilities.calendar.filterCheckedIn') },
    { key: 'completed', label: t('app.facilities.calendar.filterCompleted') },
    { key: 'cancelled', label: t('app.facilities.calendar.filterCancelled') },
]);

// ─── Booking data ─────────────────────────────────────────────────────────────

const allBookings = ref<CalendarBooking[]>([]);
const loading = ref(false);

async function loadBookings(): Promise<void> {
    loading.value = true;
    allBookings.value = [];

    try {
        const params: Record<string, string | number> = {
            week_start: toDateString(weekStart.value),
        };
        if (selectedFacilityId.value !== null) {
            params['facility_id'] = selectedFacilityId.value;
        }

        const url = calendarBookings.url({ query: params });
        const response = await fetch(url, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (response.ok) {
            const data = await response.json();
            allBookings.value = data.bookings ?? [];
        }
    } finally {
        loading.value = false;
    }
}

// Load bookings on mount
loadBookings();

function onFacilityChange(): void {
    loadBookings();
}

const filteredBookings = computed<CalendarBooking[]>(() => {
    if (activeFilter.value === 'all') {
        return allBookings.value;
    }
    const filterMap: Record<FilterKey, number[]> = {
        all: [],
        confirmed: [STATUS_BOOKED],
        checkedIn: [], // checked-in would be a different status ID; show none if no match
        completed: [],
        cancelled: [STATUS_CANCELLED],
    };
    const ids = filterMap[activeFilter.value];
    return allBookings.value.filter((b) => ids.includes(b.status_id));
});

// ─── Booking popover ──────────────────────────────────────────────────────────

const popoverBooking = ref<CalendarBooking | null>(null);
const popoverLoading = ref(false);

async function openPopover(bookingId: number): Promise<void> {
    popoverLoading.value = true;
    popoverBooking.value = null;

    try {
        const response = await fetch(calendarShow.url(bookingId), {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
        if (response.ok) {
            popoverBooking.value = await response.json();
        }
    } finally {
        popoverLoading.value = false;
    }
}

function closePopover(): void {
    popoverBooking.value = null;
}

// ─── Admin create modal ───────────────────────────────────────────────────────

const showCreateModal = ref(false);
const prefilledDate = ref<string>('');
const prefilledStart = ref<string>('');
const prefilledEnd = ref<string>('');
const prefilledFacilityId = ref<number | null>(null);

function openCreateModal(opts?: { date?: string; start?: string; end?: string; facilityId?: number }): void {
    prefilledDate.value = opts?.date ?? toDateString(weekStart.value);
    prefilledStart.value = opts?.start ?? '09:00';
    prefilledEnd.value = opts?.end ?? '10:00';
    prefilledFacilityId.value = opts?.facilityId ?? selectedFacilityId.value;
    showCreateModal.value = true;
}

function closeCreateModal(): void {
    showCreateModal.value = false;
}

async function onBookingCreated(newBooking: CalendarBooking): Promise<void> {
    closeCreateModal();
    allBookings.value = [...allBookings.value, newBooking];
}
</script>

<template>
    <div class="flex flex-col gap-4 p-4">
        <Head :title="t('app.facilities.calendar.pageTitle')" />

        <!-- ── Header ──────────────────────────────────────────────────────── -->
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-bold tracking-tight">{{ t('app.facilities.calendar.heading') }}</h1>
            <p class="text-muted-foreground text-sm">{{ t('app.facilities.calendar.description') }}</p>
        </div>

        <!-- ── Controls row ────────────────────────────────────────────────── -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- Facility dropdown -->
            <select
                v-model="selectedFacilityId"
                class="border-input bg-background text-foreground h-9 rounded-md border px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                @change="onFacilityChange"
            >
                <option :value="null">{{ t('app.facilities.calendar.allFacilities') }}</option>
                <option v-for="f in facilities" :key="f.id" :value="f.id">
                    {{ isArabic ? (f.name_ar ?? f.name) : (f.name_en ?? f.name) }}
                </option>
            </select>

            <!-- Week navigation -->
            <div class="flex items-center gap-2 ms-auto">
                <button
                    type="button"
                    class="border-input bg-background hover:bg-muted flex h-9 w-9 items-center justify-center rounded-md border text-sm"
                    :aria-label="t('app.facilities.calendar.previousWeek')"
                    @click="prevWeek"
                >
                    <span aria-hidden="true">{{ isArabic ? '▶' : '◀' }}</span>
                </button>

                <span class="min-w-[200px] text-center text-sm font-medium">{{ weekLabel }}</span>

                <button
                    type="button"
                    class="border-input bg-background hover:bg-muted flex h-9 w-9 items-center justify-center rounded-md border text-sm"
                    :aria-label="t('app.facilities.calendar.nextWeek')"
                    @click="nextWeek"
                >
                    <span aria-hidden="true">{{ isArabic ? '◀' : '▶' }}</span>
                </button>

                <button
                    type="button"
                    class="border-input bg-background hover:bg-muted h-9 rounded-md border px-3 text-sm"
                    @click="goToday"
                >
                    {{ t('app.facilities.calendar.today') }}
                </button>

                <button
                    type="button"
                    class="bg-primary text-primary-foreground hover:bg-primary/90 h-9 rounded-md px-3 text-sm font-medium"
                    @click="openCreateModal()"
                >
                    + {{ t('app.facilities.calendar.createBooking') }}
                </button>
            </div>
        </div>

        <!-- ── Status filter tabs ──────────────────────────────────────────── -->
        <div class="flex gap-1 border-b" role="tablist">
            <button
                v-for="tab in filterTabs"
                :key="tab.key"
                type="button"
                role="tab"
                :aria-selected="activeFilter === tab.key"
                :class="[
                    'border-b-2 px-3 py-2 text-sm font-medium transition-colors',
                    activeFilter === tab.key
                        ? 'border-primary text-primary'
                        : 'text-muted-foreground hover:text-foreground border-transparent',
                ]"
                @click="activeFilter = tab.key"
            >
                {{ tab.label }}
            </button>
        </div>

        <!-- ── Calendar grid ───────────────────────────────────────────────── -->
        <CalendarGrid
            :week-dates="weekDates"
            :bookings="filteredBookings"
            :loading="loading"
            :is-arabic="isArabic"
            @booking-click="openPopover"
            @slot-click="openCreateModal"
        />

        <!-- ── Legend ─────────────────────────────────────────────────────── -->
        <div class="flex flex-wrap items-center gap-4 text-xs text-muted-foreground">
            <span class="flex items-center gap-1.5">
                <span class="inline-block h-3 w-3 rounded-sm bg-blue-500"></span>
                {{ t('app.facilities.calendar.legendConfirmed') }}
            </span>
            <span class="flex items-center gap-1.5">
                <span class="inline-block h-3 w-3 rounded-sm bg-green-500"></span>
                {{ t('app.facilities.calendar.legendCheckedIn') }}
            </span>
            <span class="flex items-center gap-1.5">
                <span class="inline-block h-3 w-3 rounded-sm bg-amber-400"></span>
                {{ t('app.facilities.calendar.legendPending') }}
            </span>
            <span class="flex items-center gap-1.5">
                <span class="inline-block h-3 w-3 rounded-sm bg-gray-400"></span>
                {{ t('app.facilities.calendar.legendCompleted') }}
            </span>
            <span class="flex items-center gap-1.5">
                <span class="inline-block h-3 w-3 rounded-sm bg-red-400"></span>
                {{ t('app.facilities.calendar.legendCancelled') }}
            </span>
        </div>

        <!-- ── Booking popover ─────────────────────────────────────────────── -->
        <BookingPopover
            v-if="popoverBooking || popoverLoading"
            :booking="popoverBooking"
            :loading="popoverLoading"
            @close="closePopover"
        />

        <!-- ── Admin create modal ──────────────────────────────────────────── -->
        <AdminBookingModal
            v-if="showCreateModal"
            :facilities="facilities"
            :prefilled-facility-id="prefilledFacilityId"
            :prefilled-date="prefilledDate"
            :prefilled-start="prefilledStart"
            :prefilled-end="prefilledEnd"
            @close="closeCreateModal"
            @created="onBookingCreated"
        />
    </div>
</template>
