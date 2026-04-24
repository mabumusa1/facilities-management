<script setup lang="ts">
import { Form, Head, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        title: t('app.auth.login.layoutTitle'),
        description: t('app.auth.login.layoutDescription'),
    });
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <Head :title="t('app.auth.login.headTitle')" />

    <div
        v-if="status"
        class="mb-4 text-center text-sm font-medium text-green-600"
    >
        {{ status }}
    </div>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="email">{{ t('app.auth.login.emailAddress') }}</Label>
                <!-- Gap 4: dir="ltr" prevents RTL browsers from mirroring email addresses -->
                <Input
                    id="email"
                    type="email"
                    name="email"
                    dir="ltr"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="email"
                    :placeholder="t('app.auth.login.emailPlaceholder')"
                />
                <!--
                    Gap 7: Throttle detection via error message content.
                    Fortify returns the throttle message on the `email` key (same as invalid credentials).
                    We detect it by checking whether the error string contains 'seconds' (EN) or
                    'ثانية' (AR) — the seconds count is already embedded in the Fortify message,
                    so we display it verbatim rather than interpolating a separate variable.
                    NOTE: This approach is intentionally simple for this story. If Fortify's
                    throttle message wording changes, only this condition needs updating.
                -->
                <div
                    v-if="errors.email && (errors.email.includes('seconds') || errors.email.includes('ثانية'))"
                    class="rounded-md border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-700 dark:bg-amber-950 dark:text-amber-200"
                    role="alert"
                    aria-live="assertive"
                >
                    {{ errors.email }}
                </div>
                <InputError v-else :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <Label for="password">{{ t('app.auth.login.password') }}</Label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-sm"
                        :tabindex="5"
                    >
                        {{ t('app.auth.login.forgotPassword') }}
                    </TextLink>
                </div>
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    :placeholder="t('app.auth.login.password')"
                />
                <InputError :message="errors.password" />
            </div>

            <!-- Gap 8: gap-3 replaces space-x-3 (RTL-safe, consistent with rest of form) -->
            <div class="flex items-center justify-between">
                <Label for="remember" class="flex items-center gap-3">
                    <Checkbox id="remember" name="remember" :tabindex="3" />
                    <span>{{ t('app.auth.login.rememberMe') }}</span>
                </Label>
            </div>

            <Button
                type="submit"
                class="mt-4 w-full"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                {{ t('app.auth.login.submit') }}
            </Button>
        </div>

        <div
            class="text-center text-sm text-muted-foreground"
            v-if="canRegister"
        >
            {{ t('app.auth.login.noAccount') }}
            <TextLink :href="register()" :tabindex="5">{{ t('app.auth.login.signUp') }}</TextLink>
        </div>
    </Form>
</template>
