<script setup lang="ts">
import { computed, ref, watch, watchEffect } from 'vue';
import { Head, setLayoutProps, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { useI18n } from '@/composables/useI18n';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { Amenity, City, Community, Country, Currency, District } from '@/types';

const { t, isArabic } = useI18n();

const props = defineProps<{
    community: Community;
    countries: Pick<Country, 'id' | 'name' | 'name_en' | 'currency'>[];
    currencies: Pick<Currency, 'id' | 'name' | 'code' | 'symbol'>[];
    cities: (Pick<City, 'id' | 'name' | 'name_en'> & { country_id: number })[];
    districts: (Pick<District, 'id' | 'name' | 'name_en'> & { city_id: number })[];
    all_amenities: Pick<Amenity, 'id' | 'name' | 'name_en' | 'name_ar'>[];
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.properties.communities.pageTitle'), href: '/communities' },
            { title: t('app.properties.communities.edit.breadcrumb'), href: '#' },
        ],
    });
});

const pageTitle = computed(() => t('app.properties.communities.edit.pageTitleWithName', { name: props.community.name }));

const WORKING_DAY_KEYS = ['sat', 'sun', 'mon', 'tue', 'wed', 'thu', 'fri'] as const;
type DayKey = (typeof WORKING_DAY_KEYS)[number];

function dayLabel(key: DayKey): string {
    return t(`app.properties.communities.edit.workingDays.${key}`);
}

const form = useForm({
    name: props.community.name,
    country_id: String(props.community.country_id),
    currency_id: String(props.community.currency_id),
    city_id: String(props.community.city_id),
    district_id: String(props.community.district_id),
    sales_commission_rate: props.community.sales_commission_rate ?? '',
    rental_commission_rate: props.community.rental_commission_rate ?? '',
    amenity_ids: (props.community.amenities ?? []).map((a) => a.id),
    working_days: (props.community.working_days ?? []) as string[],
    latitude: props.community.latitude ?? '',
    longitude: props.community.longitude ?? '',
});

const filteredCities = computed(() =>
    form.country_id ? props.cities.filter((c) => c.country_id === Number(form.country_id)) : props.cities,
);

const filteredDistricts = computed(() =>
    form.city_id ? props.districts.filter((d) => d.city_id === Number(form.city_id)) : [],
);

// Only reset dependent fields when the user actively changes country/city (not on initial load)
let countryInitialized = false;
let cityInitialized = false;

watch(() => form.country_id, (countryId) => {
    if (countryInitialized) {
        form.city_id = '';
        form.district_id = '';

        // Auto-select the country's default currency
        const country = props.countries.find((c) => c.id === Number(countryId));
        if (country?.currency) {
            const match = props.currencies.find((cur) => cur.code === country.currency);
            if (match) {
                form.currency_id = String(match.id);
            }
        }
    }
    countryInitialized = true;
});

watch(() => form.city_id, () => {
    if (cityInitialized) {
        form.district_id = '';
    }
    cityInitialized = true;
});

// Amenity chip toggle
function isAmenitySelected(amenityId: number): boolean {
    return form.amenity_ids.includes(amenityId);
}

function toggleAmenity(amenityId: number): void {
    if (isAmenitySelected(amenityId)) {
        form.amenity_ids = form.amenity_ids.filter((id) => id !== amenityId);
    } else {
        form.amenity_ids = [...form.amenity_ids, amenityId];
    }
}

function amenityDisplayName(amenity: Pick<Amenity, 'id' | 'name' | 'name_en' | 'name_ar'>): string {
    return (isArabic.value ? amenity.name_ar : amenity.name_en) ?? amenity.name;
}

// Working days toggle
function isDaySelected(day: string): boolean {
    return form.working_days.includes(day);
}

function toggleDay(day: string): void {
    if (isDaySelected(day)) {
        form.working_days = form.working_days.filter((d) => d !== day);
    } else {
        form.working_days = [...form.working_days, day];
    }
}

// Geolocation
const isLocating = ref(false);
const locationDenied = ref(false);

