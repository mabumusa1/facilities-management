<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { FacilityCategory } from '@/types';

const props = defineProps<{ category: FacilityCategory }>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Facility Categories', href: '/app-settings/facility-categories' },
            { title: 'Edit', href: '#' },
        ],
    },
});

const form = useForm({
    name_ar: props.category.name_ar ?? '',
    name_en: props.category.name_en ?? '',
    status: (props.category as any).status ?? true,
});

function submit() {
    form.put(`/app-settings/facility-categories/${props.category.id}`);
}
</script>

<template>
    <Head :title="`Edit ${category.name_en ?? category.name}`" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="`Edit: ${category.name_en ?? category.name}`" description="Update facility category." />

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="name_en">Name (English)</Label>
                    <Input id="name_en" v-model="form.name_en" required />
                    <InputError :message="form.errors.name_en" />
                </div>
                <div class="grid gap-2">
                    <Label for="name_ar">Name (Arabic)</Label>
                    <Input id="name_ar" v-model="form.name_ar" required dir="rtl" />
                    <InputError :message="form.errors.name_ar" />
                </div>
            </div>

            <label class="flex items-center gap-2">
                <Checkbox :checked="form.status" @update:checked="form.status = $event" />
                <span class="text-sm">Active</span>
            </label>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">Update Category</Button>
            </div>
        </form>
    </div>
</template>
