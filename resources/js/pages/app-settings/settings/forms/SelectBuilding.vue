<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';

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

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Settings', href: '/settings/invoice' },
            { title: 'Forms', href: '/settings/forms' },
            { title: 'Select Building', href: '/settings/forms/select-building' },
        ],
    },
});
</script>

<template>
    <Head title="Select Building" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" title="Select Building" description="Filter by community then select a building for the template scope." />

        <Card>
            <CardHeader>
                <CardTitle>Building Scope</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="grid gap-2">
                    <Label for="community_id">Community</Label>
                    <select id="community_id" class="rounded-md border border-input bg-background px-3 py-2" :value="props.selectedCommunityId ?? ''" @change="selectCommunity">
                        <option value="">Select community</option>
                        <option v-for="community in props.communities" :key="community.id" :value="community.id">
                            {{ community.name }}
                        </option>
                    </select>
                </div>

                <div class="space-y-2">
                    <div v-for="building in props.buildings" :key="building.id" class="flex items-center justify-between rounded-md border p-3">
                        <p class="text-sm font-medium">{{ building.name }}</p>
                        <Button size="sm" as-child>
                            <Link :href="`/settings/forms/create?community_id=${building.rf_community_id}`">Use in Template</Link>
                        </Button>
                    </div>
                    <p v-if="props.selectedCommunityId && props.buildings.length === 0" class="text-muted-foreground text-sm">No buildings found for this community.</p>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
