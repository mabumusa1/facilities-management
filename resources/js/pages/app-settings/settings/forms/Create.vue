<script setup lang="ts">
import { Head, Link, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, ref, watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

const { t } = useI18n();

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

type TemplateFormState = {
    name: string;
    description: string;
    request_category_id: number | null;
    community_id: number | null;
    building_id: number | null;
    is_active: boolean;
    schema: string;
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
const initialSchema = (props.template?.schema ?? { fields: [] }) as Record<string, unknown>;

const form = useForm<TemplateFormState>({
    name: props.template?.name ?? '',
    description: props.template?.description ?? '',
    request_category_id: props.template?.request_category_id ?? null,
    community_id: props.template?.community_id ?? props.selectedCommunityId,
    building_id: props.template?.building_id ?? null,
    is_active: props.template?.is_active ?? true,
    schema: JSON.stringify(initialSchema, null, 2),
});

function submit() {
    schemaParseError.value = null;

    let parsedSchema: Record<string, unknown>;

    try {
        parsedSchema = JSON.parse(form.schema) as Record<string, unknown>;
    } catch {
        schemaParseError.value = t('app.settingsForms.schemaMustBeValidJson');
        return;
    }

    form.transform((data) => ({
        ...data,
        schema: parsedSchema,
    }));

    if (isEdit.value && props.template) {
        form.put(`/settings/forms/${props.template.id}`);
        return;
    }

    form.post('/settings/forms');
}

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
    <Head :title="isEdit ? t('app.settingsForms.editTemplate') : t('app.settingsForms.createTemplate')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading
                variant="small"
                :title="isEdit ? t('app.settingsForms.editTemplate') : t('app.settingsForms.createTemplate')"
                :description="t('app.settingsForms.createDescription')"
            />
            <div class="flex gap-2">
                <Button variant="outline" as-child>
                    <Link href="/settings/forms/select-community">{{ t('app.settingsForms.selectCommunity') }}</Link>
                </Button>
                <Button variant="outline" as-child>
                    <Link :href="`/settings/forms/select-building?community_id=${form.community_id ?? ''}`">{{ t('app.settingsForms.selectBuilding') }}</Link>
                </Button>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>{{ isEdit ? t('app.settingsForms.updateTemplate') : t('app.settingsForms.newTemplate') }}</CardTitle>
            </CardHeader>
            <CardContent>
                <form class="space-y-4" @submit.prevent="submit">
                    <div class="grid gap-2">
                        <Label for="name">{{ t('app.settingsForms.templateName') }}</Label>
                        <Input id="name" v-model="form.name" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="description">{{ t('app.settingsForms.descriptionLabel') }}</Label>
                        <Textarea id="description" v-model="form.description" rows="3" />
                        <InputError :message="form.errors.description" />
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="grid gap-2">
                            <Label for="request_category_id">{{ t('app.settingsForms.requestCategory') }}</Label>
                            <select id="request_category_id" v-model.number="form.request_category_id" class="rounded-md border border-input bg-background px-3 py-2">
                                <option :value="null">{{ t('app.settingsForms.selectCategory') }}</option>
                                <option v-for="category in props.categories" :key="category.id" :value="category.id">
                                    {{ category.name_en ?? category.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.request_category_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="community_id">{{ t('app.settingsForms.community') }}</Label>
                            <select id="community_id" v-model.number="form.community_id" class="rounded-md border border-input bg-background px-3 py-2">
                                <option :value="null">{{ t('app.settingsForms.selectCommunity') }}</option>
                                <option v-for="community in props.communities" :key="community.id" :value="community.id">
                                    {{ community.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.community_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="building_id">{{ t('app.settingsForms.building') }}</Label>
                            <select id="building_id" v-model.number="form.building_id" class="rounded-md border border-input bg-background px-3 py-2">
                                <option :value="null">{{ t('app.settingsForms.selectBuilding') }}</option>
                                <option v-for="building in props.buildings" :key="building.id" :value="building.id">
                                    {{ building.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.building_id" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="schema">{{ t('app.settingsForms.schemaJson') }}</Label>
                        <Textarea id="schema" v-model="form.schema" rows="10" class="font-mono text-xs" />
                        <InputError :message="schemaParseError ?? form.errors.schema" />
                    </div>

                    <label class="flex items-center gap-2 text-sm">
                        <input v-model="form.is_active" type="checkbox" />
                        {{ t('app.settingsForms.activeTemplate') }}
                    </label>

                    <div class="flex justify-end gap-2">
                        <Button variant="outline" as-child>
                            <Link href="/settings/forms">{{ t('app.actions.cancel') }}</Link>
                        </Button>
                        <Button :disabled="form.processing">{{ isEdit ? t('app.settingsForms.updateTemplate') : t('app.settingsForms.createTemplate') }}</Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
