<script setup lang="ts">
import { Head, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, ref, watch, watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Skeleton } from '@/components/ui/skeleton';

const { t } = useI18n();

const props = defineProps<{
    companyProfile: {
        name_en?: string | null;
        name_ar?: string | null;
        vat_number?: string | null;
        cr_number?: string | null;
        logo_url?: string | null;
        logo_ar_url?: string | null;
        timezone?: string | null;
        primary_color?: string | null;
    } | null;
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.appSettings'), href: '/app-settings/general' },
            { title: t('app.appSettings.companyProfile.pageTitle'), href: '/app-settings/company-profile' },
        ],
    });
});

const form = useForm({
    name_en: '',
    name_ar: '',
    vat_number: '',
    cr_number: '',
    timezone: 'UTC',
    primary_color: '#1A73E8',
    remove_logo: false,
    remove_logo_ar: false,
});

function resetState() {
    form.defaults({
        name_en: '',
        name_ar: '',
        vat_number: '',
        cr_number: '',
        timezone: 'UTC',
        primary_color: '#1A73E8',
        remove_logo: false,
        remove_logo_ar: false,
    });
    form.reset();
}

const isDirty = computed(() => form.isDirty);

// File upload state
const logoInput = ref<HTMLInputElement | null>(null);
const logoArInput = ref<HTMLInputElement | null>(null);
const logoFile = ref<File | null>(null);
const logoPreview = ref<string | null>(null);
const logoArFile = ref<File | null>(null);
const logoArPreview = ref<string | null>(null);
const logoError = ref<string | null>(null);
const logoArError = ref<string | null>(null);

const allowedTypes = ['image/png', 'image/svg+xml'];
const maxSize = 2 * 1024 * 1024;

function handleLogoSelect(_event: Event, isArabic = false) {
    const input = isArabic ? logoArInput.value : logoInput.value;
    if (!input) return;

    const file = input.files?.[0];
    if (!file) return;

    if (!allowedTypes.includes(file.type)) {
        if (isArabic) {
            logoArError.value = t('app.appSettings.companyProfile.validationLogoType');
        } else {
            logoError.value = t('app.appSettings.companyProfile.validationLogoType');
        }
        input.value = '';
        return;
    }

    if (file.size > maxSize) {
        if (isArabic) {
            logoArError.value = t('app.appSettings.companyProfile.validationLogoSize');
        } else {
            logoError.value = t('app.appSettings.companyProfile.validationLogoSize');
        }
        input.value = '';
        return;
    }

    if (isArabic) {
        logoArError.value = null;
        logoArFile.value = file;
        logoArPreview.value = URL.createObjectURL(file);
    } else {
        logoError.value = null;
        logoFile.value = file;
        logoPreview.value = URL.createObjectURL(file);
    }
}

function removeLogo(isArabic = false) {
    if (isArabic) {
        logoArFile.value = null;
        logoArPreview.value = null;
        logoArError.value = null;
        form.remove_logo_ar = true;
    } else {
        logoFile.value = null;
        logoPreview.value = null;
        logoError.value = null;
        form.remove_logo = true;
    }
}

function submit() {
    form.clearErrors();
    logoError.value = null;
    logoArError.value = null;

    form.transform((data) => {
        const fd = new FormData();
        fd.append('_method', 'PUT');
        fd.append('name_en', data.name_en);
        fd.append('name_ar', data.name_ar);
        fd.append('vat_number', data.vat_number || '');
        fd.append('cr_number', data.cr_number || '');
        fd.append('timezone', data.timezone || 'UTC');
        fd.append('primary_color', data.primary_color || '#1A73E8');

        if (logoFile.value) {
            fd.append('logo', logoFile.value);
        }
        if (logoArFile.value) {
            fd.append('logo_ar', logoArFile.value);
        }
        if (data.remove_logo) {
            fd.append('remove_logo', '1');
        }
        if (data.remove_logo_ar) {
            fd.append('remove_logo_ar', '1');
        }

        return fd;
    });

    form.post('/app-settings/company-profile', {
        onSuccess: () => {
            form.remove_logo = false;
            form.remove_logo_ar = false;
            logoFile.value = null;
            logoPreview.value = null;
            logoArFile.value = null;
            logoArPreview.value = null;
        },
    });
}

function discard() {
    resetState();
    logoFile.value = null;
    logoPreview.value = null;
    logoArFile.value = null;
    logoArPreview.value = null;
    logoError.value = null;
    logoArError.value = null;
    form.remove_logo = false;
    form.remove_logo_ar = false;
    form.clearErrors();
}

