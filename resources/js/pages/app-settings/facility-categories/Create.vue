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
            { title: t('app.appSettings.facilityCategories.pageTitle'), href: '/app-settings/facility-categories' },
            { title: t('app.appSettings.facilityCategories.newCategory'), href: '/app-settings/facility-categories/create' },
        ],
    });
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
    <Head :title="t('app.appSettings.facilityCategories.newCategory')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="t('app.appSettings.facilityCategories.createTitle')" :description="t('app.appSettings.facilityCategories.createDescription')" />

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="name_en">{{ t('app.appSettings.facilityCategories.nameEn') }}</Label>
                    <Input id="name_en" v-model="form.name_en" required :placeholder="t('app.appSettings.facilityCategories.nameEn')" />
                    <InputError :message="form.errors.name_en" />
                </div>
                <div class="grid gap-2">
                    <Label for="name_ar">{{ t('app.appSettings.facilityCategories.nameAr') }}</Label>
                    <Input id="name_ar" v-model="form.name_ar" required :placeholder="t('app.appSettings.facilityCategories.nameAr')" dir="rtl" />
                    <InputError :message="form.errors.name_ar" />
                </div>
            </div>

            <label class="flex items-center gap-2">
                <Checkbox :checked="form.status" @update:checked="form.status = $event" />
                <span class="text-sm">{{ t('app.appSettings.common.active') }}</span>
            </label>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">{{ t('app.appSettings.facilityCategories.createButton') }}</Button>
            </div>
        </form>
    </div>
</template>
