<script setup lang="ts">
import { Head, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import Heading from '@/components/Heading.vue';
import { edit } from '@/routes/appearance';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            {
                title: t('app.settings.appearance.pageTitle'),
                href: edit(),
            },
        ],
    });
});
</script>

<template>
    <Head :title="t('app.settings.appearance.pageTitle')" />

    <h1 class="sr-only">{{ t('app.settings.appearance.pageTitle') }}</h1>

    <div class="space-y-6">
        <Heading
            variant="small"
            :title="t('app.settings.appearance.heading')"
            :description="t('app.settings.appearance.description')"
        />
        <AppearanceTabs />
    </div>
</template>
