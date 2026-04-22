<script setup lang="ts">
import { Head, Link, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, ref, watch, watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    index as formsIndex,
    store as storeTemplateRoute,
    update as updateTemplateRoute,
} from '@/routes/settings/forms';

const { t, isArabic } = useI18n();

type TemplateSchemaFieldType = 'text' | 'textarea' | 'number' | 'email' | 'tel' | 'date';

type TemplateSchemaField = {
    key: string;
    label: string;
    type: TemplateSchemaFieldType;
    required: boolean;
    placeholder: string;
};

type SchemaPayloadField = {
    key: string;
    type: TemplateSchemaFieldType;
    required: boolean;
    label?: string;
    placeholder?: string;
};

const FIELD_TYPE_OPTIONS: Array<{ value: TemplateSchemaFieldType; labelKey: string }> = [
    { value: 'text', labelKey: 'app.settingsForms.fieldTypeText' },
    { value: 'textarea', labelKey: 'app.settingsForms.fieldTypeTextarea' },
    { value: 'number', labelKey: 'app.settingsForms.fieldTypeNumber' },
    { value: 'email', labelKey: 'app.settingsForms.fieldTypeEmail' },
    { value: 'tel', labelKey: 'app.settingsForms.fieldTypeTel' },
    { value: 'date', labelKey: 'app.settingsForms.fieldTypeDate' },
];

type Template = {
    id: number;
    name: string;
    description: string | null;
    request_category_id: number | null;
    community_id: number | null;
    building_id: number | null;
    schema: {
        fields?: Array<Record<string, unknown>>;
    } | null;
    is_active: boolean;
};

type TemplateFormState = {
    name: string;
    description: string;
    request_category_id: number | null;
    community_id: number | null;
    building_id: number | null;
    is_active: boolean;
    schema: {
        fields: SchemaPayloadField[];
    };
};

const props = defineProps<{
    template: Template | null;
    categories: { id: number; name?: string | null; name_ar?: string | null; name_en?: string | null }[];
    communities: { id: number; name: string }[];
    buildings: { id: number; name: string; rf_community_id: number }[];
    selectedCommunityId: number | null;
}>();

const isEdit = computed(() => Boolean(props.template));

function emptyField(): TemplateSchemaField {
    return {
        key: '',
        label: '',
        type: 'text',
        required: false,
        placeholder: '',
    };
}

function normalizeFieldType(value: unknown): TemplateSchemaFieldType {
    if (
        value === 'text' ||
        value === 'textarea' ||
        value === 'number' ||
        value === 'email' ||
        value === 'tel' ||
        value === 'date'
    ) {
        return value;
    }

    return 'text';
}

function normalizeSchemaFields(schema: Template['schema']): TemplateSchemaField[] {
    const rawFields = Array.isArray(schema?.fields) ? schema.fields : [];

    return rawFields.map((field): TemplateSchemaField => {
        if (typeof field !== 'object' || field === null) {
            return emptyField();
        }

        return {
            key: typeof field.key === 'string' ? field.key : '',
            label: typeof field.label === 'string' ? field.label : '',
            type: normalizeFieldType(field.type),
            required: Boolean(field.required),
            placeholder: typeof field.placeholder === 'string' ? field.placeholder : '',
        };
    });
}

function sanitizeFieldKey(value: string): string {
    return value
        .trim()
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '_')
        .replace(/^_+|_+$/g, '');
}

function toSchemaPayloadFields(fields: TemplateSchemaField[]): SchemaPayloadField[] {
    return fields.map((field, index): SchemaPayloadField => {
        const key = sanitizeFieldKey(field.key) || sanitizeFieldKey(field.label) || `field_${index + 1}`;
        const payloadField: SchemaPayloadField = {
            key,
            type: field.type,
            required: field.required,
        };

        const label = field.label.trim();

        if (label.length > 0) {
            payloadField.label = label;
        }

        const placeholder = field.placeholder.trim();

        if (placeholder.length > 0) {
            payloadField.placeholder = placeholder;
        }

        return payloadField;
    });
}

