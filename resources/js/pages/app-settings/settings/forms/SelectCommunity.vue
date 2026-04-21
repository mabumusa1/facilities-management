<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Settings', href: '/settings/invoice' },
            { title: 'Forms', href: '/settings/forms' },
            { title: 'Select Community', href: '/settings/forms/select-community' },
        ],
    },
});

const props = defineProps<{
    communities: { id: number; name: string }[];
}>();
</script>

<template>
    <Head title="Select Community" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" title="Select Community" description="Pick a community and continue creating your form template." />

        <Card>
            <CardHeader>
                <CardTitle>Communities</CardTitle>
            </CardHeader>
            <CardContent class="space-y-2">
                <div v-for="community in props.communities" :key="community.id" class="flex items-center justify-between rounded-md border p-3">
                    <p class="text-sm font-medium">{{ community.name }}</p>
                    <Button size="sm" as-child>
                        <Link :href="`/settings/forms/create?community_id=${community.id}`">Select</Link>
                    </Button>
                </div>
                <p v-if="props.communities.length === 0" class="text-muted-foreground text-sm">No communities available.</p>
            </CardContent>
        </Card>
    </div>
</template>
