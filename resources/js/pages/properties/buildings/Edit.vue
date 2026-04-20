<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Building, Community } from '@/types';

const props = defineProps<{
    building: Building;
    communities: Pick<Community, 'id' | 'name'>[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Buildings', href: '/buildings' },
            { title: 'Edit', href: '#' },
        ],
    },
});

const form = useForm({
    name: props.building.name,
    rf_community_id: String(props.building.rf_community_id),
    city_id: props.building.city_id ? String(props.building.city_id) : '',
    district_id: props.building.district_id ? String(props.building.district_id) : '',
    no_floors: props.building.no_floors != null ? String(props.building.no_floors) : '',
    year_build: props.building.year_build ?? '',
});

function submit() {
    form.put(`/buildings/${props.building.id}`);
}
</script>

<template>
    <Head :title="`Edit ${building.name}`" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="`Edit ${building.name}`" description="Update building details." />

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-2">
                <Label for="name">Building Name</Label>
                <Input id="name" v-model="form.name" required maxlength="20" />
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="rf_community_id">Community</Label>
                <select id="rf_community_id" v-model="form.rf_community_id" required class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none">
                    <option value="" disabled>Select community</option>
                    <option v-for="c in communities" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
                <InputError :message="form.errors.rf_community_id" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="no_floors">Number of Floors</Label>
                    <Input id="no_floors" v-model="form.no_floors" type="number" min="0" />
                    <InputError :message="form.errors.no_floors" />
                </div>

                <div class="grid gap-2">
                    <Label for="year_build">Year Built</Label>
                    <Input id="year_build" v-model="form.year_build" type="number" min="1900" max="2099" />
                    <InputError :message="form.errors.year_build" />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">Update Building</Button>
            </div>
        </form>
    </div>
</template>
