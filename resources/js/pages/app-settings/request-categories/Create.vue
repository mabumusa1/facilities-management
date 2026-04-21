<script setup lang="ts">
import { Head, setLayoutProps, useForm } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { useI18n } from '@/composables/useI18n';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.appSettings.requestCategories.pageTitle'), href: '/app-settings/request-categories' },
            { title: t('app.appSettings.requestCategories.newCategory'), href: '/app-settings/request-categories/create' },
        ],
    });
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
    <Head :title="t('app.appSettings.requestCategories.newCategory')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="t('app.appSettings.requestCategories.createTitle')" :description="t('app.appSettings.requestCategories.createDescription')" />

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="name_en">{{ t('app.appSettings.requestCategories.nameEn') }}</Label>
                    <Input id="name_en" v-model="form.name_en" required :placeholder="t('app.appSettings.requestCategories.nameEn')" />
                    <InputError :message="form.errors.name_en" />
                </div>
                <div class="grid gap-2">
                    <Label for="name_ar">{{ t('app.appSettings.requestCategories.nameAr') }}</Label>
                    <Input id="name_ar" v-model="form.name_ar" required :placeholder="t('app.appSettings.requestCategories.nameAr')" dir="rtl" />
                    <InputError :message="form.errors.name_ar" />
                </div>
            </div>

            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2">
                    <Checkbox :checked="form.status" @update:checked="form.status = $event" />
                    <span class="text-sm">{{ t('app.appSettings.common.active') }}</span>
                </label>
                <label class="flex items-center gap-2">
                    <Checkbox :checked="form.has_sub_categories" @update:checked="form.has_sub_categories = $event" />
                    <span class="text-sm">{{ t('app.appSettings.requestCategories.hasSubcategories') }}</span>
                </label>
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">{{ t('app.appSettings.requestCategories.createButton') }}</Button>
            </div>
        </form>
    </div>
</template>
