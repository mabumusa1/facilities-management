<script setup lang="ts">
import { Form, Head, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/set-password';

const { t } = useI18n();

const props = defineProps<{
    token: string;
    email: string;
    tokenValid: boolean;
}>();

watchEffect(() => {
    setLayoutProps({
        title: t('app.auth.setPassword.layoutTitle'),
        description: t('app.auth.setPassword.layoutDescription'),
    });
});
</script>

<template>
    <Head :title="t('app.auth.setPassword.headTitle')" />

    <div v-if="!tokenValid" class="rounded-lg border border-amber-200 bg-amber-50 p-4" role="alert">
        <p class="text-amber-800 text-sm">
            {{ t('app.auth.setPassword.expiredToken') }}
        </p>
    </div>

    <Form
        v-else
        v-bind="store.form()"
        :transform="(data) => ({ ...data, token })"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
    >
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="password">{{ t('app.auth.setPassword.password') }}</Label>
                <PasswordInput
                    id="password"
                    name="password"
                    autocomplete="new-password"
                    class="mt-1 block w-full"
                    autofocus
                    :placeholder="t('app.auth.setPassword.password')"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation">
                    {{ t('app.auth.setPassword.confirmPassword') }}
                </Label>
                <PasswordInput
                    id="password_confirmation"
                    name="password_confirmation"
                    autocomplete="new-password"
                    class="mt-1 block w-full"
                    :placeholder="t('app.auth.setPassword.confirmPassword')"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                class="mt-4 w-full"
                :disabled="processing"
            >
                <Spinner v-if="processing" />
                {{ t('app.auth.setPassword.submit') }}
            </Button>
        </div>
    </Form>
</template>
