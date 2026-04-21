<script setup lang="ts">
import { Head, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

const { t, isArabic } = useI18n();

type Option = {
    id: number;
    name?: string | null;
    name_ar?: string | null;
    name_en?: string | null;
};

type Facility = {
    id: number;
    name: string;
    name_ar: string | null;
    name_en: string | null;
    category_id: number;
    community_id: number | null;
    description: string | null;
    capacity: number | null;
    open_time: string | null;
    close_time: string | null;
    booking_fee: string;
    is_active: boolean;
    requires_approval: boolean;
};

const props = defineProps<{
    facility: Facility | null;
    categories: Option[];
    communities: { id: number; name: string }[];
}>();

const isEdit = computed(() => Boolean(props.facility));

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.settings'), href: '/settings/invoice' },
            { title: t('app.navigation.facilities'), href: '/settings/facilities' },
        ],
    });
});

const form = useForm({
    name: props.facility?.name ?? '',
    name_ar: props.facility?.name_ar ?? '',
    name_en: props.facility?.name_en ?? '',
    category_id: props.facility?.category_id ?? 0,
    community_id: props.facility?.community_id ?? null as number | null,
    description: props.facility?.description ?? '',
    capacity: props.facility?.capacity ?? null as number | null,
    open_time: props.facility?.open_time ?? '',
    close_time: props.facility?.close_time ?? '',
    booking_fee: props.facility?.booking_fee ?? '0',
    is_active: props.facility?.is_active ?? true,
    requires_approval: props.facility?.requires_approval ?? false,
});

function localizedOptionName(option: Option): string {
    if (isArabic.value) {
        return option.name_ar ?? option.name ?? option.name_en ?? '';
    }

    return option.name_en ?? option.name ?? option.name_ar ?? '';
}

function submit() {
    if (isEdit.value && props.facility) {
        form.put(`/settings/facilities/${props.facility.id}`);
        return;
    }

    form.post('/settings/facilities');
}
</script>

<template>
    <Head :title="isEdit ? t('app.appSettings.facilities.editFacility') : t('app.appSettings.facilities.addFacility')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            variant="small"
            :title="isEdit ? t('app.appSettings.facilities.editFacility') : t('app.appSettings.facilities.addFacility')"
            :description="t('app.appSettings.facilities.formDescription')"
        />

        <Card>
            <CardHeader>
                <CardTitle>{{ isEdit ? t('app.appSettings.facilities.updateFacility') : t('app.appSettings.facilities.createFacility') }}</CardTitle>
            </CardHeader>
            <CardContent>
                <form class="grid gap-4" @submit.prevent="submit">
                    <div class="grid gap-2">
                        <Label for="name">{{ t('app.appSettings.facilities.name') }}</Label>
                        <Input id="name" v-model="form.name" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="name_en">{{ t('app.appSettings.facilities.nameEn') }}</Label>
                            <Input id="name_en" v-model="form.name_en" />
                            <InputError :message="form.errors.name_en" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="name_ar">{{ t('app.appSettings.facilities.nameAr') }}</Label>
                            <Input id="name_ar" v-model="form.name_ar" />
                            <InputError :message="form.errors.name_ar" />
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="category_id">{{ t('app.appSettings.facilities.category') }}</Label>
                            <select id="category_id" v-model.number="form.category_id" class="rounded-md border border-input bg-background px-3 py-2">
                                <option :value="0">{{ t('app.appSettings.facilities.selectCategory') }}</option>
                                <option v-for="category in props.categories" :key="category.id" :value="category.id">
                                    {{ localizedOptionName(category) }}
                                </option>
                            </select>
                            <InputError :message="form.errors.category_id" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="community_id">{{ t('app.appSettings.facilities.community') }}</Label>
                            <select id="community_id" v-model.number="form.community_id" class="rounded-md border border-input bg-background px-3 py-2">
                                <option :value="null">{{ t('app.appSettings.facilities.selectCommunity') }}</option>
                                <option v-for="community in props.communities" :key="community.id" :value="community.id">
                                    {{ community.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.community_id" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="description">{{ t('app.appSettings.facilities.descriptionLabel') }}</Label>
                        <Textarea id="description" v-model="form.description" rows="3" />
                        <InputError :message="form.errors.description" />
                    </div>

                    <div class="grid gap-4 md:grid-cols-4">
                        <div class="grid gap-2">
                            <Label for="capacity">{{ t('app.appSettings.facilities.capacity') }}</Label>
                            <Input id="capacity" v-model.number="form.capacity" type="number" min="1" />
                            <InputError :message="form.errors.capacity" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="open_time">{{ t('app.appSettings.facilities.open') }}</Label>
                            <Input id="open_time" v-model="form.open_time" type="time" />
                            <InputError :message="form.errors.open_time" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="close_time">{{ t('app.appSettings.facilities.close') }}</Label>
                            <Input id="close_time" v-model="form.close_time" type="time" />
                            <InputError :message="form.errors.close_time" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="booking_fee">{{ t('app.appSettings.facilities.bookingFee') }}</Label>
                            <Input id="booking_fee" v-model="form.booking_fee" type="number" min="0" step="0.01" />
                            <InputError :message="form.errors.booking_fee" />
                        </div>
                    </div>

                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 text-sm">
                            <input v-model="form.is_active" type="checkbox" />
                            {{ t('app.common.active') }}
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input v-model="form.requires_approval" type="checkbox" />
                            {{ t('app.appSettings.facilities.requiresApproval') }}
                        </label>
                    </div>

                    <div class="flex justify-end">
                        <Button :disabled="form.processing">{{ isEdit ? t('app.appSettings.facilities.updateFacility') : t('app.appSettings.facilities.createFacility') }}</Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
