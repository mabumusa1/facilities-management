<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Facility Categories', href: '/app-settings/facility-categories' },
            { title: 'New Category', href: '/app-settings/facility-categories/create' },
        ],
    },
});

const form = useForm({
    name_ar: '',
    name_en: '',
    status: true,
});

function submit() {
    form.post('/app-settings/facility-categories');
}
</script>

<template>
    <Head title="New Facility Category" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" title="Create Facility Category" description="Add a new facility category." />

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="name_en">Name (English)</Label>
                    <Input id="name_en" v-model="form.name_en" required placeholder="e.g. Swimming Pools" />
                    <InputError :message="form.errors.name_en" />
                </div>
                <div class="grid gap-2">
                    <Label for="name_ar">Name (Arabic)</Label>
                    <Input id="name_ar" v-model="form.name_ar" required placeholder="مثال: مسابح" dir="rtl" />
                    <InputError :message="form.errors.name_ar" />
                </div>
            </div>

            <label class="flex items-center gap-2">
                <Checkbox :checked="form.status" @update:checked="form.status = $event" />
                <span class="text-sm">Active</span>
            </label>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">Create Category</Button>
            </div>
        </form>
    </div>
</template>
