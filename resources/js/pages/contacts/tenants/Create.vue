<script setup lang="ts">
import { Head, setLayoutProps, useForm } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { useI18n } from '@/composables/useI18n';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.contacts.tenants.pageTitle'), href: '/residents' },
            { title: t('app.contacts.tenants.newTenant'), href: '/residents/create' },
        ],
    });
});

const form = useForm({ first_name: '', last_name: '', email: '', phone_number: '', phone_country_code: 'SA', national_id: '', gender: '', georgian_birthdate: '' });

function submit() { form.post('/residents'); }
</script>

<template>
    <Head :title="t('app.contacts.tenants.newTenant')" />
    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="t('app.contacts.tenants.createTitle')" :description="t('app.contacts.tenants.createDescription')" />
        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2"><Label for="first_name">{{ t('app.contacts.shared.firstName') }}</Label><Input id="first_name" v-model="form.first_name" required :placeholder="t('app.contacts.shared.firstName')" /><InputError :message="form.errors.first_name" /></div>
                <div class="grid gap-2"><Label for="last_name">{{ t('app.contacts.shared.lastName') }}</Label><Input id="last_name" v-model="form.last_name" required :placeholder="t('app.contacts.shared.lastName')" /><InputError :message="form.errors.last_name" /></div>
            </div>
            <div class="grid gap-2"><Label for="email">{{ t('app.contacts.shared.email') }}</Label><Input id="email" v-model="form.email" type="email" :placeholder="t('app.auth.login.emailPlaceholder')" /><InputError :message="form.errors.email" /></div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2"><Label for="phone_country_code">{{ t('app.contacts.shared.countryCode') }}</Label><Input id="phone_country_code" v-model="form.phone_country_code" required maxlength="5" /><InputError :message="form.errors.phone_country_code" /></div>
                <div class="grid gap-2"><Label for="phone_number">{{ t('app.contacts.shared.phoneNumber') }}</Label><Input id="phone_number" v-model="form.phone_number" required :placeholder="t('app.contacts.shared.phoneNumberPlaceholder')" /><InputError :message="form.errors.phone_number" /></div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2"><Label for="national_id">{{ t('app.contacts.shared.nationalId') }}</Label><Input id="national_id" v-model="form.national_id" /><InputError :message="form.errors.national_id" /></div>
                <div class="grid gap-2">
                    <Label for="gender">{{ t('app.contacts.shared.gender') }}</Label>
                    <select id="gender" v-model="form.gender" class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"><option value="">{{ t('app.contacts.shared.select') }}</option><option value="male">{{ t('app.contacts.shared.male') }}</option><option value="female">{{ t('app.contacts.shared.female') }}</option></select>
                    <InputError :message="form.errors.gender" />
                </div>
            </div>
            <div class="grid gap-2"><Label for="georgian_birthdate">{{ t('app.contacts.shared.dateOfBirth') }}</Label><Input id="georgian_birthdate" v-model="form.georgian_birthdate" type="date" /><InputError :message="form.errors.georgian_birthdate" /></div>
            <div class="flex items-center gap-4"><Button :disabled="form.processing">{{ t('app.contacts.tenants.createButton') }}</Button></div>
        </form>
    </div>
</template>
