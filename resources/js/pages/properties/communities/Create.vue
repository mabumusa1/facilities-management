<script setup lang="ts">
import { computed, watch, watchEffect } from 'vue';
import { Head, setLayoutProps, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { useI18n } from '@/composables/useI18n';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { City, Country, Currency, District } from '@/types';

const { t } = useI18n();

const props = defineProps<{
    countries: Pick<Country, 'id' | 'name' | 'name_en' | 'currency'>[];
    currencies: Pick<Currency, 'id' | 'name' | 'code' | 'symbol'>[];
    cities: (Pick<City, 'id' | 'name' | 'name_en'> & { country_id: number })[];
    districts: (Pick<District, 'id' | 'name' | 'name_en'> & { city_id: number })[];
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.properties.communities.pageTitle'), href: '/communities' },
            { title: t('app.properties.communities.newCommunity'), href: '/communities/create' },
        ],
    });
});

const form = useForm({
    name: '',
    country_id: '',
    currency_id: '',
    city_id: '',
    district_id: '',
    sales_commission_rate: '',
    rental_commission_rate: '',
});

const filteredCities = computed(() =>
    form.country_id ? props.cities.filter((c) => c.country_id === Number(form.country_id)) : props.cities,
);

const filteredDistricts = computed(() =>
    form.city_id ? props.districts.filter((d) => d.city_id === Number(form.city_id)) : [],
);

watch(() => form.country_id, (countryId) => {
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
});

watch(() => form.city_id, () => {
    form.district_id = '';
});

function submit() {
    form.post('/communities');
}
</script>

<template>
    <Head :title="t('app.properties.communities.create.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            variant="small"
            :title="t('app.properties.communities.create.heading')"
            :description="t('app.properties.communities.create.description')"
        />

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
                    <Input
                        id="sales_commission_rate"
                        v-model="form.sales_commission_rate"
                        type="number"
                        step="0.01"
                        min="0"
                        max="100"
                        :placeholder="t('app.properties.communities.create.ratePlaceholder')"
                    />
                    <InputError :message="form.errors.sales_commission_rate" />
                </div>

                <div class="grid gap-2">
                    <Label for="rental_commission_rate">{{ t('app.properties.communities.create.rentalCommission') }}</Label>
                    <Input
                        id="rental_commission_rate"
                        v-model="form.rental_commission_rate"
                        type="number"
                        step="0.01"
                        min="0"
                        max="100"
                        :placeholder="t('app.properties.communities.create.ratePlaceholder')"
                    />
                    <InputError :message="form.errors.rental_commission_rate" />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">{{ t('app.properties.communities.create.createButton') }}</Button>
            </div>
        </form>
    </div>
</template>