// Watch the deferred prop to populate the form when data arrives
watch(
    () => props.companyProfile,
    (profile) => {
        if (!profile) {
            return;
        }
        form.defaults({
            name_en: profile.name_en ?? '',
            name_ar: profile.name_ar ?? '',
            vat_number: profile.vat_number ?? '',
            cr_number: profile.cr_number ?? '',
            timezone: profile.timezone ?? 'UTC',
            primary_color: profile.primary_color ?? '#1A73E8',
            remove_logo: false,
            remove_logo_ar: false,
        });
        form.reset();
    },
    { immediate: true },
);

const timezones = [
    { group: 'gulf', value: 'Asia/Riyadh', labelEn: 'Asia/Riyadh (GMT+3)', labelAr: 'الرياض (GMT+3)' },
    { group: 'gulf', value: 'Asia/Dubai', labelEn: 'Asia/Dubai (GMT+4)', labelAr: 'دبي (GMT+4)' },
    { group: 'gulf', value: 'Asia/Kuwait', labelEn: 'Asia/Kuwait (GMT+3)', labelAr: 'الكويت (GMT+3)' },
    { group: 'gulf', value: 'Asia/Qatar', labelEn: 'Asia/Qatar (GMT+3)', labelAr: 'قطر (GMT+3)' },
    { group: 'gulf', value: 'Asia/Bahrain', labelEn: 'Asia/Bahrain (GMT+3)', labelAr: 'البحرين (GMT+3)' },
    { group: 'gulf', value: 'Asia/Muscat', labelEn: 'Asia/Muscat (GMT+4)', labelAr: 'مسقط (GMT+4)' },
    { group: 'all', value: 'UTC', labelEn: 'UTC (GMT+0)', labelAr: 'UTC (GMT+0)' },
    { group: 'all', value: 'Africa/Cairo', labelEn: 'Africa/Cairo (GMT+2)', labelAr: 'القاهرة (GMT+2)' },
    { group: 'all', value: 'America/New_York', labelEn: 'America/New_York (GMT-4)', labelAr: 'نيويورك (GMT-4)' },
    { group: 'all', value: 'Asia/Karachi', labelEn: 'Asia/Karachi (GMT+5)', labelAr: 'كراتشي (GMT+5)' },
    { group: 'all', value: 'Europe/London', labelEn: 'Europe/London (GMT+0)', labelAr: 'لندن (GMT+0)' },
];

const gulfTimezones = computed(() => timezones.filter(tz => tz.group === 'gulf'));
const allTimezones = computed(() => timezones.filter(tz => tz.group === 'all'));

const serverError = ref<string | null>(null);
</script>

