<script setup lang="ts">
import { Head, Link, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const { t } = useI18n();

type Template = {
    id: number;
    name: string;
    description: string | null;
    is_active: boolean;
    schema: {
        fields?: Array<{
            key?: string;
            label?: string;
            type?: string;
            required?: boolean;
            placeholder?: string;
        }>;
    } | null;
    request_category?: { name?: string | null; name_en?: string | null } | null;
    community?: { name?: string | null } | null;
    building?: { name?: string | null } | null;
};

const props = defineProps<{
    template: Template;
    requiredFields: Array<Record<string, unknown>>;
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.settings'), href: '/settings/invoice' },
            { title: t('app.navigation.settingsForms'), href: '/settings/forms' },
        ],
    });
});
</script>

<template>
    <Head :title="t('app.settingsForms.templatePreviewTitle', { name: props.template.name })" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading
                variant="small"
                :title="t('app.settingsForms.previewTitle', { name: props.template.name })"
                :description="t('app.settingsForms.previewDescription')"
            />
            <div class="flex gap-2">
                <Button variant="outline" as-child>
                    <Link href="/settings/forms">{{ t('app.actions.back') }}</Link>
                </Button>
                <Button as-child>
                    <Link :href="`/settings/forms/${props.template.id}/edit`">{{ t('app.actions.edit') }}</Link>
                </Button>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.settingsForms.templateDetails') }}</CardTitle>
            </CardHeader>
            <CardContent class="grid gap-2 text-sm md:grid-cols-2">
                <p><span class="font-medium">{{ t('app.settingsForms.status') }}:</span>
                    <Badge :variant="props.template.is_active ? 'default' : 'secondary'">
                        {{ props.template.is_active ? t('app.common.active') : t('app.common.inactive') }}
                    </Badge>
                </p>
                <p><span class="font-medium">{{ t('app.settingsForms.category') }}:</span> {{ props.template.request_category?.name_en ?? props.template.request_category?.name ?? t('app.common.notAvailable') }}</p>
                <p><span class="font-medium">{{ t('app.settingsForms.community') }}:</span> {{ props.template.community?.name ?? t('app.common.notAvailable') }}</p>
                <p><span class="font-medium">{{ t('app.settingsForms.building') }}:</span> {{ props.template.building?.name ?? t('app.common.notAvailable') }}</p>
                <p class="md:col-span-2"><span class="font-medium">{{ t('app.settingsForms.descriptionLabel') }}:</span> {{ props.template.description ?? t('app.common.notAvailable') }}</p>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.settingsForms.fieldsPreview') }}</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
                <div
                    v-for="(field, index) in props.template.schema?.fields ?? []"
                    :key="`${field.key ?? 'field'}-${index}`"
                    class="rounded-md border p-4"
                >
                    <p class="text-sm font-medium">{{ field.label ?? field.key ?? t('app.settingsForms.fieldFallback', { number: index + 1 }) }}</p>
                    <p class="text-muted-foreground text-xs">{{ t('app.settingsForms.type') }}: {{ field.type ?? 'text' }}</p>
                    <p class="text-muted-foreground text-xs">{{ t('app.settingsForms.required') }}: {{ field.required ? t('app.common.yes') : t('app.common.no') }}</p>
                    <input
                        class="mt-2 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        :placeholder="field.placeholder ?? ''"
                        :type="field.type === 'number' ? 'number' : 'text'"
                        disabled
                    />
                </div>

                <p v-if="(props.template.schema?.fields ?? []).length === 0" class="text-muted-foreground text-sm">
                    {{ t('app.settingsForms.noFieldsConfigured') }}
                </p>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.settingsForms.requiredFields') }}</CardTitle>
            </CardHeader>
            <CardContent>
                <ul class="list-disc space-y-1 pl-5 text-sm">
                    <li v-for="(field, index) in props.requiredFields" :key="index">
                        {{ field.label ?? field.key ?? t('app.settingsForms.fieldFallback', { number: index + 1 }) }}
                    </li>
                    <li v-if="props.requiredFields.length === 0" class="text-muted-foreground list-none pl-0">{{ t('app.settingsForms.noRequiredFields') }}</li>
                </ul>
            </CardContent>
        </Card>
    </div>
</template>
