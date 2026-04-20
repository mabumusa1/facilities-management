<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Facility } from '@/types';

const props = defineProps<{ facility: Facility }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Facilities', href: '/facilities' }, { title: 'Edit', href: '#' }] } });

const form = useForm({
    name: props.facility.name,
    category_id: props.facility.category_id ?? '',
    community_id: props.facility.community_id ?? '',
    max_capacity: props.facility.max_capacity ?? '',
    active: props.facility.active ?? true,
});

function submit() { form.put(`/facilities/${props.facility.id}`); }
</script>

<template>
    <Head title="Edit Facility" />
    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" title="Edit Facility" description="Update facility details." />
        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-2"><Label for="name">Name</Label><Input id="name" v-model="form.name" required /><InputError :message="form.errors.name" /></div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2"><Label for="category_id">Category ID</Label><Input id="category_id" v-model="form.category_id" type="number" /><InputError :message="form.errors.category_id" /></div>
                <div class="grid gap-2"><Label for="community_id">Community ID</Label><Input id="community_id" v-model="form.community_id" type="number" /><InputError :message="form.errors.community_id" /></div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2"><Label for="max_capacity">Max Capacity</Label><Input id="max_capacity" v-model="form.max_capacity" type="number" /><InputError :message="form.errors.max_capacity" /></div>
                <div class="flex items-end gap-2"><label class="flex items-center gap-2"><input type="checkbox" v-model="form.active" class="rounded border-gray-300" /><span class="text-sm">Active</span></label></div>
            </div>
            <div class="flex items-center gap-4"><Button :disabled="form.processing">Update Facility</Button></div>
        </form>
    </div>
</template>
