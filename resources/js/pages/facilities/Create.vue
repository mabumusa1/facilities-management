<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { FacilityCategory } from '@/types';

defineOptions({ layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Facilities', href: '/facilities' }, { title: 'New Facility', href: '/facilities/create' }] } });

defineProps<{ categories: FacilityCategory[] }>();

const form = useForm({ name: '', category_id: '', community_id: '', capacity: '' });

function submit() { form.post('/facilities'); }
</script>

<template>
    <Head title="New Facility" />
    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" title="Create Facility" description="Add a new shared facility." />
        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-2"><Label for="name">Facility Name</Label><Input id="name" v-model="form.name" required placeholder="e.g. Swimming Pool" /><InputError :message="form.errors.name" /></div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="category_id">Category</Label>
                    <select id="category_id" v-model="form.category_id" required class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none">
                        <option value="" disabled>Select category</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                    <InputError :message="form.errors.category_id" />
                </div>
                <div class="grid gap-2"><Label for="community_id">Community ID</Label><Input id="community_id" v-model="form.community_id" type="number" required /><InputError :message="form.errors.community_id" /></div>
            </div>
            <div class="grid gap-2"><Label for="capacity">Capacity</Label><Input id="capacity" v-model="form.capacity" type="number" min="1" placeholder="Maximum occupancy" /><InputError :message="form.errors.capacity" /></div>
            <div class="flex items-center gap-4"><Button :disabled="form.processing">Create Facility</Button></div>
        </form>
    </div>
</template>
