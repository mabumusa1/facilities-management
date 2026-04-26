<script setup lang="ts">
import { Head, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { Community, FacilityAvailabilityRule, FacilityCategory } from '@/types';

const { isArabic, t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.facilities'), href: '/facilities' },
            { title: t('app.facilities.newFacility'), href: '/facilities/create' },
        ],
    });
});

defineProps<{
    categories: FacilityCategory[];
    communities: Pick<Community, 'id' | 'name'>[];
}>();

type AvailabilityRuleForm = Omit<FacilityAvailabilityRule, 'id' | 'facility_id' | 'created_at' | 'updated_at'>;

const defaultAvailabilityRules: AvailabilityRuleForm[] = [
    { day_of_week: 0, open_time: '06:00', close_time: '22:00', slot_duration_minutes: 60, max_concurrent_bookings: 1, is_active: false },
    { day_of_week: 1, open_time: '06:00', close_time: '22:00', slot_duration_minutes: 60, max_concurrent_bookings: 1, is_active: true },
    { day_of_week: 2, open_time: '06:00', close_time: '22:00', slot_duration_minutes: 60, max_concurrent_bookings: 1, is_active: true },
    { day_of_week: 3, open_time: '06:00', close_time: '22:00', slot_duration_minutes: 60, max_concurrent_bookings: 1, is_active: true },
    { day_of_week: 4, open_time: '06:00', close_time: '22:00', slot_duration_minutes: 60, max_concurrent_bookings: 1, is_active: true },
    { day_of_week: 5, open_time: '06:00', close_time: '22:00', slot_duration_minutes: 60, max_concurrent_bookings: 1, is_active: false },
    { day_of_week: 6, open_time: '08:00', close_time: '20:00', slot_duration_minutes: 60, max_concurrent_bookings: 1, is_active: true },
];

const form = useForm({
    name_en: '',
    name_ar: '',
    category_id: '',
    community_id: '',
    capacity: '',
    pricing_mode: 'free' as 'free' | 'per_session' | 'per_hour',
    price_amount: '',
    currency: 'SAR',
    booking_horizon_days: 14,
    cancellation_hours_before: 2,
    min_booking_duration_minutes: 30,
    max_booking_duration_minutes: '',
    contract_required: false,
    notes: '',
    availability_rules: defaultAvailabilityRules as AvailabilityRuleForm[],
});

function submit() {
    form.post('/facilities');
}

function localizedCategoryName(category: FacilityCategory): string {
    if (isArabic.value) {
        return category.name_ar ?? category.name ?? category.name_en ?? '';
    }

    return category.name_en ?? category.name ?? category.name_ar ?? '';
}

const dayNames = computed(() => [
    t('app.facilities.sunday'),
    t('app.facilities.monday'),
    t('app.facilities.tuesday'),
    t('app.facilities.wednesday'),
    t('app.facilities.thursday'),
    t('app.facilities.friday'),
    t('app.facilities.saturday'),
]);

const slotOptions = [15, 30, 45, 60, 90, 120];
const showPricing = computed(() => form.pricing_mode !== 'free');
</script>