function useMyLocation(): void {
    if (! navigator.geolocation) {
        locationDenied.value = true;
        return;
    }
    isLocating.value = true;
    locationDenied.value = false;
    navigator.geolocation.getCurrentPosition(
        (position) => {
            form.latitude = String(position.coords.latitude);
            form.longitude = String(position.coords.longitude);
            isLocating.value = false;
        },
        () => {
            isLocating.value = false;
            locationDenied.value = true;
        },
    );
}

function submit() {
    form.put(`/communities/${props.community.id}`);
}
</script>

<template>
    <Head :title="pageTitle" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="pageTitle" :description="t('app.properties.communities.edit.description')" />

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-2">
                <Label for="name">{{ t('app.properties.communities.create.communityName') }}</Label>
                <Input id="name" v-model="form.name" required :placeholder="t('app.properties.communities.create.communityNamePlaceholder')" />
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label>{{ t('app.properties.communities.create.country') }}</Label>
                    <Select v-model="form.country_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.properties.communities.create.selectCountry')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="country in countries" :key="country.id" :value="String(country.id)">
                                {{ country.name_en ?? country.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.country_id" />
                </div>

                <div class="grid gap-2">
                    <Label>{{ t('app.properties.communities.create.currency') }}</Label>
                    <Select v-model="form.currency_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.properties.communities.create.selectCurrency')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="currency in currencies" :key="currency.id" :value="String(currency.id)">
                                {{ currency.name }} ({{ currency.code }})
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.currency_id" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label>{{ t('app.properties.communities.create.city') }}</Label>
                    <Select v-model="form.city_id" :disabled="!form.country_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.properties.communities.create.selectCity')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="city in filteredCities" :key="city.id" :value="String(city.id)">
                                {{ city.name_en ?? city.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.city_id" />
                </div>

                <div class="grid gap-2">
                    <Label>{{ t('app.properties.communities.create.district') }}</Label>
                    <Select v-model="form.district_id" :disabled="!form.city_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.properties.communities.create.selectDistrict')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="district in filteredDistricts" :key="district.id" :value="String(district.id)">
                                {{ district.name_en ?? district.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.district_id" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="sales_commission_rate">{{ t('app.properties.communities.create.salesCommission') }}</Label>
                    <Input id="sales_commission_rate" v-model="form.sales_commission_rate" type="number" step="0.01" min="0" max="100" />
                    <InputError :message="form.errors.sales_commission_rate" />
                </div>

                <div class="grid gap-2">
                    <Label for="rental_commission_rate">{{ t('app.properties.communities.create.rentalCommission') }}</Label>
                    <Input id="rental_commission_rate" v-model="form.rental_commission_rate" type="number" step="0.01" min="0" max="100" />
                    <InputError :message="form.errors.rental_commission_rate" />
                </div>
            </div>

            <!-- Amenities Section -->
            <Card>
                <CardHeader>
                    <CardTitle id="amenities-label">{{ t('app.properties.communities.edit.amenities.sectionTitle') }}</CardTitle>
                    <CardDescription>{{ t('app.properties.communities.edit.amenities.sectionDescription') }}</CardDescription>
                </CardHeader>
                <CardContent>
                    <div role="group" aria-labelledby="amenities-label" class="flex flex-wrap gap-2">
                        <button
                            v-for="amenity in all_amenities"
                            :key="amenity.id"
                            type="button"
                            role="checkbox"
                            :aria-checked="isAmenitySelected(amenity.id)"
                            :class="[
                                'inline-flex items-center rounded-full border px-3 py-1 text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring',
                                isAmenitySelected(amenity.id)
                                    ? 'bg-primary text-primary-foreground border-primary'
                                    : 'bg-background text-foreground border-border hover:bg-muted',
                            ]"
                            @click="toggleAmenity(amenity.id)"
                            @keydown.backspace="isAmenitySelected(amenity.id) && toggleAmenity(amenity.id)"
                        >
                            <span :dir="isArabic ? 'rtl' : 'ltr'">{{ amenityDisplayName(amenity) }}</span>
                            <button
                                v-if="isAmenitySelected(amenity.id)"
                                type="button"
                                class="ms-1 text-primary-foreground/70 focus-visible:ring-1 focus-visible:ring-offset-1 focus-visible:outline-none"
                                :aria-label="t('app.properties.communities.edit.amenities.remove', { name: amenityDisplayName(amenity) })"
                                @click.stop="toggleAmenity(amenity.id)"
                            >×</button>
                        </button>
                        <p v-if="all_amenities.length === 0" class="text-muted-foreground text-sm">
                            {{ t('app.properties.communities.edit.amenities.noAmenities') }}
                        </p>
                    </div>
                    <InputError :message="form.errors.amenity_ids" />
                </CardContent>
            </Card>

            <!-- Working Days Section -->
            <Card>
                <CardHeader>
                    <CardTitle id="working-days-label">{{ t('app.properties.communities.edit.workingDays.sectionTitle') }}</CardTitle>
                    <CardDescription>{{ t('app.properties.communities.edit.workingDays.sectionDescription') }}</CardDescription>
                </CardHeader>
                <CardContent>
                    <div role="group" aria-labelledby="working-days-label" class="grid grid-cols-7 gap-1">
                        <button
                            v-for="day in WORKING_DAY_KEYS"
                            :key="day"
                            type="button"
                            role="checkbox"
                            :aria-checked="isDaySelected(day)"
                            :aria-label="dayLabel(day)"
                            :class="[
                                'flex min-w-[40px] flex-col items-center rounded-md border px-1 py-2 text-xs font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring',
                                isDaySelected(day)
                                    ? 'bg-primary text-primary-foreground border-primary font-semibold'
                                    : 'bg-background text-muted-foreground border-border hover:bg-muted',
                            ]"
                            @click="toggleDay(day)"
                        >
                            {{ dayLabel(day) }}
                        </button>
                    </div>
                    <InputError :message="form.errors.working_days" />
                </CardContent>
            </Card>

            <!-- Map Coordinates Section -->
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('app.properties.communities.edit.coordinates.sectionTitle') }}</CardTitle>
                    <CardDescription>{{ t('app.properties.communities.edit.coordinates.sectionDescription') }}</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div>
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            :disabled="isLocating"
                            :aria-busy="isLocating"
                            aria-label="Use my location to fill coordinates"
                            @click="useMyLocation"
                        >
                            <span v-if="isLocating">{{ t('app.properties.communities.edit.coordinates.gettingLocation') }}</span>
                            <span v-else>{{ t('app.properties.communities.edit.coordinates.useMyLocation') }}</span>
                        </Button>
                    </div>

                    <div
                        v-if="locationDenied"
                        role="status"
                        aria-live="polite"
                        class="rounded-md border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-200"
                    >
                        {{ t('app.properties.communities.edit.coordinates.permissionDenied') }}
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="latitude">{{ t('app.properties.communities.edit.coordinates.latitude') }}</Label>
                            <Input
                                id="latitude"
                                v-model="form.latitude"
                                type="number"
                                step="0.000001"
                                min="-90"
                                max="90"
                                dir="ltr"
                                class="text-left"
                                :placeholder="t('app.properties.communities.edit.coordinates.latitudePlaceholder')"
                                :aria-invalid="!!form.errors.latitude"
                                :aria-describedby="form.errors.latitude ? 'latitude-hint latitude-error' : 'latitude-hint'"
                            />
                            <p id="latitude-hint" class="text-muted-foreground text-xs">
                                {{ t('app.properties.communities.edit.coordinates.latitudeHint') }}
                            </p>
                            <InputError id="latitude-error" :message="form.errors.latitude" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="longitude">{{ t('app.properties.communities.edit.coordinates.longitude') }}</Label>
                            <Input
                                id="longitude"
                                v-model="form.longitude"
                                type="number"
                                step="0.000001"
                                min="-180"
                                max="180"
                                dir="ltr"
                                class="text-left"
                                :placeholder="t('app.properties.communities.edit.coordinates.longitudePlaceholder')"
                                :aria-invalid="!!form.errors.longitude"
                                :aria-describedby="form.errors.longitude ? 'longitude-hint longitude-error' : 'longitude-hint'"
                            />
                            <p id="longitude-hint" class="text-muted-foreground text-xs">
                                {{ t('app.properties.communities.edit.coordinates.longitudeHint') }}
                            </p>
                            <InputError id="longitude-error" :message="form.errors.longitude" />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">{{ t('app.properties.communities.edit.updateButton') }}</Button>
            </div>
        </form>
    </div>
</template>
