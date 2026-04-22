<script setup lang="ts">
import { Form, Head, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        title: t('app.auth.register.layoutTitle'),
        description: t('app.auth.register.layoutDescription'),
    });
});
</script>

<template>
    <Head :title="t('app.auth.register.headTitle')" />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-6">
            <div>
                <h2 class="text-sm font-semibold text-muted-foreground">{{ t('app.auth.register.personalInformation') }}</h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="first_name">{{ t('app.auth.register.firstName') }}</Label>
                    <Input
                        id="first_name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="given-name"
                        name="first_name"
                        :placeholder="t('app.auth.register.firstNamePlaceholder')"
                    />
                    <InputError :message="errors.first_name" />
                </div>

                <div class="grid gap-2">
                    <Label for="last_name">{{ t('app.auth.register.lastName') }}</Label>
                    <Input
                        id="last_name"
                        type="text"
                        required
                        :tabindex="2"
                        autocomplete="family-name"
                        name="last_name"
                        :placeholder="t('app.auth.register.lastNamePlaceholder')"
                    />
                    <InputError :message="errors.last_name" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="email">{{ t('app.auth.register.emailAddress') }}</Label>
                <Input
                    id="email"
                    type="email"
                    required
                    :tabindex="3"
                    autocomplete="email"
                    name="email"
                    :placeholder="t('app.auth.register.emailPlaceholder')"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <Label for="phone_number">{{ t('app.auth.register.phoneNumber') }}</Label>
                <Input
                    id="phone_number"
                    type="tel"
                    required
                    :tabindex="4"
                    autocomplete="tel"
                    name="phone_number"
                    :placeholder="t('app.auth.register.phoneNumberPlaceholder')"
                />
                <InputError :message="errors.phone_number" />
            </div>

            <div>
                <h2 class="text-sm font-semibold text-muted-foreground">{{ t('app.auth.register.tenantInformation') }}</h2>
            </div>

            <div class="grid gap-2">
                <Label for="tenant_name">{{ t('app.auth.register.accountName') }}</Label>
                <Input
                    id="tenant_name"
                    type="text"
                    required
                    :tabindex="5"
                    autocomplete="organization"
                    name="tenant_name"
                    :placeholder="t('app.auth.register.accountNamePlaceholder')"
                />
                <InputError :message="errors.tenant_name" />
            </div>

            <div class="grid gap-2">
                <Label for="password">{{ t('app.auth.register.password') }}</Label>
                <PasswordInput
                    id="password"
                    required
                    :tabindex="6"
                    autocomplete="new-password"
                    name="password"
                    :placeholder="t('app.auth.register.password')"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation">{{ t('app.auth.register.confirmPassword') }}</Label>
                <PasswordInput
                    id="password_confirmation"
                    required
                    :tabindex="7"
                    autocomplete="new-password"
                    name="password_confirmation"
                    :placeholder="t('app.auth.register.confirmPassword')"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <div class="grid gap-2">
                <label for="terms" class="flex items-start gap-3 text-sm text-muted-foreground">
                    <input
                        id="terms"
                        type="checkbox"
                        name="terms"
                        value="1"
                        required
                        :tabindex="8"
                        class="mt-1 size-4 rounded border-input bg-background text-primary"
                    >
                    <span>{{ t('app.auth.register.acceptTerms') }}</span>
                </label>
                <InputError :message="errors.terms" />
            </div>

            <Button
                type="submit"
                class="mt-2 w-full"
                tabindex="9"
                :disabled="processing"
                data-test="register-user-button"
            >
                <Spinner v-if="processing" />
                {{ t('app.auth.register.submit') }}
            </Button>
        </div>

        <div class="text-center text-sm text-muted-foreground">
            {{ t('app.auth.register.hasAccount') }}
            <TextLink
                :href="login()"
                class="underline underline-offset-4"
                :tabindex="10"
                >{{ t('app.auth.register.logIn') }}</TextLink
            >
        </div>
    </Form>
</template>
