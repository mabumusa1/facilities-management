<script setup lang="ts">
import { Head, Link, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, ref, watchEffect } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useI18n } from '@/composables/useI18n';
import { duplicateCheck as duplicateCheckRoute } from '@/routes/residents';

type IdTypeOption = { value: string; label: string };
type DuplicateMatch = {
    id: number;
    name: string;
    unit: string | null;
    building: string | null;
    community: string | null;
};

const props = defineProps<{
    countries?: Array<{ id: number; name: string; name_en: string | null }>;
    idTypes?: IdTypeOption[];
}>();

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.contacts.residents.pageTitle'), href: '/residents' },
            { title: t('app.contacts.residents.newResident'), href: '/residents/create' },
        ],
    });
});

const form = useForm({
    first_name: '',
    last_name: '',
    first_name_ar: '',
    last_name_ar: '',
    email: '',
    phone_country_code: 'SA',
    phone_number: '',
    national_id: '',
    id_type: '',
    nationality_id: '' as string | number,
    gender: '',
    georgian_birthdate: '',
    force_create: false,
});

const duplicateMatch = ref<DuplicateMatch | null>(null);
const duplicateChecking = ref(false);

async function checkPhoneDuplicate(): Promise<void> {
    if (!form.phone_number || !form.phone_country_code) {
        duplicateMatch.value = null;
        return;
    }

    duplicateChecking.value = true;

    const url = duplicateCheckRoute({
        query: {
            phone_country_code: form.phone_country_code,
            phone_number: form.phone_number,
        },
    }).url;

    try {
        const response = await fetch(url, {
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            duplicateMatch.value = null;
            return;
        }

        const body = (await response.json()) as
            | { duplicate: false }
            | { duplicate: true; match: DuplicateMatch };

        if (body.duplicate) {
            duplicateMatch.value = body.match;
        } else {
            duplicateMatch.value = null;
            form.force_create = false;
        }
    } catch {
        duplicateMatch.value = null;
    } finally {
        duplicateChecking.value = false;
    }
}

const submitDisabled = computed<boolean>(() => {
    if (form.processing) {
        return true;
    }
    if (duplicateMatch.value && !form.force_create) {
        return true;
    }
    return false;
});

const matchedRecordContext = computed<string>(() => {
    if (!duplicateMatch.value) {
        return '';
    }
    const parts = [duplicateMatch.value.unit, duplicateMatch.value.building].filter(
        (part): part is string => Boolean(part),
    );
    return parts.join(', ');
});

function submit() {
    form.post('/residents', {
        onSuccess: () => {
            duplicateMatch.value = null;
        },
    });
}
</script>

<template>
    <Head :title="t('app.contacts.residents.newResident')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            variant="small"
            :title="t('app.contacts.residents.createTitle')"
            :description="t('app.contacts.residents.createDescription')"
        />

        <form class="max-w-2xl space-y-6" @submit.prevent="submit">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="first_name">
                        {{ t('app.contacts.residents.firstNameEn') }}
                    </Label>
                    <Input
                        id="first_name"
                        v-model="form.first_name"
                        dir="ltr"
                        :placeholder="t('app.contacts.shared.firstName')"
                    />
                    <InputError :message="form.errors.first_name" />
                </div>

                <div class="grid gap-2">
                    <Label for="last_name">
                        {{ t('app.contacts.residents.lastNameEn') }}
                    </Label>
                    <Input
                        id="last_name"
                        v-model="form.last_name"
                        dir="ltr"
                        :placeholder="t('app.contacts.shared.lastName')"
                    />
                    <InputError :message="form.errors.last_name" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="first_name_ar">
                        {{ t('app.contacts.residents.firstNameAr') }}
                    </Label>
                    <Input
                        id="first_name_ar"
                        v-model="form.first_name_ar"
                        dir="rtl"
                        placeholder="مثال: أحمد"
                    />
                    <InputError :message="form.errors.first_name_ar" />
                </div>

                <div class="grid gap-2">
                    <Label for="last_name_ar">
                        {{ t('app.contacts.residents.lastNameAr') }}
                    </Label>
                    <Input
                        id="last_name_ar"
                        v-model="form.last_name_ar"
                        dir="rtl"
                        placeholder="مثال: الراشد"
                    />
                    <InputError :message="form.errors.last_name_ar" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="email">{{ t('app.contacts.shared.email') }}</Label>
                <Input
                    id="email"
                    v-model="form.email"
                    type="email"
                    dir="ltr"
                    placeholder="example@email.com"
                />
                <InputError :message="form.errors.email" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="phone_country_code">
                        {{ t('app.contacts.shared.countryCode') }}
                    </Label>
                    <Input
                        id="phone_country_code"
                        v-model="form.phone_country_code"
                        dir="ltr"
                        :placeholder="t('app.contacts.shared.countryCodePlaceholder')"
                        maxlength="5"
                        @blur="checkPhoneDuplicate"
                    />
                    <InputError :message="form.errors.phone_country_code" />
                </div>

                <div class="grid gap-2">
                    <Label for="phone_number">
                        {{ t('app.contacts.shared.phoneNumber') }}
                    </Label>
                    <Input
                        id="phone_number"
                        v-model="form.phone_number"
                        dir="ltr"
                        :placeholder="t('app.contacts.shared.phoneNumberPlaceholder')"
                        :class="duplicateMatch ? 'border-amber-400 focus-visible:ring-amber-400' : ''"
                        @blur="checkPhoneDuplicate"
                    />
                    <InputError :message="form.errors.phone_number" />
                    <span v-if="duplicateChecking" class="text-muted-foreground text-xs">·</span>
                </div>
            </div>

            <div
                v-if="duplicateMatch"
                role="status"
                aria-live="polite"
                class="rounded-md border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900"
            >
                <div class="flex items-start gap-2">
                    <span aria-hidden="true">⚠</span>
                    <div class="flex-1">
                        <p id="duplicate-banner-heading" class="font-medium">
                            {{ t('app.contacts.residents.duplicateHeading') }}
                        </p>
                        <p class="mt-1">
                            <strong>{{ duplicateMatch.name }}</strong>
                            <span v-if="matchedRecordContext"> · {{ matchedRecordContext }}</span>
                        </p>
                        <p class="mt-2">
                            <Link
                                :href="`/residents/${duplicateMatch.id}`"
                                class="font-medium underline underline-offset-2"
                            >
                                {{ t('app.contacts.residents.duplicateGoToExisting') }}
                            </Link>
                        </p>
                        <label class="mt-3 flex items-start gap-2">
                            <input
                                v-model="form.force_create"
                                type="checkbox"
                                class="mt-1"
                                aria-describedby="duplicate-banner-heading"
                            />
                            <span>{{ t('app.contacts.residents.duplicateConfirmCheckbox') }}</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="id_type">{{ t('app.contacts.residents.idType') }}</Label>
                    <select
                        id="id_type"
                        v-model="form.id_type"
                        class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                    >
                        <option value="">{{ t('app.contacts.residents.selectIdType') }}</option>
                        <option v-for="opt in props.idTypes ?? []" :key="opt.value" :value="opt.value">
                            {{ t(`app.contacts.residents.idTypes.${opt.value}`) }}
                        </option>
                    </select>
                    <InputError :message="form.errors.id_type" />
                </div>

                <div class="grid gap-2">
                    <Label for="national_id">{{ t('app.contacts.residents.idNumber') }}</Label>
                    <Input id="national_id" v-model="form.national_id" dir="ltr" />
                    <InputError :message="form.errors.national_id" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="gender">{{ t('app.contacts.shared.gender') }}</Label>
                    <select
                        id="gender"
                        v-model="form.gender"
                        class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                    >
                        <option value="">{{ t('app.contacts.shared.selectGender') }}</option>
                        <option value="male">{{ t('app.contacts.shared.male') }}</option>
                        <option value="female">{{ t('app.contacts.shared.female') }}</option>
                    </select>
                    <InputError :message="form.errors.gender" />
                </div>

                <div class="grid gap-2">
                    <Label for="georgian_birthdate">
                        {{ t('app.contacts.shared.dateOfBirth') }}
                    </Label>
                    <Input
                        id="georgian_birthdate"
                        v-model="form.georgian_birthdate"
                        type="date"
                    />
                    <InputError :message="form.errors.georgian_birthdate" />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="submitDisabled" :aria-disabled="submitDisabled">
                    {{ t('app.contacts.residents.createButton') }}
                </Button>
                <Link href="/residents" class="text-sm font-medium text-muted-foreground hover:underline">
                    {{ t('app.contacts.residents.cancel') }}
                </Link>
            </div>
        </form>
    </div>
</template>
