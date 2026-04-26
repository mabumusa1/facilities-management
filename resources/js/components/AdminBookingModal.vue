<script setup lang="ts">
import { ref } from 'vue';
import { useI18n } from '@/composables/useI18n';
import type { CalendarBooking, Facility } from '@/types';
import { store as calendarStore } from '@/actions/App/Http/Controllers/Facilities/FacilityCalendarController';

// ─── Props & emits ────────────────────────────────────────────────────────────

const props = defineProps<{
    facilities: Pick<Facility, 'id' | 'name' | 'name_en' | 'name_ar'>[];
    prefilledFacilityId: number | null;
    prefilledDate: string;
    prefilledStart: string;
    prefilledEnd: string;
}>();

const emit = defineEmits<{
    close: [];
    created: [booking: CalendarBooking];
}>();

const { t, isArabic } = useI18n();

// ─── Form state ───────────────────────────────────────────────────────────────

const facilityId = ref<number | ''>(props.prefilledFacilityId ?? '');
const bookingDate = ref<string>(props.prefilledDate);
const startTime = ref<string>(props.prefilledStart);
const endTime = ref<string>(props.prefilledEnd);
const residentSearch = ref<string>('');
const notes = ref<string>('');

const processing = ref(false);
const overlapError = ref(false);
const validationErrors = ref<Record<string, string>>({});

// ─── Keyboard trap ────────────────────────────────────────────────────────────

function onKeydown(e: KeyboardEvent): void {
    if (e.key === 'Escape') { emit('close'); }
}

// ─── Submit ───────────────────────────────────────────────────────────────────

async function submit(): Promise<void> {
    if (processing.value) { return; }

    overlapError.value = false;
    validationErrors.value = {};
    processing.value = true;

    try {
        const body: Record<string, unknown> = {
            facility_id: facilityId.value,
            booking_date: bookingDate.value,
            start_time: startTime.value,
            end_time: endTime.value,
            notes: notes.value || null,
        };

        const response = await fetch(calendarStore.url(), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-XSRF-TOKEN': getCsrfToken(),
            },
            body: JSON.stringify(body),
        });

        if (response.status === 201) {
            const data = await response.json();
            emit('created', data.booking as CalendarBooking);
            return;
        }

        if (response.status === 422) {
            const data = await response.json();
            // Check if it's an overlap error embedded in the message
            if (typeof data.message === 'string' && data.message.startsWith('overlap_detected:')) {
                overlapError.value = true;
            } else {
                // Standard Laravel validation errors
                const errors = data.errors ?? {};
                Object.keys(errors).forEach((key) => {
                    validationErrors.value[key] = Array.isArray(errors[key])
                        ? errors[key][0]
                        : errors[key];
                });
            }
        }
    } finally {
        processing.value = false;
    }
}

function getCsrfToken(): string {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
}
</script>

