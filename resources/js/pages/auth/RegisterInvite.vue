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

defineProps<{
    code?: string;
}>();

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        title: t('app.auth.registerInvite.layoutTitle'),
        description: t('app.auth.registerInvite.layoutDescription'),
    });
});
</script>

<template>
    <Head :title="t('app.auth.registerInvite.layoutTitle')" />

    <Form
        v-bind="store.form()"
        :options="{
            onSuccess: () => {},
        }"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="code">{{ t('app.auth.registerInvite.inviteCode') }}</Label>
                <Input
                    id="code"
                    type="text"
                    dir="ltr"
                    autocomplete="off"
                    :value="code"
                    name="code"
                    :disabled="!!code"
                    :placeholder="t('app.auth.registerInvite.inviteCodePlaceholder')"
                />
                <InputError :message="errors.code" />
            </div>

            <div class="grid gap-2">
                <Label for="password">{{ t('app.auth.registerInvite.password') }}</Label>
                <PasswordInput
                    id="password"
                    required
                    autofocus
                    autocomplete="new-password"
                    name="password"
                    :placeholder="t('app.auth.registerInvite.password')"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation">{{ t('app.auth.registerInvite.confirmPassword') }}</Label>
                <PasswordInput
                    id="password_confirmation"
                    required
                    autocomplete="new-password"
                    name="password_confirmation"
                    :placeholder="t('app.auth.registerInvite.confirmPassword')"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                class="mt-2 w-full"
                :disabled="processing"
            >
                <Spinner v-if="processing" />
                {{ t('app.auth.registerInvite.submit') }}
            </Button>
        </div>

        <div class="text-center text-sm text-muted-foreground">
            {{ t('app.auth.registerInvite.hasAccount') }}
            <TextLink
                :href="login()"
                class="underline underline-offset-4"
            >
                {{ t('app.auth.registerInvite.logIn') }}
            </TextLink>
        </div>
    </Form>
</template>
