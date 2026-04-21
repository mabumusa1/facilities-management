<script setup lang="ts">
import { Head, Link, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.settings'), href: '/settings/invoice' },
            { title: t('app.navigation.settingsForms'), href: '/settings/forms' },
            { title: t('app.settingsForms.selectCommunity'), href: '/settings/forms/select-community' },
        ],
    });
});

const props = defineProps<{
    communities: { id: number; name: string }[];
}>();
</script>

<template>
    <Head :title="t('app.settingsForms.selectCommunity')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="t('app.settingsForms.selectCommunity')" :description="t('app.settingsForms.selectCommunityDescription')" />

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.settingsForms.communities') }}</CardTitle>
            </CardHeader>
            <CardContent class="space-y-2">
                <div v-for="community in props.communities" :key="community.id" class="flex items-center justify-between rounded-md border p-3">
                    <p class="text-sm font-medium">{{ community.name }}</p>
                    <Button size="sm" as-child>
                        <Link :href="`/settings/forms/create?community_id=${community.id}`">{{ t('app.settingsForms.select') }}</Link>
                    </Button>
                </div>
                <p v-if="props.communities.length === 0" class="text-muted-foreground text-sm">{{ t('app.settingsForms.noCommunities') }}</p>
            </CardContent>
        </Card>
    </div>
</template>
