<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

type Template = {
    id: number;
    name: string;
    description: string | null;
    request_category_id: number | null;
    community_id: number | null;
    building_id: number | null;
    schema: Record<string, unknown> | null;
    is_active: boolean;
};

const props = defineProps<{
    template: Template | null;
    categories: { id: number; name?: string | null; name_en?: string | null }[];
    communities: { id: number; name: string }[];
    buildings: { id: number; name: string; rf_community_id: number }[];
    selectedCommunityId: number | null;
}>();

const isEdit = computed(() => Boolean(props.template));
const schemaParseError = ref<string | null>(null);

const form = useForm({
    name: props.template?.name ?? '',
    description: props.template?.description ?? '',
    request_category_id: props.template?.request_category_id ?? null as number | null,
    community_id: props.template?.community_id ?? props.selectedCommunityId,
    building_id: props.template?.building_id ?? null as number | null,
    is_active: props.template?.is_active ?? true,
    schema: props.template?.schema ?? { fields: [] },
});

const schemaJson = ref(
    JSON.stringify(props.template?.schema ?? { fields: [] }, null, 2),
);

function submit() {
    schemaParseError.value = null;

    try {
        form.schema = JSON.parse(schemaJson.value);
    } catch {
        schemaParseError.value = 'Schema must be valid JSON.';
        return;
    }

    if (isEdit.value && props.template) {
        form.put(`/settings/forms/${props.template.id}`);
        return;
    }

    form.post('/settings/forms');
}

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
    <Head :title="isEdit ? 'Edit Form Template' : 'Create Form Template'" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading
                variant="small"
                :title="isEdit ? 'Edit Form Template' : 'Create Form Template'"
                description="Define reusable form schema and scope by community/building."
            />
            <div class="flex gap-2">
                <Button variant="outline" as-child>
                    <Link href="/settings/forms/select-community">Select Community</Link>
                </Button>
                <Button variant="outline" as-child>
                    <Link :href="`/settings/forms/select-building?community_id=${form.community_id ?? ''}`">Select Building</Link>
                </Button>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>{{ isEdit ? 'Update Template' : 'New Template' }}</CardTitle>
            </CardHeader>
            <CardContent>
                <form class="space-y-4" @submit.prevent="submit">
                    <div class="grid gap-2">
                        <Label for="name">Template Name</Label>
                        <Input id="name" v-model="form.name" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="description">Description</Label>
                        <Textarea id="description" v-model="form.description" rows="3" />
                        <InputError :message="form.errors.description" />
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="grid gap-2">
                            <Label for="request_category_id">Request Category</Label>
                            <select id="request_category_id" v-model.number="form.request_category_id" class="rounded-md border border-input bg-background px-3 py-2">
                                <option :value="null">Select category</option>
                                <option v-for="category in props.categories" :key="category.id" :value="category.id">
                                    {{ category.name_en ?? category.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.request_category_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="community_id">Community</Label>
                            <select id="community_id" v-model.number="form.community_id" class="rounded-md border border-input bg-background px-3 py-2">
                                <option :value="null">Select community</option>
                                <option v-for="community in props.communities" :key="community.id" :value="community.id">
                                    {{ community.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.community_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="building_id">Building</Label>
                            <select id="building_id" v-model.number="form.building_id" class="rounded-md border border-input bg-background px-3 py-2">
                                <option :value="null">Select building</option>
                                <option v-for="building in props.buildings" :key="building.id" :value="building.id">
                                    {{ building.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.building_id" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="schema">Schema (JSON)</Label>
                        <Textarea id="schema" v-model="schemaJson" rows="10" class="font-mono text-xs" />
                        <InputError :message="schemaParseError ?? form.errors.schema" />
                    </div>

                    <label class="flex items-center gap-2 text-sm">
                        <input v-model="form.is_active" type="checkbox" />
                        Active template
                    </label>

                    <div class="flex justify-end gap-2">
                        <Button variant="outline" as-child>
                            <Link href="/settings/forms">Cancel</Link>
                        </Button>
                        <Button :disabled="form.processing">{{ isEdit ? 'Update Template' : 'Create Template' }}</Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