<template>
    <Head :title="t('app.facilities.newFacility')" />
    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="t('app.facilities.createTitle')" :description="t('app.facilities.createDescription')" />
        <form @submit.prevent="submit" class="max-w-3xl space-y-8">
            <!-- Basic info -->
            <div class="space-y-4">
                <div class="grid gap-2">
                    <Label for="name_en" aria-required="true">{{ t('app.facilities.nameEn') }}</Label>
                    <Input id="name_en" v-model="form.name_en" required :placeholder="t('app.facilities.nameEnPlaceholder')" />
                    <InputError :message="form.errors.name_en" />
                </div>
                <div class="grid gap-2">
                    <Label for="name_ar" aria-required="true">{{ t('app.facilities.nameAr') }}</Label>
                    <Input id="name_ar" v-model="form.name_ar" required dir="rtl" :placeholder="t('app.facilities.nameArPlaceholder')" />
                    <InputError :message="form.errors.name_ar" />
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label aria-required="true">{{ t('app.facilities.community') }}</Label>
                        <Select v-model="form.community_id">
                            <SelectTrigger class="w-full"><SelectValue :placeholder="t('app.facilities.selectCommunity')" /></SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="community in communities" :key="community.id" :value="String(community.id)">
                                    {{ community.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.community_id" />
                    </div>
                    <div class="grid gap-2">
                        <Label aria-required="true">{{ t('app.facilities.category') }}</Label>
                        <Select v-model="form.category_id">
                            <SelectTrigger class="w-full"><SelectValue :placeholder="t('app.facilities.selectCategory')" /></SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="cat in categories" :key="cat.id" :value="String(cat.id)">
                                    {{ localizedCategoryName(cat) }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.category_id" />
                    </div>
                </div>
                <div class="grid gap-2 sm:max-w-xs">
                    <Label for="capacity">{{ t('app.facilities.capacity') }}</Label>
                    <Input id="capacity" v-model="form.capacity" type="number" min="1" :placeholder="t('app.facilities.capacityPlaceholder')" />
                    <InputError :message="form.errors.capacity" />
                </div>
            </div>

            <!-- Pricing -->
            <fieldset class="space-y-4 rounded-lg border p-4">
                <legend class="px-1 text-sm font-semibold">{{ t('app.facilities.pricing') }}</legend>
                <div class="flex flex-wrap gap-4">
                    <label class="flex cursor-pointer items-center gap-2">
                        <input type="radio" v-model="form.pricing_mode" value="free" />
                        <span>{{ t('app.facilities.pricingFree') }}</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-2">
                        <input type="radio" v-model="form.pricing_mode" value="per_session" />
                        <span>{{ t('app.facilities.pricingPerSession') }}</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-2">
                        <input type="radio" v-model="form.pricing_mode" value="per_hour" />
                        <span>{{ t('app.facilities.pricingPerHour') }}</span>
                    </label>
                </div>
                <div v-if="showPricing" class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="price_amount">{{ t('app.facilities.price') }}</Label>
                        <Input id="price_amount" v-model="form.price_amount" type="number" step="0.01" min="0" placeholder="0.00" />
                        <InputError :message="form.errors.price_amount" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="currency">{{ t('app.facilities.currency') }}</Label>
                        <Input id="currency" v-model="form.currency" maxlength="3" placeholder="SAR" />
                        <InputError :message="form.errors.currency" />
                    </div>
                </div>
            </fieldset>

            <!-- Booking Constraints -->
            <fieldset class="space-y-4 rounded-lg border p-4">
                <legend class="px-1 text-sm font-semibold">{{ t('app.facilities.bookingConstraints') }}</legend>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="booking_horizon_days">{{ t('app.facilities.bookingHorizon') }}</Label>
                        <Input id="booking_horizon_days" v-model.number="form.booking_horizon_days" type="number" min="1" />
                        <InputError :message="form.errors.booking_horizon_days" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="cancellation_hours_before">{{ t('app.facilities.cancellationDeadline') }}</Label>
                        <Input id="cancellation_hours_before" v-model.number="form.cancellation_hours_before" type="number" min="0" />
                        <InputError :message="form.errors.cancellation_hours_before" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="min_booking_duration_minutes">{{ t('app.facilities.minDuration') }}</Label>
                        <Input id="min_booking_duration_minutes" v-model.number="form.min_booking_duration_minutes" type="number" min="1" />
                        <InputError :message="form.errors.min_booking_duration_minutes" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="max_booking_duration_minutes">{{ t('app.facilities.maxDuration') }}</Label>
                        <Input id="max_booking_duration_minutes" v-model="form.max_booking_duration_minutes" type="number" min="1" />
                        <InputError :message="form.errors.max_booking_duration_minutes" />
                    </div>
                </div>
                <label class="flex cursor-pointer items-center gap-2">
                    <input type="checkbox" v-model="form.contract_required" class="rounded border-gray-300" />
                    <span class="text-sm">{{ t('app.facilities.contractRequired') }}</span>
                </label>
            </fieldset>

            <!-- Availability Rules -->
            <fieldset class="space-y-4 rounded-lg border p-4">
                <legend class="px-1 text-sm font-semibold">{{ t('app.facilities.availabilityRules') }}</legend>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-left">
                                <th scope="col" class="py-2 pr-4 font-medium">{{ t('app.facilities.day') }}</th>
                                <th scope="col" class="py-2 pr-4 font-medium">{{ t('app.facilities.opens') }}</th>
                                <th scope="col" class="py-2 pr-4 font-medium">{{ t('app.facilities.closes') }}</th>
                                <th scope="col" class="py-2 pr-4 font-medium">{{ t('app.facilities.slotDuration') }}</th>
                                <th scope="col" class="py-2 pr-4 font-medium">{{ t('app.facilities.maxConcurrent') }}</th>
                                <th scope="col" class="py-2 font-medium">{{ t('app.common.active') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="rule in form.availability_rules" :key="rule.day_of_week" class="border-b last:border-0">
                                <th scope="row" class="py-3 pr-4 text-left font-normal">{{ dayNames[rule.day_of_week] }}</th>
                                <td class="py-3 pr-4">
                                    <Input type="time" v-model="rule.open_time" :disabled="!rule.is_active" class="w-28" />
                                </td>
                                <td class="py-3 pr-4">
                                    <Input type="time" v-model="rule.close_time" :disabled="!rule.is_active" class="w-28" />
                                </td>
                                <td class="py-3 pr-4">
                                    <select
                                        v-model.number="rule.slot_duration_minutes"
                                        :disabled="!rule.is_active"
                                        class="w-24 rounded-md border border-input bg-background px-3 py-2 text-sm disabled:opacity-50"
                                    >
                                        <option v-for="opt in slotOptions" :key="opt" :value="opt">{{ opt }} min</option>
                                    </select>
                                </td>
                                <td class="py-3 pr-4">
                                    <Input type="number" v-model.number="rule.max_concurrent_bookings" min="1" :disabled="!rule.is_active" class="w-20" />
                                </td>
                                <td class="py-3">
                                    <input type="checkbox" v-model="rule.is_active" class="rounded border-gray-300" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <InputError :message="form.errors.availability_rules" />
            </fieldset>

            <!-- Notes -->
            <div class="grid gap-2">
                <Label for="notes">{{ t('app.facilities.notes') }}</Label>
                <textarea
                    id="notes"
                    v-model="form.notes"
                    rows="3"
                    class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    :placeholder="t('app.facilities.notes')"
                />
                <InputError :message="form.errors.notes" />
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-4">
                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? t('app.actions.save') : t('app.facilities.createTitle') }}
                </Button>
                <a href="/facilities" class="text-sm text-muted-foreground hover:underline">{{ t('app.actions.cancel') }}</a>
            </div>
        </form>
    </div>
</template>
