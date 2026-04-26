<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Textarea } from '@/components/ui/textarea';
import { useI18n } from '@/composables/useI18n';
import { update } from '@/routes/admin/documents';
import { Plus, Trash2 } from 'lucide-vue-next';

const { t } = useI18n();

type TemplateType = { value: string; label: string };
type FieldType = { value: string; label: string };
type Version = {
    id: number;
    version_number: number;
    body: { en: string; ar: string } | null;
    merge_fields: MergeField[];
    published_at: string | null;
    creator: { id: number; name: string } | null;
};
type MergeField = {
    key: string;
    label_en: string;
    label_ar: string | null;
    type: string;
    source_path: string;
};

const props = defineProps<{
    template: {
        id: number;
        name: { en: string; ar: string | null };
        type: string;
        status: string;
        format: string;
        current_version_id: number | null;
        versions: Version[];
        created_at: string | null;
    };
    templateTypes: TemplateType[];
    fieldTypes: FieldType[];
}>();

const form = useForm({
    name_en: props.template.name.en,
    name_ar: props.template.name.ar ?? '',
    type: props.template.type,
    format: props.template.format,
    body_en: props.template.versions[0]?.body?.en ?? '',
    body_ar: props.template.versions[0]?.body?.ar ?? '',
    merge_fields: (props.template.versions[0]?.merge_fields ?? []) as MergeField[],
});

const selectedVersion = ref(props.template.versions[0]?.id ?? null);

const currentBody = computed(() => {
    const v = props.template.versions.find((v) => v.id === selectedVersion.value);
    return v?.body ?? null;
});

const currentMergeFields = computed(() => {
    const v = props.template.versions.find((v) => v.id === selectedVersion.value);
    return v?.merge_fields ?? [];
});

function statusVariant(status: string): 'default' | 'success' | 'secondary' {
    return status === 'active' ? 'success' : status === 'draft' ? 'secondary' : 'default';
}

function statusLabel(status: string): string {
    return status === 'active'
        ? t('app.admin.documents.statusActive')
        : status === 'draft'
          ? t('app.admin.documents.statusDraft')
          : t('app.admin.documents.statusArchived');
}

function addMergeField() {
    form.merge_fields.push({
        key: '',
        label_en: '',
        label_ar: '',
        type: 'string',
        source_path: '',
    });
}

function removeMergeField(index: number) {
    form.merge_fields.splice(index, 1);
}

function submitUpdate() {
    form.put(update.url({ documentTemplate: props.template.id }), {
        preserveScroll: true,
        onSuccess: () => {
            window.location.reload();
        },
    });
}
</script>

