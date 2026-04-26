<script setup lang="ts">
import { computed, ref, watch, watchEffect } from 'vue';
import { Head, router, setLayoutProps } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, X } from 'lucide-vue-next';
import { useI18n } from '@/composables/useI18n';
import SlotGrid from '@/components/SlotGrid.vue';
import { index as residentIndex, slots as residentSlots, book as residentBook } from '@/actions/App/Http/Controllers/Facilities/ResidentFacilityController';
import type { Facility, FacilitySlot } from '@/types';

const props = defineProps<{
    facility: Facility;
    bookingHorizonDays: number;
}>();

const { t, isArabic } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.facilities.resident.heading'), href: residentIndex.url() },
            { title: localizedFacilityName.value, href: residentIndex.url() },
        ],
    });
});

// ─── Date strip ─────────────────────────────────────────────────────────────

function toDateString(d: Date): string {
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd}`;
}

const today = new Date();
today.setHours(0, 0, 0, 0);

const selectedDate = ref<string>(toDateString(today));
const weekOffset = ref<number>(0);

const weekDates = computed<Date[]>(() => {
    const dates: Date[] = [];
    for (let i = 0; i < 7; i++) {
        const d = new Date(today);
        d.setDate(today.getDate() + weekOffset.value * 7 + i);
        if (d <= new Date(today.getTime() + props.bookingHorizonDays * 86_400_000)) {
            dates.push(d);
        }
    }
    return dates;
});

function formatDayName(d: Date): string {
    return d.toLocaleDateString(isArabic.value ? 'ar-SA' : 'en-US', { weekday: 'short' });
}

function formatDayNumber(d: Date): number {
    return d.getDate();
}

function isToday(d: Date): boolean {
    return toDateString(d) === toDateString(today);
}

function isSelected(d: Date): boolean {
    return toDateString(d) === selectedDate.value;
}

function selectDate(d: Date): void {
    selectedDate.value = toDateString(d);
}

function prevWeek(): void {
    if (weekOffset.value > 0) {
        weekOffset.value--;
    }
}

function nextWeek(): void {
    const maxOffset = Math.ceil(props.bookingHorizonDays / 7);
    if (weekOffset.value < maxOffset) {
        weekOffset.value++;
    }
}

// ─── Slot loading ────────────────────────────────────────────────────────────

const slots = ref<FacilitySlot[]>([]);
const slotsLoading = ref(false);
const slotsClosed = ref(false);
const slotsClosedMessage = ref('');

async function loadSlots(date: string): Promise<void> {
    slotsLoading.value = true;
    slotsClosed.value = false;
    slots.value = [];
    selectedSlot.value = null;
    confirmError.value = null;

    try {
        const response = await fetch(residentSlots.url(props.facility.id, { query: { date } }), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
        const data = await response.json();

        if (data.closed) {
            slotsClosed.value = true;
            slotsClosedMessage.value = data.message ?? t('app.facilities.resident.closedOnDay');
        } else {
            slots.value = data.slots ?? [];
        }
    } catch {
        slotsClosedMessage.value = t('app.facilities.resident.serverError');
        slotsClosed.value = true;
    } finally {
        slotsLoading.value = false;
    }
}

watch(selectedDate, (newDate) => {
    loadSlots(newDate);
}, { immediate: true });

// ─── Slot selection & confirmation sheet ─────────────────────────────────────

const selectedSlot = ref<FacilitySlot | null>(null);
const showConfirmSheet = ref(false);
const confirmLoading = ref(false);
const confirmError = ref<string | null>(null);

function onSlotSelect(slot: FacilitySlot): void {
    selectedSlot.value = slot;
    showConfirmSheet.value = true;
    confirmError.value = null;
}

function closeSheet(): void {
    showConfirmSheet.value = false;
    selectedSlot.value = null;
    confirmError.value = null;
}

function formatTime(time: string): string {
    return time;
}

function durationMinutes(slot: FacilitySlot): number {
    const [sh, sm] = slot.start.split(':').map(Number);
    const [eh, em] = slot.end.split(':').map(Number);
    return (eh * 60 + em) - (sh * 60 + sm);
}

function durationLabel(slot: FacilitySlot): string {
    const mins = durationMinutes(slot);
    if (mins < 60) {
        return t('app.facilities.resident.confirmDuration') + ` (${mins} min)`;
    }
    const hours = mins / 60;
    return `${hours} ${hours === 1 ? 'hour' : 'hours'}`;
}

async function confirmBooking(): Promise<void> {
    if (!selectedSlot.value) {
        return;
    }

    confirmLoading.value = true;
    confirmError.value = null;

    try {
        const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null)?.content ?? '';

        const response = await fetch(residentBook.url(props.facility.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                date: selectedDate.value,
                start_time: selectedSlot.value.start,
                end_time: selectedSlot.value.end,
            }),
        });

        const data = await response.json();

        if (response.status === 409) {
            confirmError.value = 'slot_unavailable';
            return;
        }

        if (!response.ok) {
            confirmError.value = 'server_error';
            return;
        }

        showConfirmSheet.value = false;

        if (data.contract_required) {
            // Redirect to contract page
            router.visit(residentIndex.url());
            return;
        }

        // Refresh slots to reflect new booking
        await loadSlots(selectedDate.value);
    } catch {
        confirmError.value = 'server_error';
    } finally {
        confirmLoading.value = false;
    }
}

// ─── Computed helpers ─────────────────────────────────────────────────────────

const localizedFacilityName = computed<string>(() => {
    if (isArabic.value) {
        return props.facility.name_ar ?? props.facility.name_en ?? props.facility.name;
    }
    return props.facility.name_en ?? props.facility.name_ar ?? props.facility.name;
});

const selectedDateLabel = computed<string>(() => {
    const d = new Date(selectedDate.value + 'T00:00:00');
    return d.toLocaleDateString(isArabic.value ? 'ar-SA' : 'en-US', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
});
</script>

<template>
    <Head :title="localizedFacilityName" />

    <div class="relative flex flex-col gap-4 p-4">
        <!-- Page heading -->
        <div class="flex items-center gap-2">
            <button
                type="button"
                class="text-muted-foreground hover:text-foreground"
                :aria-label="t('app.actions.back')"
                @click="router.visit(residentIndex.url())"
            >
                <ChevronLeft class="h-5 w-5" :class="{ 'rotate-180': isArabic }" />
            </button>
            <h2 class="text-xl font-bold">{{ localizedFacilityName }}</h2>
        </div>

        <!-- Date strip -->
        <div class="flex flex-col gap-2">
            <div class="flex items-center justify-between">
                <button
                    type="button"
                    class="text-muted-foreground hover:text-foreground rounded p-1"
                    :aria-label="t('app.facilities.resident.previousWeek')"
                    :disabled="weekOffset === 0"
                    @click="prevWeek"
                >
                    <ChevronLeft class="h-4 w-4" :class="{ 'rotate-180': isArabic }" />
                </button>

                <div
                    class="flex flex-1 justify-around gap-1"
                    role="group"
                    :aria-label="t('app.navigation.facilities')"
                >
                    <button
                        v-for="d in weekDates"
                        :key="toDateString(d)"
                        type="button"
                        class="flex min-w-[40px] flex-col items-center rounded-lg px-2 py-1.5 text-xs transition-colors"
                        :class="{
                            'bg-primary text-primary-foreground': isSelected(d),
                            'text-muted-foreground hover:bg-muted': !isSelected(d),
                            'font-bold': isToday(d),
                        }"
                        :aria-label="t('app.facilities.resident.selectDate', { day: formatDayName(d), date: formatDayNumber(d) })"
                        :aria-pressed="isSelected(d)"
                        @click="selectDate(d)"
                    >
                        <span>{{ formatDayName(d) }}</span>
                        <span class="font-semibold">{{ formatDayNumber(d) }}</span>
                    </button>
                </div>

                <button
                    type="button"
                    class="text-muted-foreground hover:text-foreground rounded p-1"
                    :aria-label="t('app.facilities.resident.nextWeek')"
                    @click="nextWeek"
                >
                    <ChevronRight class="h-4 w-4" :class="{ 'rotate-180': isArabic }" />
                </button>
            </div>

            <p class="text-foreground text-sm font-medium">{{ selectedDateLabel }}</p>
        </div>

        <!-- Slot grid / closed state -->
        <div aria-live="polite">
            <template v-if="slotsClosed">
                <p class="text-muted-foreground rounded-lg bg-orange-50 p-4 text-sm dark:bg-orange-900/20">
                    {{ slotsClosedMessage }}
                </p>
            </template>
            <template v-else>
                <SlotGrid
                    :slots="slots"
                    :selected-slot="selectedSlot"
                    :loading="slotsLoading"
                    @select="onSlotSelect"
                />
            </template>
        </div>

        <!-- Confirmation bottom sheet -->
        <Transition
            enter-active-class="transition-transform duration-300 ease-out"
            enter-from-class="translate-y-full"
            enter-to-class="translate-y-0"
            leave-active-class="transition-transform duration-200 ease-in"
            leave-from-class="translate-y-0"
            leave-to-class="translate-y-full"
        >
            <div
                v-if="showConfirmSheet && selectedSlot"
                class="bg-background border-border fixed inset-x-0 bottom-0 z-50 rounded-t-2xl border-t p-4 shadow-xl"
                role="dialog"
                :aria-modal="true"
                :aria-label="t('app.facilities.resident.confirmTitle')"
            >
                <!-- Race condition error state -->
                <template v-if="confirmError === 'slot_unavailable'">
                    <div class="flex flex-col gap-4">
                        <div class="flex items-start justify-between">
                            <h3 class="text-destructive font-semibold">
                                ⚠ {{ t('app.facilities.resident.raceConditionTitle') }}
                            </h3>
                            <button type="button" :aria-label="t('app.actions.cancel')" @click="closeSheet">
                                <X class="h-5 w-5" />
                            </button>
                        </div>
                        <p class="text-muted-foreground text-sm">
                            {{ t('app.facilities.resident.raceConditionDetail') }}
                        </p>
                        <button
                            type="button"
                            class="bg-secondary text-secondary-foreground w-full rounded-lg py-2 text-sm font-medium"
                            @click="closeSheet"
                        >
                            {{ t('app.facilities.resident.pickAnotherSlot') }}
                        </button>
                    </div>
                </template>

                <!-- Server error state -->
                <template v-else-if="confirmError === 'server_error'">
                    <div class="flex flex-col gap-4">
                        <div class="flex items-start justify-between">
                            <h3 class="text-destructive font-semibold">⚠ {{ t('app.common.somethingWentWrong') }}</h3>
                            <button type="button" :aria-label="t('app.actions.cancel')" @click="closeSheet">
                                <X class="h-5 w-5" />
                            </button>
                        </div>
                        <p class="text-muted-foreground text-sm">{{ t('app.facilities.resident.serverError') }}</p>
                        <button
                            type="button"
                            class="bg-secondary text-secondary-foreground w-full rounded-lg py-2 text-sm font-medium"
                            @click="closeSheet"
                        >
                            {{ t('app.actions.cancel') }}
                        </button>
                    </div>
                </template>

                <!-- Normal confirmation sheet -->
                <template v-else>
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold">{{ t('app.facilities.resident.confirmTitle') }}</h3>
                            <button type="button" :aria-label="t('app.actions.cancel')" @click="closeSheet">
                                <X class="h-5 w-5" />
                            </button>
                        </div>

                        <div class="divide-border divide-y text-sm">
                            <div class="flex justify-between py-2">
                                <span class="text-muted-foreground">{{ t('app.facilities.resident.confirmDate') }}</span>
                                <span class="font-medium">{{ selectedDateLabel }}</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-muted-foreground">{{ t('app.facilities.resident.confirmTime') }}</span>
                                <span class="font-medium">
                                    {{ formatTime(selectedSlot.start) }} – {{ formatTime(selectedSlot.end) }}
                                </span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-muted-foreground">{{ t('app.facilities.resident.confirmPrice') }}</span>
                                <span class="font-medium">
                                    <template v-if="!facility.pricing_mode || facility.pricing_mode === 'free'">
                                        {{ t('app.facilities.resident.free') }}
                                    </template>
                                    <template v-else-if="facility.pricing_mode === 'per_session'">
                                        {{ t('app.facilities.resident.pricePerSession', { n: facility.booking_fee ?? '0' }) }}
                                    </template>
                                    <template v-else>
                                        {{ t('app.facilities.resident.pricePerHour', { n: facility.booking_fee ?? '0' }) }}
                                    </template>
                                </span>
                            </div>
                        </div>

                        <p
                            v-if="facility.contract_required"
                            class="rounded bg-yellow-50 px-3 py-2 text-xs text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300"
                        >
                            {{ t('app.facilities.resident.contractNotice') }}
                        </p>

                        <p class="text-muted-foreground text-xs">{{ t('app.facilities.resident.confirmNote') }}</p>

                        <div class="flex gap-2">
                            <button
                                type="button"
                                class="bg-secondary text-secondary-foreground flex-1 rounded-lg py-2 text-sm font-medium"
                                :disabled="confirmLoading"
                                @click="closeSheet"
                            >
                                {{ t('app.facilities.resident.cancel') }}
                            </button>
                            <button
                                type="button"
                                class="bg-primary text-primary-foreground flex-1 rounded-lg py-2 text-sm font-medium disabled:opacity-50"
                                :disabled="confirmLoading"
                                @click="confirmBooking"
                            >
                                <template v-if="confirmLoading">
                                    {{ t('app.facilities.resident.processing') }}
                                </template>
                                <template v-else>
                                    {{ t('app.facilities.resident.confirm') }}
                                </template>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </Transition>
    </div>
</template>
