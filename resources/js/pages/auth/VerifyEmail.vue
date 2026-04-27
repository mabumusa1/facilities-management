<script setup lang="ts">
import { Form, Head, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        title: t('app.auth.verifyEmail.layoutTitle'),
        description: t('app.auth.verifyEmail.layoutDescription'),
    });
});

defineProps<{
    status?: string;
    email?: string;
}>();
</script>

<template>
    <Head :title="t('app.auth.verifyEmail.headTitle')" />

    <div
        v-if="status === 'verification-link-sent'"
        class="mb-4 text-center text-sm font-medium text-green-600"
    >
        {{ t('app.auth.verifyEmail.success') }}
    </div>

    <p
        v-if="email"
        class="mb-4 text-center text-sm text-muted-foreground"
    >
        {{ t('app.auth.verifyEmail.layoutDescription', { email }) }}
    </p>

    <p class="mb-6 text-center text-sm text-muted-foreground">
        {{ t('app.auth.verifyEmail.instructions') }}
    </p>

    <Form
        v-bind="send.form()"
        class="space-y-6 text-center"
        v-slot="{ processing }"
    >
        <Button :disabled="processing" variant="secondary">
            <Spinner v-if="processing" />
            {{ t('app.actions.resendVerificationEmail') }}
        </Button>

        <TextLink :href="logout()" as="button" class="mx-auto block text-sm">
            {{ t('app.actions.logout') }}
        </TextLink>
    </Form>
</template>
