<script setup lang="ts">
import { Head, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { useI18n } from '@/composables/useI18n';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Professional } from '@/types';

const props = defineProps<{ professional: Professional }>();
const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.contacts.professionals.pageTitle'), href: '/professionals' },
            { title: t('app.common.edit'), href: '#' },
        ],
    });
});

const pageTitle = computed(() => t('app.contacts.professionals.editTitle', { name: `${props.professional.first_name} ${props.professional.last_name}` }));

const form = useForm({ first_name: props.professional.first_name, last_name: props.professional.last_name, email: props.professional.email ?? '', phone_number: props.professional.phone_number, phone_country_code: props.professional.phone_country_code, national_id: props.professional.national_id ?? '' });

function submit() { form.put(`/professionals/${props.professional.id}`); }
</script>

<template>
    <Head :title="pageTitle" />
    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="pageTitle" :description="t('app.contacts.professionals.editDescription')" />
        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2"><Label for="first_name">{{ t('app.contacts.shared.firstName') }}</Label><Input id="first_name" v-model="form.first_name" required /><InputError :message="form.errors.first_name" /></div>
                <div class="grid gap-2"><Label for="last_name">{{ t('app.contacts.shared.lastName') }}</Label><Input id="last_name" v-model="form.last_name" required /><InputError :message="form.errors.last_name" /></div>
            </div>
            <div class="grid gap-2"><Label for="email">{{ t('app.contacts.shared.email') }}</Label><Input id="email" v-model="form.email" type="email" /><InputError :message="form.errors.email" /></div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2"><Label for="phone_country_code">{{ t('app.contacts.shared.countryCode') }}</Label><Input id="phone_country_code" v-model="form.phone_country_code" required maxlength="5" /><InputError :message="form.errors.phone_country_code" /></div>
                <div class="grid gap-2"><Label for="phone_number">{{ t('app.contacts.shared.phoneNumber') }}</Label><Input id="phone_number" v-model="form.phone_number" required /><InputError :message="form.errors.phone_number" /></div>
            </div>
            <div class="grid gap-2"><Label for="national_id">{{ t('app.contacts.shared.nationalId') }}</Label><Input id="national_id" v-model="form.national_id" /><InputError :message="form.errors.national_id" /></div>
            <div class="flex items-center gap-4"><Button :disabled="form.processing">{{ t('app.contacts.professionals.updateButton') }}</Button></div>
        </form>
    </div>
</template>
