<script setup lang="ts">
import { watch } from 'vue';
import { Head, Link, setLayoutProps, useForm } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

const { t } = useI18n();

watch(
    () => t('app.visitorAccess.register.pageTitle'),
    () => {
        setLayoutProps({
            breadcrumbs: [
                { title: t('app.navigation.dashboard'), href: '/dashboard' },
                { title: t('app.visitorAccess.myVisitors.pageTitle'), href: '/visitor-access/invitations' },
                { title: t('app.visitorAccess.register.pageTitle'), href: '/visitor-access/invitations/create' },
            ],
        });
    },
    { immediate: true },
);

const form = useForm({
    visitor_name: '',
    visitor_purpose: 'visit',
    expected_at: '',
    visitor_phone: '',
});

function submit() {
    form.post('/visitor-access/invitations');
}
</script>

<template>
    <Head :title="t('app.visitorAccess.register.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            variant="small"
            :title="t('app.visitorAccess.register.heading')"
            :description="t('app.visitorAccess.register.description')"
        />

        <form class="max-w-2xl space-y-6" @submit.prevent="submit">
            <!-- Visitor Name -->
            <div class="grid gap-2">
                <Label for="visitor_name">
                    {{ t('app.visitorAccess.register.visitorNameLabel') }}
                    <span aria-hidden="true">*</span>
                </Label>
                <Input
                    id="visitor_name"
                    v-model="form.visitor_name"
                    dir="rtl"
                    lang="ar"
                    required
                    autofocus
                    :placeholder="t('app.visitorAccess.register.visitorNamePlaceholder')"
                    :disabled="form.processing"
                    :aria-invalid="!!form.errors.visitor_name"
                />
                <InputError :message="form.errors.visitor_name" />
            </div>

            <!-- Purpose -->
            <div class="grid gap-2">
                <Label for="visitor_purpose">
                    {{ t('app.visitorAccess.register.purposeLabel') }}
                    <span aria-hidden="true">*</span>
                </Label>
                <Select v-model="form.visitor_purpose" :disabled="form.processing">
                    <SelectTrigger id="visitor_purpose" class="w-full">
                        <SelectValue :placeholder="t('app.visitorAccess.register.purposeLabel')" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="visit">{{ t('app.visitorAccess.register.purposeVisit') }}</SelectItem>
                        <SelectItem value="delivery">{{ t('app.visitorAccess.register.purposeDelivery') }}</SelectItem>
                        <SelectItem value="service">{{ t('app.visitorAccess.register.purposeService') }}</SelectItem>
                        <SelectItem value="other">{{ t('app.visitorAccess.register.purposeOther') }}</SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="form.errors.visitor_purpose" />
            </div>

            <!-- Expected arrival (datetime-local) -->
            <div class="grid gap-2">
                <Label for="expected_at">
                    {{ t('app.visitorAccess.register.expectedArrivalLabel') }}
                    <span aria-hidden="true">*</span>
                </Label>
                <Input
                    id="expected_at"
                    v-model="form.expected_at"
                    type="datetime-local"
                    required
                    :disabled="form.processing"
                    :aria-label="t('app.visitorAccess.register.expectedArrivalLabel')"
                    :aria-invalid="!!form.errors.expected_at"
                />
                <InputError :message="form.errors.expected_at" />
            </div>

            <!-- Visitor phone (optional) -->
            <div class="grid gap-2">
                <Label for="visitor_phone">{{ t('app.visitorAccess.register.phoneLabel') }}</Label>
                <Input
                    id="visitor_phone"
                    v-model="form.visitor_phone"
                    type="tel"
                    dir="ltr"
                    :disabled="form.processing"
                    :aria-invalid="!!form.errors.visitor_phone"
                />
                <InputError :message="form.errors.visitor_phone" />
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4">
                <Button variant="outline" as-child :disabled="form.processing">
                    <Link href="/visitor-access/invitations">{{ t('app.actions.cancel') }}</Link>
                </Button>
                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? t('app.visitorAccess.register.generatingCta') : t('app.visitorAccess.register.generateCta') }}
                </Button>
            </div>
        </form>
    </div>
</template>
