<script setup lang="ts">
import { Head, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, watch, watchEffect } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { useI18n } from '@/composables/useI18n';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { City, Community, District } from '@/types';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.properties.buildings.pageTitle'), href: '/buildings' },
            { title: t('app.properties.buildings.newBuilding'), href: '/buildings/create' },
        ],
    });
});

const props = defineProps<{
    communities: Pick<Community, 'id' | 'name'>[];
    cities: (Pick<City, 'id' | 'name' | 'name_en'> & { country_id: number })[];
    districts: (Pick<District, 'id' | 'name' | 'name_en'> & { city_id: number })[];
}>();

const form = useForm({
    name: '',
    rf_community_id: '',
    city_id: '',
    district_id: '',
    no_floors: '',
    year_build: '',
});

const filteredDistricts = computed(() =>
    form.city_id ? props.districts.filter((district) => district.city_id === Number(form.city_id)) : [],
);

watch(() => form.city_id, () => {
    form.district_id = '';
});

function submit() {
    form.post('/buildings');
}
</script>

<template>
    <Head :title="t('app.properties.buildings.create.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            variant="small"
            :title="t('app.properties.buildings.create.heading')"
            :description="t('app.properties.buildings.create.description')"
        />

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-2">
                <Label for="name">{{ t('app.properties.buildings.create.buildingName') }}</Label>
                <Input id="name" v-model="form.name" required maxlength="20" :placeholder="t('app.properties.buildings.create.buildingNamePlaceholder')" />
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="rf_community_id">{{ t('app.properties.buildings.create.community') }}</Label>
                <select id="rf_community_id" v-model="form.rf_community_id" required class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none">
                    <option value="" disabled>{{ t('app.properties.buildings.create.selectCommunity') }}</option>
                    <option v-for="c in communities" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
                <InputError :message="form.errors.rf_community_id" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="city_id">{{ t('app.properties.buildings.create.city') }}</Label>
                    <select id="city_id" v-model="form.city_id" class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none">
                        <option value="">{{ t('app.properties.buildings.create.selectCity') }}</option>
                        <option v-for="city in cities" :key="city.id" :value="city.id">{{ city.name_en ?? city.name }}</option>
                    </select>
                    <InputError :message="form.errors.city_id" />
                </div>

                <div class="grid gap-2">
                    <Label for="district_id">{{ t('app.properties.buildings.create.district') }}</Label>
                    <select id="district_id" v-model="form.district_id" :disabled="!form.city_id" class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none">
                        <option value="">{{ t('app.properties.buildings.create.selectDistrict') }}</option>
                        <option v-for="district in filteredDistricts" :key="district.id" :value="district.id">{{ district.name_en ?? district.name }}</option>
                    </select>
                    <InputError :message="form.errors.district_id" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="no_floors">{{ t('app.properties.buildings.create.floors') }}</Label>
                    <Input id="no_floors" v-model="form.no_floors" type="number" min="0" :placeholder="t('app.properties.buildings.create.floorsPlaceholder')" />
                    <InputError :message="form.errors.no_floors" />
                </div>

                <div class="grid gap-2">
                    <Label for="year_build">{{ t('app.properties.buildings.create.yearBuilt') }}</Label>
                    <Input id="year_build" v-model="form.year_build" type="number" min="1900" max="2099" :placeholder="t('app.properties.buildings.create.yearPlaceholder')" />
                    <InputError :message="form.errors.year_build" />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">{{ t('app.properties.buildings.create.createButton') }}</Button>
            </div>
        </form>
    </div>
</template>
