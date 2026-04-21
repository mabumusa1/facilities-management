<script setup lang="ts">
import { Form, Head, Link, setLayoutProps, usePage } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { useI18n } from '@/composables/useI18n';
import DeleteUser from '@/components/DeleteUser.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';

type Props = {
    mustVerifyEmail: boolean;
    status?: string;
};

defineProps<Props>();

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            {
                title: t('app.settings.profile.pageTitle'),
                href: edit(),
            },
        ],
    });
});

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <Head :title="t('app.settings.profile.pageTitle')" />

    <h1 class="sr-only">{{ t('app.settings.profile.pageTitle') }}</h1>

    <div class="flex flex-col space-y-6">
        <Heading
            variant="small"
            :title="t('app.settings.profile.heading')"
            :description="t('app.settings.profile.description')"
        />

        <Form
            v-bind="ProfileController.update.form()"
            class="space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="name">{{ t('app.settings.profile.name') }}</Label>
                <Input
                    id="name"
                    class="mt-1 block w-full"
                    name="name"
                    :default-value="user.name"
                    required
                    autocomplete="name"
                    :placeholder="t('app.settings.profile.fullName')"
                />
                <InputError class="mt-2" :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="email">{{ t('app.settings.profile.emailAddress') }}</Label>
                <Input
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    name="email"
                    :default-value="user.email"
                    required
                    autocomplete="username"
                    :placeholder="t('app.settings.profile.emailPlaceholder')"
                />
                <InputError class="mt-2" :message="errors.email" />
            </div>

            <div v-if="mustVerifyEmail && !user.email_verified_at">
                <p class="-mt-4 text-sm text-muted-foreground">
                    {{ t('app.settings.profile.unverifiedNotice') }}
                    <Link
                        :href="send()"
                        as="button"
                        class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                    >
                        {{ t('app.settings.profile.resendVerification') }}
                    </Link>
                </p>

                <div
                    v-if="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    {{ t('app.settings.profile.verificationSent') }}
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing" data-test="update-profile-button"
                    >{{ t('app.actions.save') }}</Button
                >
            </div>
        </Form>
    </div>

    <DeleteUser />
</template>
