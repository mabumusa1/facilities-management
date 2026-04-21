<script setup lang="ts">
import { Head, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.documents.leadsImportErrors.pageTitle'), href: '/rf/excel-sheets/leads/errors' },
        ],
    });
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
    <Head :title="t('app.documents.leadsImportErrors.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            variant="small"
            :title="t('app.documents.leadsImportErrors.heading')"
            :description="t('app.documents.leadsImportErrors.description')"
        />

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.documents.leadsImportErrors.errorRecords') }}</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3">
                <div v-for="item in props.errors.data" :key="item.id" class="rounded-md border p-3 text-sm">
                    <p class="font-medium">{{ item.file_name ?? t('app.documents.importFallback', { id: item.id }) }}</p>
                    <p>{{ t('app.documents.leadsImportErrors.status') }}: {{ item.status }}</p>
                    <p>{{ t('app.documents.leadsImportErrors.created') }}: {{ item.created_at }}</p>
                    <pre class="mt-2 overflow-auto rounded bg-muted p-2 text-xs">{{ JSON.stringify(item.error_details, null, 2) }}</pre>
                </div>
                <p v-if="props.errors.data.length === 0" class="text-muted-foreground text-sm">{{ t('app.documents.leadsImportErrors.empty') }}</p>
            </CardContent>
        </Card>
    </div>
</template>