<template>
    <Head :title="`${t('app.admin.documents.edit')}: ${template.name.en}`" />
    <Heading
        :title="`${t('app.admin.documents.edit')}: ${template.name.en}`"
        :description="`Type: ${template.type} · Status:`"
    >
        <Badge :variant="statusVariant(template.status)" class="ml-2">
            {{ statusLabel(template.status) }}
        </Badge>
    </Heading>

    <div class="flex gap-8">
        <div class="flex-1 space-y-6 max-w-2xl">
            <div class="grid gap-2">
                <Label for="name_en">{{ t('app.admin.documents.nameEn') }} *</Label>
                <Input id="name_en" v-model="form.name_en" dir="ltr" required />
                <InputError :message="form.errors.name_en" />
            </div>

            <div class="grid gap-2">
                <Label for="name_ar">{{ t('app.admin.documents.nameAr') }}</Label>
                <Input id="name_ar" v-model="form.name_ar" dir="rtl" />
                <InputError :message="form.errors.name_ar" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label>{{ t('app.admin.documents.type') }}</Label>
                    <Select v-model="form.type">
                        <SelectTrigger>
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="t in templateTypes" :key="t.value" :value="t.value">
                                {{ t.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.type" />
                </div>

                <div class="grid gap-2">
                    <Label>{{ t('app.admin.documents.format') }}</Label>
                    <Select v-model="form.format">
                        <SelectTrigger>
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="in_platform">
                                {{ t('app.admin.documents.formatInPlatform') }}
                            </SelectItem>
                            <SelectItem value="word_upload">
                                {{ t('app.admin.documents.formatWordUpload') }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.format" />
                </div>
            </div>

            <Tabs default-value="en">
                <TabsList>
                    <TabsTrigger value="en">English</TabsTrigger>
                    <TabsTrigger value="ar">العربية</TabsTrigger>
                </TabsList>
                <TabsContent value="en">
                    <div class="grid gap-2">
                        <Label>{{ t('app.admin.documents.bodyEn') }}</Label>
                        <Textarea
                            v-model="form.body_en"
                            dir="ltr"
                            rows="10"
                            placeholder="Enter template body with {{merge_fields}}..."
                        />
                        <InputError :message="form.errors.body_en" />
                    </div>
                </TabsContent>
                <TabsContent value="ar">
                    <div class="grid gap-2">
                        <Label>{{ t('app.admin.documents.bodyAr') }}</Label>
                        <Textarea
                            v-model="form.body_ar"
                            dir="rtl"
                            rows="10"
                            placeholder="أدخل نص القالب مع {{حقول_الدمج}}..."
                        />
                        <InputError :message="form.errors.body_ar" />
                    </div>
                </TabsContent>
            </Tabs>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <Label>{{ t('app.admin.documents.mergeFields') }}</Label>
                    <Button variant="outline" size="sm" @click="addMergeField">
                        <Plus class="h-3 w-3 mr-1" />
                        {{ t('app.admin.documents.addMergeField') }}
                    </Button>
                </div>

                <div v-if="form.merge_fields.length === 0" class="text-sm text-muted-foreground py-4">
                    No merge fields defined. Add fields like <code>{`{{lease.start_date}}`}</code> or
                    <code>{`{{resident.full_name}}`}</code>.
                </div>

                <div v-for="(field, i) in form.merge_fields" :key="i" class="border rounded-lg p-3 mb-3 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium">Field {{ i + 1 }}</span>
                        <Button variant="ghost" size="icon" @click="removeMergeField(i)">
                            <Trash2 class="h-4 w-4 text-destructive" />
                        </Button>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="grid gap-1">
                            <Label class="text-xs">{{ t('app.admin.documents.mergeFieldKey') }}</Label>
                            <Input v-model="field.key" placeholder="lease.start_date" size="small" />
                            <InputError :message="form.errors[`merge_fields.${i}.key`]" />
                        </div>
                        <div class="grid gap-1">
                            <Label class="text-xs">{{ t('app.admin.documents.mergeFieldType') }}</Label>
                            <Select v-model="field.type">
                                <SelectTrigger>
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="ft in fieldTypes" :key="ft.value" :value="ft.value">
                                        {{ ft.label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors[`merge_fields.${i}.type`]" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="grid gap-1">
                            <Label class="text-xs">{{ t('app.admin.documents.mergeFieldLabelEn') }}</Label>
                            <Input v-model="field.label_en" dir="ltr" placeholder="Start Date" />
                            <InputError :message="form.errors[`merge_fields.${i}.label_en`]" />
                        </div>
                        <div class="grid gap-1">
                            <Label class="text-xs">{{ t('app.admin.documents.mergeFieldLabelAr') }}</Label>
                            <Input v-model="field.label_ar" dir="rtl" placeholder="تاريخ البدء" />
                            <InputError :message="form.errors[`merge_fields.${i}.label_ar`]" />
                        </div>
                    </div>

                    <div class="grid gap-1">
                        <Label class="text-xs">{{ t('app.admin.documents.mergeFieldSourcePath') }}</Label>
                        <Input v-model="field.source_path" placeholder="lease.start_date" />
                        <InputError :message="form.errors[`merge_fields.${i}.source_path`]" />
                    </div>
                </div>
            </div>

            <Button @click="submitUpdate" :disabled="form.processing" class="w-full">
                Save &amp; Create New Version
            </Button>
        </div>

        <div class="w-64 shrink-0">
            <h3 class="text-sm font-semibold mb-3">{{ t('app.admin.documents.versions') }}</h3>
            <div class="space-y-2">
                <button
                    v-for="v in template.versions"
                    :key="v.id"
                    class="w-full text-left p-3 rounded-lg border text-sm transition-colors"
                    :class="selectedVersion === v.id ? 'border-primary bg-primary/5' : 'border-border hover:bg-muted'"
                    @click="selectedVersion = v.id"
                >
                    <div class="font-medium">v{{ v.version_number }}</div>
                    <div class="text-xs text-muted-foreground mt-1">
                        {{ v.published_at ? new Date(v.published_at).toLocaleDateString() : '—' }}
                    </div>
                    <div v-if="v.creator" class="text-xs text-muted-foreground">
                        {{ v.creator.name }}
                    </div>
                </button>
            </div>

            <div v-if="currentBody" class="mt-6">
                <h4 class="text-sm font-semibold mb-2">Preview v{{ selectedVersion }}</h4>
                <div class="p-3 border rounded-lg bg-muted/30 text-sm whitespace-pre-wrap font-mono max-h-96 overflow-y-auto">
                    {{ currentBody.en || currentBody.ar || '(empty)' }}
                </div>
            </div>

            <div v-if="currentMergeFields.length" class="mt-4">
                <h4 class="text-sm font-semibold mb-2">Merge Fields</h4>
                <div class="space-y-1">
                    <div
                        v-for="(f, i) in currentMergeFields"
                        :key="i"
                        class="text-xs p-2 border rounded flex justify-between"
                    >
                        <code>{{ `{{${f.key}}}` }}</code>
                        <span class="text-muted-foreground">{{ f.label_en || f.key }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
