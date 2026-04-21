<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Documents', href: '/rf/excel-sheets/leads/errors' },
        ],
    },
});

const props = defineProps<{
    errors: {
        data: Array<{
            id: number;
            file_name: string | null;
            status: string;
            error_details: Record<string, unknown> | null;
            created_at: string;
        }>;
    };
}>();
</script>

<template>
    <Head title="Leads Import Errors" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            variant="small"
            title="Leads Import Errors"
            description="Review failed leads import files and their error details."
        />

        <Card>
            <CardHeader>
                <CardTitle>Error Records</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3">
                <div v-for="item in props.errors.data" :key="item.id" class="rounded-md border p-3 text-sm">
                    <p class="font-medium">{{ item.file_name ?? `Import #${item.id}` }}</p>
                    <p>Status: {{ item.status }}</p>
                    <p>Created: {{ item.created_at }}</p>
                    <pre class="mt-2 overflow-auto rounded bg-muted p-2 text-xs">{{ JSON.stringify(item.error_details, null, 2) }}</pre>
                </div>
                <p v-if="props.errors.data.length === 0" class="text-muted-foreground text-sm">No import errors found.</p>
            </CardContent>
        </Card>
    </div>
</template>
