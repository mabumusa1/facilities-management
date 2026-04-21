<script setup lang="ts">
import { Head, router, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, ref, watchEffect } from 'vue';
import { update as updateRequestCategory } from '@/actions/App/Http/Controllers/AppSettings/RequestCategoryController';
import { destroy as destroyRequestSubcategory, store as storeRequestSubcategory } from '@/actions/App/Http/Controllers/AppSettings/RequestSubcategoryController';
import { updateOrCreate as updateOrCreateServiceSetting } from '@/actions/App/Http/Controllers/AppSettings/ServiceSettingController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useI18n } from '@/composables/useI18n';
import type { RequestCategory, RequestSubcategory, ServiceSetting } from '@/types';

const props = defineProps<{
    category: RequestCategory & { subcategories?: RequestSubcategory[] };
    serviceSetting: ServiceSetting | null;
}>();

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.appSettings.requestCategories.pageTitle'), href: '/app-settings/request-categories' },
            { title: t('app.appSettings.common.edit'), href: '#' },
        ],
    });
});

const pageTitle = computed(() => t('app.appSettings.requestCategories.editTitle', { name: props.category.name_en ?? props.category.name }));

const form = useForm({
    name_ar: props.category.name_ar ?? '',
    name_en: props.category.name_en ?? '',
    status: (props.category as any).status ?? true,
    has_sub_categories: (props.category as any).has_sub_categories ?? true,
});

function submit() {
    form.put(updateRequestCategory(props.category.id).url);
}

type ServicePermissions = {
    manager_close_Request: boolean;
    not_require_professional_enter_request_code: boolean;
    not_require_professional_upload_request_photo: boolean;
    attachments_required: boolean;
    allow_professional_reschedule: boolean;
};

function normalizePermissions(value: unknown): ServicePermissions {
    const defaults: ServicePermissions = {
        manager_close_Request: false,
        not_require_professional_enter_request_code: false,
        not_require_professional_upload_request_photo: false,
        attachments_required: false,
        allow_professional_reschedule: false,
    };

    if (!value || typeof value !== 'object' || Array.isArray(value)) {
        return defaults;
    }

    const raw = value as Record<string, unknown>;

    return {
        manager_close_Request: Boolean(raw.manager_close_Request),
        not_require_professional_enter_request_code: Boolean(raw.not_require_professional_enter_request_code),
        not_require_professional_upload_request_photo: Boolean(raw.not_require_professional_upload_request_photo),
        attachments_required: Boolean(raw.attachments_required),
        allow_professional_reschedule: Boolean(raw.allow_professional_reschedule),
    };
}

const serviceForm = useForm({
    rf_category_id: props.category.id,
    permissions: normalizePermissions(props.serviceSetting?.permissions ?? null),
});

function saveServiceSettings() {
    serviceForm.post(updateOrCreateServiceSetting().url, {
        preserveScroll: true,
    });
}

// Subcategory management
const showSubcategoryForm = ref(false);
const subForm = useForm({
    name_ar: '',
    name_en: '',
    status: true,
    is_all_day: true,
    start: '',
    end: '',
    terms_and_conditions: '',
});

function addSubcategory() {
    subForm.post(storeRequestSubcategory(props.category.id).url, {
        preserveScroll: true,
        onSuccess: () => {
            subForm.reset();
            showSubcategoryForm.value = false;
        },
    });
}

function deleteSubcategory(subId: number) {
    if (confirm(t('app.appSettings.requestCategories.deleteSubcategoryConfirm'))) {
        router.delete(destroyRequestSubcategory([props.category.id, subId]).url, {
            preserveScroll: true,
        });
    }
}
</script>

