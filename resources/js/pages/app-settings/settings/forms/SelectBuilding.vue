<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';

const { t } = useI18n();

const props = defineProps<{
    communities: { id: number; name: string }[];
    selectedCommunityId: number | null;
    buildings: { id: number; name: string; rf_community_id: number }[];
}>();

function selectCommunity(event: Event) {
    const target = event.target as HTMLSelectElement;
    const communityId = target.value;
    router.get('/settings/forms/select-building', { community_id: communityId || undefined }, { preserveState: true });
}

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.settings'), href: '/settings/invoice' },
            { title: t('app.navigation.settingsForms'), href: '/settings/forms' },
            { title: t('app.settingsForms.selectBuilding'), href: '/settings/forms/select-building' },
        ],
    });
});
</script>

<template>
    <Head :title="t('app.settingsForms.selectBuilding')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="t('app.settingsForms.selectBuilding')" :description="t('app.settingsForms.selectBuildingDescription')" />

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.settingsForms.buildingScope') }}</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="grid gap-2">
                    <Label for="community_id">{{ t('app.settingsForms.community') }}</Label>
                    <select id="community_id" class="rounded-md border border-input bg-background px-3 py-2" :value="props.selectedCommunityId ?? ''" @change="selectCommunity">
                        <option value="">{{ t('app.settingsForms.selectCommunity') }}</option>
                        <option v-for="community in props.communities" :key="community.id" :value="community.id">
                            {{ community.name }}
                        </option>
                    </select>
                </div>

                <div class="space-y-2">
                    <div v-for="building in props.buildings" :key="building.id" class="flex items-center justify-between rounded-md border p-3">
                        <p class="text-sm font-medium">{{ building.name }}</p>
                        <Button size="sm" as-child>
                            <Link :href="`/settings/forms/create?community_id=${building.rf_community_id}`">{{ t('app.settingsForms.useInTemplate') }}</Link>
                        </Button>
                    </div>
                    <p v-if="props.selectedCommunityId && props.buildings.length === 0" class="text-muted-foreground text-sm">{{ t('app.settingsForms.noBuildingsForCommunity') }}</p>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
