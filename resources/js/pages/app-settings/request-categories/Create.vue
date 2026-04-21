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
            { title: 'Request Categories', href: '/app-settings/request-categories' },
            { title: 'New Category', href: '/app-settings/request-categories/create' },
        ],
    },
});

const form = useForm({
    name_ar: '',
    name_en: '',
    status: true,
    has_sub_categories: true,
});

function submit() {
    form.post('/app-settings/request-categories');
}
</script>

<template>
    <Head title="New Request Category" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" title="Create Request Category" description="Add a new service request category." />

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="name_en">Name (English)</Label>
                    <Input id="name_en" v-model="form.name_en" required placeholder="e.g. Unit Services" />
                    <InputError :message="form.errors.name_en" />
                </div>
                <div class="grid gap-2">
                    <Label for="name_ar">Name (Arabic)</Label>
                    <Input id="name_ar" v-model="form.name_ar" required placeholder="مثال: خدمات الوحدات" dir="rtl" />
                    <InputError :message="form.errors.name_ar" />
                </div>
            </div>

            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2">
                    <Checkbox :checked="form.status" @update:checked="form.status = $event" />
                    <span class="text-sm">Active</span>
                </label>
                <label class="flex items-center gap-2">
                    <Checkbox :checked="form.has_sub_categories" @update:checked="form.has_sub_categories = $event" />
                    <span class="text-sm">Has Subcategories</span>
                </label>
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">Create Category</Button>
            </div>
        </form>
    </div>
</template>