<template>
    <Head :title="pageTitle" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="t('app.appSettings.requestCategories.editHeading', { name: category.name_en ?? category.name })" :description="t('app.appSettings.requestCategories.editDescription')" />

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="name_en">{{ t('app.appSettings.requestCategories.nameEn') }}</Label>
                    <Input id="name_en" v-model="form.name_en" required />
                    <InputError :message="form.errors.name_en" />
                </div>
                <div class="grid gap-2">
                    <Label for="name_ar">{{ t('app.appSettings.requestCategories.nameAr') }}</Label>
                    <Input id="name_ar" v-model="form.name_ar" required dir="rtl" />
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
                <Button :disabled="form.processing">{{ t('app.appSettings.requestCategories.updateButton') }}</Button>
            </div>
        </form>

        <Separator />

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.appSettings.requestCategories.serviceSettingsTitle') }}</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
                <p class="text-muted-foreground text-sm">
                    {{ t('app.appSettings.requestCategories.serviceSettingsDescription') }}
                </p>

                <div class="grid gap-3 sm:grid-cols-2">
                    <label class="flex items-center gap-2">
                        <Checkbox :checked="serviceForm.permissions.manager_close_Request" @update:checked="serviceForm.permissions.manager_close_Request = !!$event" />
                        <span class="text-sm">{{ t('app.appSettings.requestCategories.managerCloseRequest') }}</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <Checkbox :checked="serviceForm.permissions.attachments_required" @update:checked="serviceForm.permissions.attachments_required = !!$event" />
                        <span class="text-sm">{{ t('app.appSettings.requestCategories.attachmentsRequired') }}</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <Checkbox :checked="serviceForm.permissions.allow_professional_reschedule" @update:checked="serviceForm.permissions.allow_professional_reschedule = !!$event" />
                        <span class="text-sm">{{ t('app.appSettings.requestCategories.allowProfessionalReschedule') }}</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <Checkbox :checked="serviceForm.permissions.not_require_professional_enter_request_code" @update:checked="serviceForm.permissions.not_require_professional_enter_request_code = !!$event" />
                        <span class="text-sm">{{ t('app.appSettings.requestCategories.skipRequestCodeForProfessional') }}</span>
                    </label>
                    <label class="flex items-center gap-2 sm:col-span-2">
                        <Checkbox :checked="serviceForm.permissions.not_require_professional_upload_request_photo" @update:checked="serviceForm.permissions.not_require_professional_upload_request_photo = !!$event" />
                        <span class="text-sm">{{ t('app.appSettings.requestCategories.skipRequestPhotoForProfessional') }}</span>
                    </label>
                </div>

                <InputError :message="serviceForm.errors.permissions" />

                <div class="flex items-center gap-4">
                    <Button :disabled="serviceForm.processing" @click="saveServiceSettings">{{ t('app.appSettings.requestCategories.saveServiceSettings') }}</Button>
                </div>
            </CardContent>
        </Card>

        <Separator />

        <!-- Subcategories Section -->
        <Card>
            <CardHeader class="flex flex-row items-center justify-between">
                <CardTitle>{{ t('app.appSettings.requestCategories.subcategories') }}</CardTitle>
                <Button size="sm" @click="showSubcategoryForm = !showSubcategoryForm">
                    {{ showSubcategoryForm ? t('app.appSettings.requestCategories.cancelSubcategory') : t('app.appSettings.requestCategories.addSubcategory') }}
                </Button>
            </CardHeader>
            <CardContent>
                <!-- Add subcategory form -->
                <form v-if="showSubcategoryForm" @submit.prevent="addSubcategory" class="mb-6 space-y-4 rounded-lg border p-4">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label>{{ t('app.appSettings.requestCategories.nameEn') }}</Label>
                            <Input v-model="subForm.name_en" required :placeholder="t('app.appSettings.requestCategories.subcategoryNamePlaceholder')" />
                            <InputError :message="subForm.errors.name_en" />
                        </div>
                        <div class="grid gap-2">
                            <Label>{{ t('app.appSettings.requestCategories.nameAr') }}</Label>
                            <Input v-model="subForm.name_ar" required :placeholder="t('app.appSettings.requestCategories.subcategoryNameArPlaceholder')" dir="rtl" />
                            <InputError :message="subForm.errors.name_ar" />
                        </div>
                    </div>
                    <div class="flex items-center gap-6">
                        <label class="flex items-center gap-2">
                            <Checkbox :checked="subForm.status" @update:checked="subForm.status = $event" />
                            <span class="text-sm">{{ t('app.appSettings.common.active') }}</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <Checkbox :checked="subForm.is_all_day" @update:checked="subForm.is_all_day = $event" />
                            <span class="text-sm">{{ t('app.appSettings.requestCategories.allDay') }}</span>
                        </label>
                    </div>
                    <div v-if="!subForm.is_all_day" class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label>{{ t('app.appSettings.requestCategories.startTime') }}</Label>
                            <Input v-model="subForm.start" type="time" />
                        </div>
                        <div class="grid gap-2">
                            <Label>{{ t('app.appSettings.requestCategories.endTime') }}</Label>
                            <Input v-model="subForm.end" type="time" />
                        </div>
                    </div>
                    <Button size="sm" :disabled="subForm.processing">{{ t('app.appSettings.requestCategories.addSubcategorySubmit') }}</Button>
                </form>

                <!-- Subcategories list -->
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>{{ t('app.appSettings.requestCategories.nameEn') }}</TableHead>
                            <TableHead>{{ t('app.appSettings.requestCategories.nameAr') }}</TableHead>
                            <TableHead>{{ t('app.appSettings.requestCategories.status') }}</TableHead>
                            <TableHead class="text-right">{{ t('app.appSettings.common.actions') }}</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="sub in category.subcategories" :key="sub.id">
                            <TableCell>{{ sub.name_en ?? sub.name }}</TableCell>
                            <TableCell>{{ sub.name_ar ?? '—' }}</TableCell>
                            <TableCell><Badge :variant="(sub as any).status !== false ? 'default' : 'secondary'">{{ (sub as any).status !== false ? t('app.appSettings.common.active') : t('app.appSettings.common.inactive') }}</Badge></TableCell>
                            <TableCell class="text-right">
                                <Button variant="destructive" size="sm" @click="deleteSubcategory(sub.id)">{{ t('app.appSettings.common.delete') }}</Button>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="!category.subcategories?.length">
                            <TableCell :colspan="4" class="text-muted-foreground text-center">{{ t('app.appSettings.requestCategories.noSubcategories') }}</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    </div>
</template>