const schemaFields = ref<TemplateSchemaField[]>(normalizeSchemaFields(props.template?.schema ?? null));

if (!isEdit.value && schemaFields.value.length === 0) {
    schemaFields.value.push(emptyField());
}

const form = useForm<TemplateFormState>({
    name: props.template?.name ?? '',
    description: props.template?.description ?? '',
    request_category_id: props.template?.request_category_id ?? null,
    community_id: props.template?.community_id ?? props.selectedCommunityId,
    building_id: props.template?.building_id ?? null,
    is_active: props.template?.is_active ?? true,
    schema: {
        fields: toSchemaPayloadFields(schemaFields.value),
    },
});

const availableBuildings = computed(() => {
    if (!form.community_id) {
        return [];
    }

    return props.buildings.filter((building) => building.rf_community_id === form.community_id);
});

watch(
    () => form.community_id,
    (communityId) => {
        if (!communityId) {
            form.building_id = null;
            return;
        }

        if (form.building_id && !availableBuildings.value.some((building) => building.id === form.building_id)) {
            form.building_id = null;
        }
    },
);

function localizedCategoryName(category: { name?: string | null; name_ar?: string | null; name_en?: string | null }): string {
    if (isArabic.value) {
        return category.name_ar ?? category.name ?? category.name_en ?? '';
    }

    return category.name_en ?? category.name ?? category.name_ar ?? '';
}

function addField(): void {
    schemaFields.value.push(emptyField());
}

function removeField(index: number): void {
    schemaFields.value.splice(index, 1);
}