<template>
    <!-- Backdrop -->
    <div
        class="fixed inset-0 z-40 bg-black/40"
        aria-hidden="true"
        @click="emit('close')"
    ></div>

    <!-- Modal panel -->
    <div
        role="dialog"
        :aria-label="t('app.facilities.calendar.createBooking')"
        aria-modal="true"
        class="fixed inset-x-4 top-1/2 z-50 mx-auto max-w-lg -translate-y-1/2 rounded-xl bg-background p-6 shadow-xl"
        @keydown="onKeydown"
    >
        <!-- Header -->
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-base font-semibold">{{ t('app.facilities.calendar.createBooking') }}</h2>
            <button
                type="button"
                class="text-muted-foreground hover:text-foreground -mr-2 -mt-1 rounded p-1"
                @click="emit('close')"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Overlap error panel -->
        <div
            v-if="overlapError"
            class="mb-4 rounded-md bg-destructive/10 p-3 text-sm text-destructive"
            role="alert"
        >
            <p class="font-medium">{{ t('app.facilities.calendar.overlapTitle') }}</p>
            <p>{{ t('app.facilities.calendar.overlapMsg') }}</p>
            <p>{{ t('app.facilities.calendar.overlapAction') }}</p>
        </div>

        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <!-- Facility -->
            <div class="flex flex-col gap-1.5">
                <label class="text-sm font-medium" for="cal-facility">
                    {{ t('app.facilities.calendar.facility') }}
                </label>
                <select
                    id="cal-facility"
                    v-model="facilityId"
                    class="border-input bg-background text-foreground h-9 rounded-md border px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                    :disabled="processing"
                    required
                >
                    <option value="">{{ t('app.facilities.calendar.allFacilities') }}</option>
                    <option v-for="f in facilities" :key="f.id" :value="f.id">
                        {{ isArabic ? (f.name_ar ?? f.name) : (f.name_en ?? f.name) }}
                    </option>
                </select>
                <p v-if="validationErrors.facility_id" class="text-xs text-destructive">{{ validationErrors.facility_id }}</p>
            </div>

            <!-- Date -->
            <div class="flex flex-col gap-1.5">
                <label class="text-sm font-medium" for="cal-date">
                    {{ t('app.facilities.calendar.date') }}
                </label>
                <input
                    id="cal-date"
                    v-model="bookingDate"
                    type="date"
                    class="border-input bg-background text-foreground h-9 rounded-md border px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                    :disabled="processing"
                    required
                />
                <p v-if="validationErrors.booking_date" class="text-xs text-destructive">{{ validationErrors.booking_date }}</p>
            </div>

            <!-- Start / End time -->
            <div class="grid grid-cols-2 gap-3">
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium" for="cal-start">
                        {{ t('app.facilities.calendar.start') }}
                    </label>
                    <input
                        id="cal-start"
                        v-model="startTime"
                        type="time"
                        step="900"
                        class="border-input bg-background text-foreground h-9 rounded-md border px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                        :disabled="processing"
                        required
                    />
                    <p v-if="validationErrors.start_time" class="text-xs text-destructive">{{ validationErrors.start_time }}</p>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium" for="cal-end">
                        {{ t('app.facilities.calendar.end') }}
                    </label>
                    <input
                        id="cal-end"
                        v-model="endTime"
                        type="time"
                        step="900"
                        class="border-input bg-background text-foreground h-9 rounded-md border px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                        :disabled="processing"
                        required
                    />
                    <p v-if="validationErrors.end_time" class="text-xs text-destructive">{{ validationErrors.end_time }}</p>
                </div>
            </div>

            <!-- Resident search (optional) -->
            <div class="flex flex-col gap-1.5">
                <label class="text-sm font-medium" for="cal-resident">
                    {{ t('app.facilities.calendar.resident') }}
                    <span class="text-muted-foreground font-normal">({{ t('app.common.optional', { fallback: 'optional' }) }})</span>
                </label>
                <input
                    id="cal-resident"
                    v-model="residentSearch"
                    type="search"
                    class="border-input bg-background text-foreground h-9 rounded-md border px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                    :placeholder="t('app.facilities.calendar.residentSearch')"
                    :disabled="processing"
                />
            </div>

            <!-- Notes -->
            <div class="flex flex-col gap-1.5">
                <label class="text-sm font-medium" for="cal-notes">
                    {{ t('app.facilities.calendar.notes') }}
                </label>
                <textarea
                    id="cal-notes"
                    v-model="notes"
                    rows="3"
                    class="border-input bg-background text-foreground rounded-md border px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                    :disabled="processing"
                ></textarea>
            </div>

            <!-- Footer actions -->
            <div class="flex items-center justify-end gap-2 border-t pt-4">
                <button
                    type="button"
                    class="border-input bg-background hover:bg-muted h-9 rounded-md border px-4 text-sm font-medium"
                    :disabled="processing"
                    @click="emit('close')"
                >
                    {{ t('app.facilities.calendar.cancel') }}
                </button>
                <button
                    type="submit"
                    class="bg-primary text-primary-foreground hover:bg-primary/90 h-9 rounded-md px-4 text-sm font-medium disabled:opacity-50"
                    :disabled="processing"
                >
                    <span v-if="processing">{{ t('app.facilities.calendar.loading') }}</span>
                    <span v-else>{{ t('app.facilities.calendar.create') }}</span>
                </button>
            </div>
        </form>
    </div>
</template>