<template>
    <Head :title="t('app.appSettings.companyProfile.pageTitle')" />

    <div class="flex flex-col gap-6 p-4 pb-24">
        <!-- Server error banner -->
        <div
            v-if="serverError"
            role="alert"
            aria-live="assertive"
            class="rounded-md border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-950"
        >
            <p class="text-sm font-medium text-amber-900 dark:text-amber-100">
                {{ t('app.appSettings.companyProfile.toastError') }}
            </p>
            <p class="text-sm text-amber-700 dark:text-amber-300">
                {{ t('app.appSettings.companyProfile.retryMessage') }}
            </p>
        </div>

        <Heading
            variant="small"
            :title="t('app.appSettings.companyProfile.heading')"
            :description="t('app.appSettings.companyProfile.description')"
        />

        <!-- Page content with deferred loading -->
        <template v-if="!companyProfile">
            <!-- Skeleton loading state -->
            <Card v-for="i in 4" :key="i">
                <CardHeader>
                    <Skeleton class="h-4 w-32" />
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <Skeleton class="h-3 w-24" />
                        <Skeleton class="h-10 w-full max-w-md" />
                        <Skeleton class="h-3 w-24" />
                        <Skeleton class="h-10 w-full max-w-md" />
                    </div>
                </CardContent>
            </Card>
        </template>

        <template v-else>
            <!-- Section 1: Identity -->
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('app.appSettings.companyProfile.sectionIdentity') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-2">
                            <Label for="name_en">{{ t('app.appSettings.companyProfile.nameEn') }} *</Label>
                            <Input
                                id="name_en"
                                v-model="form.name_en"
                                required
                                :placeholder="t('app.appSettings.companyProfile.nameEnPlaceholder')"
                                dir="ltr"
                            />
                            <InputError :message="form.errors.name_en" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="name_ar">{{ t('app.appSettings.companyProfile.nameAr') }} *</Label>
                            <Input
                                id="name_ar"
                                v-model="form.name_ar"
                                required
                                :placeholder="t('app.appSettings.companyProfile.nameArPlaceholder')"
                                dir="rtl"
                            />
                            <InputError :message="form.errors.name_ar" />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="vat_number">{{ t('app.appSettings.companyProfile.vatNumber') }}</Label>
                                <Input
                                    id="vat_number"
                                    v-model="form.vat_number"
                                    :placeholder="t('app.appSettings.companyProfile.vatNumberPlaceholder')"
                                    dir="ltr"
                                />
                                <p class="text-xs text-muted-foreground">
                                    {{ t('app.appSettings.companyProfile.vatHelper') }}
                                </p>
                                <InputError :message="form.errors.vat_number" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="cr_number">{{ t('app.appSettings.companyProfile.crNumber') }}</Label>
                                <Input
                                    id="cr_number"
                                    v-model="form.cr_number"
                                    :placeholder="t('app.appSettings.companyProfile.crNumberPlaceholder')"
                                    dir="ltr"
                                />
                                <InputError :message="form.errors.cr_number" />
                            </div>
                        </div>
                    </form>
                </CardContent>
            </Card>

            <!-- Section 2: Logo & Brand -->
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('app.appSettings.companyProfile.sectionLogos') }}</CardTitle>
                </CardHeader>
                <CardContent class="space-y-6">
                    <!-- Primary logo -->
                    <div>
                        <Label>{{ t('app.appSettings.companyProfile.primaryLogo') }}</Label>
                        <p class="text-xs text-muted-foreground mb-3">
                            {{ t('app.appSettings.companyProfile.logoHint') }}
                        </p>

                        <div class="flex flex-wrap items-start gap-4">
                            <!-- Logo thumbnail -->
                            <div class="flex h-20 w-32 items-center justify-center rounded-md border bg-muted">
                                <img
                                    v-if="logoPreview || (!form.remove_logo && companyProfile?.logo_url)"
                                    :src="(logoPreview || companyProfile?.logo_url) ?? ''"
                                    alt="Company logo"
                                    class="max-h-full max-w-full object-contain p-1"
                                />
                                <template v-else>
                                    <span class="sr-only">{{ t('app.appSettings.companyProfile.noLogo') }}</span>
                                    <svg class="h-8 w-8 text-muted-foreground/40" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z" />
                                    </svg>
                                </template>
                            </div>

                            <div class="flex flex-col gap-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <input
                                        ref="logoInput"
                                        type="file"
                                        accept="image/png,image/svg+xml"
                                        class="hidden"
                                        @change="handleLogoSelect($event, false)"
                                    />
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        @click="logoInput?.click()"
                                    >
                                        {{ (logoPreview || companyProfile?.logo_url) ? t('app.appSettings.companyProfile.changeLogo') : t('app.appSettings.companyProfile.uploadLogo') }}
                                    </Button>
                                    <Button
                                        v-if="logoPreview || companyProfile?.logo_url"
                                        type="button"
                                        variant="ghost"
                                        size="sm"
                                        class="text-destructive"
                                        @click="removeLogo(false)"
                                    >
                                        {{ t('app.appSettings.companyProfile.removeLogo') }}
                                    </Button>
                                </div>
                                <p class="text-xs text-muted-foreground">
                                    {{ t('app.appSettings.companyProfile.uploadHelper') }}
                                </p>
                                <InputError v-if="logoError" :message="logoError" />
                            </div>
                        </div>
                    </div>

                    <!-- Arabic logo variant -->
                    <div>
                        <Label>{{ t('app.appSettings.companyProfile.logoArVariant') }}</Label>
                        <p class="text-xs text-muted-foreground mb-3">
                            {{ t('app.appSettings.companyProfile.logoArHint') }}
                        </p>

                        <div class="flex flex-wrap items-start gap-4">
                            <div class="flex h-20 w-32 items-center justify-center rounded-md border bg-muted">
                                <img
                                    v-if="logoArPreview || (!form.remove_logo_ar && companyProfile?.logo_ar_url)"
                                    :src="(logoArPreview || companyProfile?.logo_ar_url) ?? ''"
                                    alt="Arabic company logo"
                                    class="max-h-full max-w-full object-contain p-1"
                                />
                                <template v-else>
                                    <span class="sr-only">{{ t('app.appSettings.companyProfile.noLogo') }}</span>
                                    <svg class="h-8 w-8 text-muted-foreground/40" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z" />
                                    </svg>
                                </template>
                            </div>

                            <div class="flex flex-col gap-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <input
                                        ref="logoArInput"
                                        type="file"
                                        accept="image/png,image/svg+xml"
                                        class="hidden"
                                        @change="handleLogoSelect($event, true)"
                                    />
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        @click="logoArInput?.click()"
                                    >
                                        {{ (logoArPreview || companyProfile?.logo_ar_url) ? t('app.appSettings.companyProfile.changeLogo') : t('app.appSettings.companyProfile.uploadLogo') }}
                                    </Button>
                                    <Button
                                        v-if="logoArPreview || companyProfile?.logo_ar_url"
                                        type="button"
                                        variant="ghost"
                                        size="sm"
                                        class="text-destructive"
                                        @click="removeLogo(true)"
                                    >
                                        {{ t('app.appSettings.companyProfile.removeLogo') }}
                                    </Button>
                                </div>
                                <p class="text-xs text-muted-foreground">
                                    {{ t('app.appSettings.companyProfile.uploadHelper') }}
                                </p>
                                <InputError v-if="logoArError" :message="logoArError" />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Section 3: Regional -->
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('app.appSettings.companyProfile.sectionRegional') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-2 max-w-md">
                        <Label for="timezone">{{ t('app.appSettings.companyProfile.timezone') }}</Label>
                        <Select v-model="form.timezone">
                            <SelectTrigger id="timezone" class="w-full">
                                <SelectValue :placeholder="t('app.appSettings.companyProfile.timezoneSearch')" />
                            </SelectTrigger>
                            <SelectContent>
                                <!-- Gulf group -->
                                <div
                                    role="group"
                                    :aria-label="t('app.appSettings.companyProfile.timezoneGroupGulf')"
                                    class="px-2 py-1.5 text-xs font-semibold text-muted-foreground"
                                >
                                    {{ t('app.appSettings.companyProfile.timezoneGroupGulf') }}
                                </div>
                                <SelectItem
                                    v-for="tz in gulfTimezones"
                                    :key="tz.value"
                                    :value="tz.value"
                                >
                                    <span dir="ltr">{{ tz.value }}</span>
                                    <span class="ms-2 text-muted-foreground">(GMT{{ tz.labelEn.includes('GMT-') ? tz.labelEn.split('GMT')[1].split(')')[0] : '+' + tz.labelEn.split('GMT')[1].split(')')[0] }})</span>
                                </SelectItem>

                                <!-- All timezones group -->
                                <div
                                    v-if="allTimezones.length > 0"
                                    role="group"
                                    aria-label="All timezones"
                                    class="border-t px-2 pt-2 pb-1.5 text-xs font-semibold text-muted-foreground"
                                >
                                    All timezones
                                </div>
                                <SelectItem
                                    v-for="tz in allTimezones"
                                    :key="tz.value"
                                    :value="tz.value"
                                >
                                    {{ tz.labelEn }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p class="text-xs text-muted-foreground">
                            {{ t('app.appSettings.companyProfile.timezoneHelper') }}
                        </p>
                        <InputError :message="form.errors.timezone" />
                    </div>
                </CardContent>
            </Card>

            <!-- Section 4: Brand Colors -->
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('app.appSettings.companyProfile.sectionBrandColors') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-2 max-w-xs">
                        <Label for="primary_color">{{ t('app.appSettings.companyProfile.primaryColor') }}</Label>
                        <div class="flex items-center gap-3">
                            <div
                                class="h-10 w-10 shrink-0 rounded-md border"
                                :style="{ backgroundColor: form.primary_color || '#1A73E8' }"
                                aria-hidden="true"
                            />
                            <Input
                                id="primary_color"
                                v-model="form.primary_color"
                                :placeholder="t('app.appSettings.companyProfile.hexLabel')"
                                class="font-mono"
                                dir="ltr"
                                maxlength="7"
                            />
                        </div>
                        <p class="text-xs text-muted-foreground">
                            {{ t('app.appSettings.companyProfile.colorHint') }}
                        </p>
                        <InputError :message="form.errors.primary_color" />
                    </div>
                </CardContent>
            </Card>
        </template>
    </div>

    <!-- Sticky dirty bar -->
    <div
        v-if="isDirty && companyProfile"
        role="region"
        :aria-label="t('app.appSettings.companyProfile.unsavedChanges')"
        class="fixed inset-x-0 bottom-0 z-50 border-t bg-background px-4 py-3 shadow-lg"
    >
        <div class="mx-auto flex max-w-4xl items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="inline-block h-2 w-2 rounded-full bg-amber-500" aria-hidden="true" />
                <span class="text-sm font-medium">{{ t('app.appSettings.companyProfile.unsavedChanges') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    :disabled="form.processing"
                    @click="discard"
                >
                    {{ t('app.appSettings.companyProfile.discard') }}
                </Button>
                <Button
                    type="button"
                    size="sm"
                    :disabled="form.processing"
                    @click="submit"
                >
                    <span v-if="form.processing" class="flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        {{ t('app.appSettings.companyProfile.saving') }}
                    </span>
                    <span v-else>{{ t('app.appSettings.companyProfile.saveChanges') }}</span>
                </Button>
            </div>
        </div>
    </div>
</template>
