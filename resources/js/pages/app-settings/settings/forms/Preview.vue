<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

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

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Settings', href: '/settings/invoice' },
            { title: 'Forms', href: '/settings/forms' },
        ],
    },
});
</script>

<template>
    <Head :title="`Template Preview - ${props.template.name}`" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading
                variant="small"
                :title="`Preview: ${props.template.name}`"
                description="Preview structure and required fields before publishing."
            />
            <div class="flex gap-2">
                <Button variant="outline" as-child>
                    <Link href="/settings/forms">Back</Link>
                </Button>
                <Button as-child>
                    <Link :href="`/settings/forms/${props.template.id}/edit`">Edit</Link>
                </Button>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Template Details</CardTitle>
            </CardHeader>
            <CardContent class="grid gap-2 text-sm md:grid-cols-2">
                <p><span class="font-medium">Status:</span>
                    <Badge :variant="props.template.is_active ? 'default' : 'secondary'">
                        {{ props.template.is_active ? 'Active' : 'Inactive' }}
                    </Badge>
                </p>
                <p><span class="font-medium">Category:</span> {{ props.template.request_category?.name_en ?? props.template.request_category?.name ?? 'N/A' }}</p>
                <p><span class="font-medium">Community:</span> {{ props.template.community?.name ?? 'N/A' }}</p>
                <p><span class="font-medium">Building:</span> {{ props.template.building?.name ?? 'N/A' }}</p>
                <p class="md:col-span-2"><span class="font-medium">Description:</span> {{ props.template.description ?? 'N/A' }}</p>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Fields Preview</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
                <div
                    v-for="(field, index) in props.template.schema?.fields ?? []"
                    :key="`${field.key ?? 'field'}-${index}`"
                    class="rounded-md border p-4"
                >
                    <p class="text-sm font-medium">{{ field.label ?? field.key ?? `Field ${index + 1}` }}</p>
                    <p class="text-muted-foreground text-xs">Type: {{ field.type ?? 'text' }}</p>
                    <p class="text-muted-foreground text-xs">Required: {{ field.required ? 'Yes' : 'No' }}</p>
                    <input
                        class="mt-2 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        :placeholder="field.placeholder ?? ''"
                        :type="field.type === 'number' ? 'number' : 'text'"
                        disabled
                    />
                </div>

                <p v-if="(props.template.schema?.fields ?? []).length === 0" class="text-muted-foreground text-sm">
                    No fields are configured yet.
                </p>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Required Fields</CardTitle>
            </CardHeader>
            <CardContent>
                <ul class="list-disc space-y-1 pl-5 text-sm">
                    <li v-for="(field, index) in props.requiredFields" :key="index">
                        {{ field.label ?? field.key ?? `Field ${index + 1}` }}
                    </li>
                    <li v-if="props.requiredFields.length === 0" class="text-muted-foreground list-none pl-0">No required fields defined.</li>
                </ul>
            </CardContent>
        </Card>
    </div>
</template>
