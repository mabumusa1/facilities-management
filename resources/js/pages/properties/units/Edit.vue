<script setup lang="ts">
import { Head, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, watch, watchEffect } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { useI18n } from '@/composables/useI18n';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import type { Building, City, Community, District, Owner, Resident, Status, Unit, UnitCategory } from '@/types';

const props = defineProps<{
    unit: Unit;
    communities: Pick<Community, 'id' | 'name'>[];
    buildings: (Pick<Building, 'id' | 'name'> & { rf_community_id: number })[];
    categories: UnitCategory[];
    statuses: Pick<Status, 'id' | 'name' | 'name_en'>[];
    owners: Pick<Owner, 'id' | 'first_name' | 'last_name'>[];
    residents: Pick<Resident, 'id' | 'first_name' | 'last_name'>[];
    cities: (Pick<City, 'id' | 'name' | 'name_en'> & { country_id: number })[];
    districts: (Pick<District, 'id' | 'name' | 'name_en'> & { city_id: number })[];
}>();

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.properties.units.pageTitle'), href: '/units' },
            { title: t('app.properties.units.edit.breadcrumb'), href: '#' },
        ],
    });
});

const pageTitle = computed(() => t('app.properties.units.edit.pageTitleWithName', { name: props.unit.name }));

const form = useForm({
    name: props.unit.name,
    rf_community_id: String(props.unit.rf_community_id),
    rf_building_id: props.unit.rf_building_id ? String(props.unit.rf_building_id) : '',
    category_id: String(props.unit.category_id),
    type_id: String(props.unit.type_id),
    status_id: String(props.unit.status_id),
    owner_id: props.unit.owner_id ? String(props.unit.owner_id) : '',
    tenant_id: props.unit.tenant_id ? String(props.unit.tenant_id) : '',
    city_id: props.unit.city_id ? String(props.unit.city_id) : '',
    district_id: props.unit.district_id ? String(props.unit.district_id) : '',
    net_area: props.unit.net_area ?? '',
    floor_no: props.unit.floor_no != null ? String(props.unit.floor_no) : '',
    year_build: props.unit.year_build ?? '',
    about: props.unit.about ?? '',
});

const filteredBuildings = computed(() =>
    form.rf_community_id
        ? props.buildings.filter((building) => building.rf_community_id === Number(form.rf_community_id))
        : [],
);

const filteredTypes = computed(() => {
    if (!form.category_id) {
        return [];
    }

    const selectedCategory = props.categories.find((category) => category.id === Number(form.category_id));

    return selectedCategory?.types ?? [];
});

const filteredDistricts = computed(() =>
    form.city_id ? props.districts.filter((district) => district.city_id === Number(form.city_id)) : [],
);

watch(() => form.rf_community_id, () => {
    form.rf_building_id = '';
});

watch(() => form.category_id, () => {
    form.type_id = '';
});

watch(() => form.city_id, () => {
    form.district_id = '';
});

function submit() {
    form.put(`/units/${props.unit.id}`);
}
</script>

