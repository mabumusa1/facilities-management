<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Community } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Buildings', href: '/buildings' },
            { title: 'New Building', href: '/buildings/create' },
        ],
    },
});

defineProps<{
    communities: Pick<Community, 'id' | 'name'>[];
}>();

const form = useForm({
    name: '',
    rf_community_id: '',
    city_id: '',
    district_id: '',
    no_floors: '',
    year_build: '',
});

function submit() {
    form.post('/buildings');
}
</script>

<template>
    <Head title="New Building" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" title="Create Building" description="Add a new building to a community." />

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-2">
                <Label for="name">Building Name</Label>
                <Input id="name" v-model="form.name" required maxlength="20" placeholder="Enter building name" />
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
                    <Input id="no_floors" v-model="form.no_floors" type="number" min="0" placeholder="0" />
                    <InputError :message="form.errors.no_floors" />
                </div>

                <div class="grid gap-2">
                    <Label for="year_build">Year Built</Label>
                    <Input id="year_build" v-model="form.year_build" type="number" min="1900" max="2099" placeholder="2024" />
                    <InputError :message="form.errors.year_build" />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">Create Building</Button>
            </div>
        </form>
    </div>
</template>