function submit() {
    form.schema.fields = toSchemaPayloadFields(schemaFields.value);

    if (isEdit.value && props.template) {
        form.put(updateTemplateRoute.url({ formTemplate: props.template.id }));
        return;
    }

    form.post(storeTemplateRoute.url());
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

    <div class="mx-auto flex w-full max-w-5xl flex-col gap-6 p-4">
        <Heading
            variant="small"
            :title="isEdit ? t('app.settingsForms.editTemplate') : t('app.settingsForms.createTemplate')"
            :description="t('app.settingsForms.createDescription')"
        />

        <Card>
            <CardHeader class="space-y-1">
                <CardTitle>{{ isEdit ? t('app.settingsForms.updateTemplate') : t('app.settingsForms.newTemplate') }}</CardTitle>
                <p class="text-muted-foreground text-sm">{{ t('app.settingsForms.simpleFlowHint') }}</p>
            </CardHeader>
            <CardContent>
                <form class="space-y-6" @submit.prevent="submit">
                    <section class="space-y-4 rounded-md border p-4">
                        <div>
                            <h3 class="text-sm font-semibold">{{ t('app.settingsForms.basicDetails') }}</h3>
                            <p class="text-muted-foreground text-xs">{{ t('app.settingsForms.basicDetailsDescription') }}</p>
                        </div>

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
                    </section>

                    <section class="space-y-4 rounded-md border p-4">
                        <div>
                            <h3 class="text-sm font-semibold">{{ t('app.settingsForms.whereUsed') }}</h3>
                            <p class="text-muted-foreground text-xs">{{ t('app.settingsForms.whereUsedDescription') }}</p>
                        </div>

                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="grid gap-2">
                                <Label for="request_category_id">{{ t('app.settingsForms.requestCategory') }}</Label>
                                <select id="request_category_id" v-model.number="form.request_category_id" class="rounded-md border border-input bg-background px-3 py-2">
                                    <option :value="null">{{ t('app.settingsForms.selectCategory') }}</option>
                                    <option v-for="category in props.categories" :key="category.id" :value="category.id">
                                        {{ localizedCategoryName(category) }}
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
                                <select id="building_id" v-model.number="form.building_id" :disabled="!form.community_id" class="rounded-md border border-input bg-background px-3 py-2 disabled:cursor-not-allowed disabled:opacity-60">
                                    <option :value="null">
                                        {{ !form.community_id ? t('app.settingsForms.selectCommunityFirst') : t('app.settingsForms.selectBuilding') }}
                                    </option>
                                    <option v-for="building in availableBuildings" :key="building.id" :value="building.id">
                                        {{ building.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.building_id" />
                            </div>
                        </div>

                        <p v-if="!form.community_id" class="text-muted-foreground text-xs">
                            {{ t('app.settingsForms.selectCommunityFirst') }}
                        </p>
                        <p v-else-if="availableBuildings.length === 0" class="text-muted-foreground text-xs">
                            {{ t('app.settingsForms.noBuildingsForCommunity') }}
                        </p>
                    </section>

                    <section class="space-y-3 rounded-md border p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <h3 class="text-sm font-semibold">{{ t('app.settingsForms.formBuilder') }}</h3>
                                <p class="text-muted-foreground mt-1 text-xs">{{ t('app.settingsForms.formBuilderDescription') }}</p>
                            </div>
                            <Button type="button" size="sm" variant="outline" @click="addField">
                                {{ t('app.settingsForms.addField') }}
                            </Button>
                        </div>

                        <p class="text-muted-foreground text-xs">{{ t('app.settingsForms.fieldKeyAutoGenerated') }}</p>

                        <div v-if="schemaFields.length === 0" class="space-y-3 rounded-md border border-dashed p-4 text-sm">
                            <p class="text-muted-foreground">{{ t('app.settingsForms.noFieldsConfigured') }}</p>
                            <Button type="button" size="sm" variant="outline" @click="addField">
                                {{ t('app.settingsForms.addFirstField') }}
                            </Button>
                        </div>

                        <div
                            v-for="(field, index) in schemaFields"
                            :key="`schema-field-${index}`"
                            class="space-y-4 rounded-md border bg-background p-4"
                        >
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="text-sm font-medium">{{ t('app.settingsForms.fieldPosition', { number: index + 1 }) }}</p>
                                <Button type="button" size="sm" variant="destructive" @click="removeField(index)">
                                    {{ t('app.settingsForms.removeField') }}
                                </Button>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="grid gap-2 md:col-span-2">
                                    <Label :for="`field-label-${index}`">{{ t('app.settingsForms.fieldLabel') }}</Label>
                                    <Input :id="`field-label-${index}`" v-model="field.label" />
                                </div>

                                <div class="grid gap-2">
                                    <Label :for="`field-type-${index}`">{{ t('app.settingsForms.fieldType') }}</Label>
                                    <select
                                        :id="`field-type-${index}`"
                                        v-model="field.type"
                                        class="rounded-md border border-input bg-background px-3 py-2"
                                    >
                                        <option v-for="fieldType in FIELD_TYPE_OPTIONS" :key="fieldType.value" :value="fieldType.value">
                                            {{ t(fieldType.labelKey) }}
                                        </option>
                                    </select>
                                </div>

                                <div class="grid gap-2">
                                    <Label :for="`field-placeholder-${index}`">{{ t('app.settingsForms.fieldPlaceholder') }}</Label>
                                    <Input :id="`field-placeholder-${index}`" v-model="field.placeholder" />
                                </div>
                            </div>

                            <label class="flex items-center gap-2 text-sm">
                                <Checkbox :checked="field.required" @update:checked="field.required = $event === true" />
                                {{ t('app.settingsForms.required') }}
                            </label>
                        </div>

                        <InputError :message="form.errors.schema ?? form.errors['schema.fields']" />
                    </section>

                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-md border p-4">
                        <label class="flex items-center gap-2 text-sm">
                            <Checkbox :checked="form.is_active" @update:checked="form.is_active = $event === true" />
                            {{ t('app.settingsForms.activeTemplate') }}
                        </label>

                        <div class="flex justify-end gap-2">
                            <Button variant="outline" as-child>
                                <Link :href="formsIndex.url()">{{ t('app.actions.cancel') }}</Link>
                            </Button>
                            <Button :disabled="form.processing">{{ isEdit ? t('app.settingsForms.updateTemplate') : t('app.settingsForms.createTemplate') }}</Button>
                        </div>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
