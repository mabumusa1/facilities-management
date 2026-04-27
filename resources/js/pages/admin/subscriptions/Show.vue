<script setup lang="ts">
import { Head, setLayoutProps } from '@inertiajs/vue3';
import { computed, ref, watchEffect } from 'vue';
import Heading from '@/components/Heading.vue';
import { useI18n } from '@/composables/useI18n';
import { index as subscriptionsIndexRoute } from '@/routes/admin/subscriptions';
import FeaturesTab from './components/FeaturesTab.vue';
import type { FeatureFlag } from './components/FeaturesTab.vue';

const { t } = useI18n();

type TenantInfo = {
    id: number;
    name: string;
    domain: string | null;
};

const props = defineProps<{
    tenant: TenantInfo;
    features: FeatureFlag[] | undefined;
}>();

type Tab = 'overview' | 'features' | 'users' | 'subscription';
const activeTab = ref<Tab>('features');

const featuresCount = computed<number>(() => props.features?.length ?? 0);

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.accountSubscriptions'), href: subscriptionsIndexRoute.url() },
            { title: props.tenant.name, href: '#' },
        ],
    });
});
</script>

<template>
    <Head :title="t('app.admin.featureFlags.pageTitle', { tenant: tenant.name })" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between">
            <div>
                <Heading>{{ tenant.name }}</Heading>
                <p class="text-muted-foreground text-sm">{{ tenant.domain ?? '—' }}</p>
            </div>
        </div>

        <div class="border-b">
            <nav class="-mb-px flex gap-6">
                <button
                    class="pb-2 text-sm font-medium transition-colors"
                    :class="activeTab === 'overview' ? 'border-b-2 border-current' : 'text-muted-foreground hover:text-foreground'"
                    @click="activeTab = 'overview'"
                >
                    {{ t('app.admin.featureFlags.tabOverview') }}
                </button>
                <button
                    class="pb-2 text-sm font-medium transition-colors"
                    :class="activeTab === 'features' ? 'border-b-2 border-current' : 'text-muted-foreground hover:text-foreground'"
                    @click="activeTab = 'features'"
                >
                    {{ t('app.admin.featureFlags.tabFeatures') }}
                    <span v-if="features !== undefined" class="ms-1 text-xs">({{ featuresCount }})</span>
                    <span v-else class="ms-1 h-2 w-2 inline-block rounded-full bg-current" />
                </button>
                <button
                    class="pb-2 text-sm font-medium transition-colors text-muted-foreground hover:text-foreground"
                    :class="activeTab === 'users' ? 'border-b-2 border-current text-foreground' : ''"
                    @click="activeTab = 'users'"
                >
                    {{ t('app.admin.featureFlags.tabUsers') }}
                </button>
                <button
                    class="pb-2 text-sm font-medium transition-colors text-muted-foreground hover:text-foreground"
                    :class="activeTab === 'subscription' ? 'border-b-2 border-current text-foreground' : ''"
                    @click="activeTab = 'subscription'"
                >
                    {{ t('app.admin.featureFlags.tabSubscription') }}
                </button>
            </nav>
        </div>

        <div>
            <FeaturesTab
                v-if="activeTab === 'features'"
                :features="features"
                :tenant-id="tenant.id"
            />

            <div v-else-if="activeTab === 'overview'" class="text-muted-foreground py-8 text-center text-sm">
                {{ t('app.admin.featureFlags.tabOverview') }}
            </div>

            <div v-else-if="activeTab === 'users'" class="text-muted-foreground py-8 text-center text-sm">
                {{ t('app.admin.featureFlags.tabUsers') }}
            </div>

            <div v-else-if="activeTab === 'subscription'" class="text-muted-foreground py-8 text-center text-sm">
                {{ t('app.admin.featureFlags.tabSubscription') }}
            </div>
        </div>
    </div>
</template>