<template>
    <Head :title="pageTitle" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="pageTitle" :description="t('app.properties.units.edit.description')" />

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-2">
                <Label for="name">{{ t('app.properties.units.create.unitName') }}</Label>
                <Input id="name" v-model="form.name" required />
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="rf_community_id">{{ t('app.properties.units.create.community') }}</Label>
                <select id="rf_community_id" v-model="form.rf_community_id" required class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none">
                    <option value="" disabled>{{ t('app.properties.units.create.selectCommunity') }}</option>
                    <option v-for="c in communities" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
                <InputError :message="form.errors.rf_community_id" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="rf_building_id">{{ t('app.properties.units.create.building') }}</Label>
                    <select id="rf_building_id" v-model="form.rf_building_id" :disabled="!form.rf_community_id" class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none">
                        <option value="">{{ t('app.properties.units.create.selectBuilding') }}</option>
                        <option v-for="building in filteredBuildings" :key="building.id" :value="building.id">{{ building.name }}</option>
                    </select>
                    <InputError :message="form.errors.rf_building_id" />
                </div>

                <div class="grid gap-2">
                    <Label for="status_id">{{ t('app.properties.units.create.status') }}</Label>
                    <select id="status_id" v-model="form.status_id" required class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none">
                        <option value="" disabled>{{ t('app.properties.units.create.selectStatus') }}</option>
                        <option v-for="status in statuses" :key="status.id" :value="status.id">{{ status.name_en ?? status.name }}</option>
                    </select>
                    <InputError :message="form.errors.status_id" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="category_id">{{ t('app.properties.units.create.category') }}</Label>
                    <select id="category_id" v-model="form.category_id" required class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none">
                        <option value="" disabled>{{ t('app.properties.units.create.selectCategory') }}</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                    <InputError :message="form.errors.category_id" />
                </div>

                <div class="grid gap-2">
                    <Label for="type_id">{{ t('app.properties.units.create.type') }}</Label>
                    <select id="type_id" v-model="form.type_id" required :disabled="!form.category_id" class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none">
                        <option value="" disabled>{{ t('app.properties.units.create.selectType') }}</option>
                        <option v-for="type in filteredTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
                    </select>
                    <InputError :message="form.errors.type_id" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="owner_id">{{ t('app.properties.units.create.owner') }}</Label>
                    <select id="owner_id" v-model="form.owner_id" class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none">
                        <option value="">{{ t('app.properties.units.create.selectOwner') }}</option>
                        <option v-for="owner in owners" :key="owner.id" :value="owner.id">{{ owner.first_name }} {{ owner.last_name }}</option>
                    </select>
                    <InputError :message="form.errors.owner_id" />
                </div>

                <div class="grid gap-2">
                    <Label for="tenant_id">{{ t('app.properties.units.create.tenant') }}</Label>
                    <select id="tenant_id" v-model="form.tenant_id" class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none">
                        <option value="">{{ t('app.properties.units.create.selectTenant') }}</option>
                        <option v-for="resident in residents" :key="resident.id" :value="resident.id">{{ resident.first_name }} {{ resident.last_name }}</option>
                    </select>
                    <InputError :message="form.errors.tenant_id" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="city_id">{{ t('app.properties.units.create.city') }}</Label>
                    <select id="city_id" v-model="form.city_id" class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none">
                        <option value="">{{ t('app.properties.units.create.selectCity') }}</option>
                        <option v-for="city in cities" :key="city.id" :value="city.id">{{ city.name_en ?? city.name }}</option>
                    </select>
                    <InputError :message="form.errors.city_id" />
                </div>

                <div class="grid gap-2">
                    <Label for="district_id">{{ t('app.properties.units.create.district') }}</Label>
                    <select id="district_id" v-model="form.district_id" :disabled="!form.city_id" class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none">
                        <option value="">{{ t('app.properties.units.create.selectDistrict') }}</option>
                        <option v-for="district in filteredDistricts" :key="district.id" :value="district.id">{{ district.name_en ?? district.name }}</option>
                    </select>
                    <InputError :message="form.errors.district_id" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="grid gap-2">
                    <Label for="net_area">{{ t('app.properties.units.create.netArea') }}</Label>
                    <Input id="net_area" v-model="form.net_area" type="number" step="0.01" min="0" />
                    <InputError :message="form.errors.net_area" />
                </div>

                <div class="grid gap-2">
                    <Label for="floor_no">{{ t('app.properties.units.create.floorNumber') }}</Label>
                    <Input id="floor_no" v-model="form.floor_no" type="number" />
                    <InputError :message="form.errors.floor_no" />
                </div>

                <div class="grid gap-2">
                    <Label for="year_build">{{ t('app.properties.units.create.yearBuilt') }}</Label>
                    <Input id="year_build" v-model="form.year_build" type="number" min="1900" max="2099" />
                    <InputError :message="form.errors.year_build" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="about">{{ t('app.properties.units.create.descriptionLabel') }}</Label>
                <Textarea id="about" v-model="form.about" />
                <InputError :message="form.errors.about" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">{{ t('app.properties.units.edit.updateButton') }}</Button>
            </div>
        </form>
    </div>
</template>
