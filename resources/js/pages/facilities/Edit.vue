<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { Community, Facility, FacilityCategory } from '@/types';

const props = defineProps<{
    facility: Facility;
    categories: FacilityCategory[];
    communities: Pick<Community, 'id' | 'name'>[];
}>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Facilities', href: '/facilities' }, { title: 'Edit', href: '#' }] } });

const form = useForm({
    name: props.facility.name,
    category_id: String(props.facility.category_id ?? ''),
    community_id: String(props.facility.community_id ?? ''),
    capacity: props.facility.capacity ?? '',
    is_active: props.facility.is_active ?? true,
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
                <div class="grid gap-2">
                    <Label>Category</Label>
                    <Select v-model="form.category_id">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select category" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="cat in categories" :key="cat.id" :value="String(cat.id)">
                                {{ cat.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.category_id" />
                </div>
                <div class="grid gap-2">
                    <Label>Community</Label>
                    <Select v-model="form.community_id">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select community" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="community in communities" :key="community.id" :value="String(community.id)">
                                {{ community.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.community_id" />
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2"><Label for="capacity">Capacity</Label><Input id="capacity" v-model="form.capacity" type="number" /><InputError :message="form.errors.capacity" /></div>
                <div class="flex items-end gap-2"><label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_active" class="rounded border-gray-300" /><span class="text-sm">Active</span></label></div>
            </div>
            <div class="flex items-center gap-4"><Button :disabled="form.processing">Update Facility</Button></div>
        </form>
    </div>
</template>
