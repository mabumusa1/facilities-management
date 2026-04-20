<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import type { Community, Unit, UnitCategory } from '@/types';

const props = defineProps<{
    unit: Unit;
    communities: Pick<Community, 'id' | 'name'>[];
    categories: UnitCategory[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Units', href: '/units' },
            { title: 'Edit', href: '#' },
        ],
    },
});

const form = useForm({
    name: props.unit.name,
    rf_community_id: String(props.unit.rf_community_id),
    rf_building_id: props.unit.rf_building_id ? String(props.unit.rf_building_id) : '',
    category_id: String(props.unit.category_id),
    type_id: String(props.unit.type_id),
    status_id: String(props.unit.status_id),
    net_area: props.unit.net_area ?? '',
    floor_no: props.unit.floor_no != null ? String(props.unit.floor_no) : '',
    year_build: props.unit.year_build ?? '',
    about: props.unit.about ?? '',
});

function submit() {
    form.put(`/units/${props.unit.id}`);
}
</script>

<template>
    <Head :title="`Edit ${unit.name}`" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="`Edit ${unit.name}`" description="Update unit details." />

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-2">
                <Label for="name">Unit Name</Label>
                <Input id="name" v-model="form.name" required />
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
                    <Label for="category_id">Category</Label>
                    <select id="category_id" v-model="form.category_id" required class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none">
                        <option value="" disabled>Select category</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                    <InputError :message="form.errors.category_id" />
                </div>

                <div class="grid gap-2">
                    <Label for="type_id">Type</Label>
                    <select id="type_id" v-model="form.type_id" required class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none">
                        <option value="" disabled>Select type</option>
                        <template v-for="cat in categories" :key="cat.id">
                            <option v-for="t in cat.types" :key="t.id" :value="t.id">{{ t.name }}</option>
                        </template>
                    </select>
                    <InputError :message="form.errors.type_id" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="grid gap-2">
                    <Label for="net_area">Net Area (sqm)</Label>
                    <Input id="net_area" v-model="form.net_area" type="number" step="0.01" min="0" />
                    <InputError :message="form.errors.net_area" />
                </div>

                <div class="grid gap-2">
                    <Label for="floor_no">Floor Number</Label>
                    <Input id="floor_no" v-model="form.floor_no" type="number" />
                    <InputError :message="form.errors.floor_no" />
                </div>

                <div class="grid gap-2">
                    <Label for="year_build">Year Built</Label>
                    <Input id="year_build" v-model="form.year_build" type="number" min="1900" max="2099" />
                    <InputError :message="form.errors.year_build" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="about">Description</Label>
                <Textarea id="about" v-model="form.about" />
                <InputError :message="form.errors.about" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">Update Unit</Button>
            </div>
        </form>
    </div>
</template>
