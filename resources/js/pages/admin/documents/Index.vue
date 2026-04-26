<script setup lang="ts">
import { Head, Link, router, setLayoutProps, useForm, useHttp } from '@inertiajs/vue3';
import { computed, ref, watchEffect } from 'vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useI18n } from '@/composables/useI18n';
import type { PaginationLink } from '@/types';
import { activate, archive, destroy, index, preview, store } from '@/routes/admin/documents';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import InputError from '@/components/InputError.vue';
import { AlertCircle, Eye } from 'lucide-vue-next';

const { t } = useI18n();

type TemplateType = { value: string; label: string };

type Template = {
    id: number;
    name: { en: string; ar: string | null };
    type: string;
    status: string;
    format: string;
    current_version: { id: number; version_number: number; published_at: string } | null;
    created_at: string | null;
};

defineProps<{
    templates: { data: Template[]; links: PaginationLink[] };
    templateTypes: TemplateType[];
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.admin.documents.pageTitle'), href: index.url() },
        ],
    });
});

const sheetOpen = ref(false);

const createForm = useForm({
    name_en: '',
    name_ar: '',
    type: 'lease',
    format: 'in_platform',
    body_en: '',
    body_ar: '',
    merge_fields: [] as { key: string; label_en: string; label_ar: string; type: string; source_path: string }[],
});

function openCreateSheet() {
    createForm.reset();
    createForm.clearErrors();
    sheetOpen.value = true;
}

function submitCreate() {
    createForm.post(store.url(), {
        preserveScroll: true,
        onSuccess: () => {
            sheetOpen.value = false;
        },
    });
}

function confirmActivate(template: Template) {
    if (confirm(t('app.admin.documents.activate') + '?')) {
        router.post(activate.url({ documentTemplate: template.id }));
    }
}

function confirmArchive(template: Template) {
    if (confirm(t('app.admin.documents.archive') + '?')) {
        router.post(archive.url({ documentTemplate: template.id }));
    }
}

function confirmDelete(template: Template) {
    if (confirm('Delete this template?')) {
        router.delete(destroy.url({ documentTemplate: template.id }));
    }
}

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

// --- Preview ---
const previewOpen = ref(false);
const previewLang = ref<'en' | 'ar'>('en');
const previewRendered = ref('');
const previewUnresolved = ref<any[]>([]);
const previewLoading = ref(false);

const previewHasWarnings = computed(() => previewUnresolved.value.length > 0);

async function openPreview(template: Template) {
    previewLoading.value = true;
    previewOpen.value = true;
    previewRendered.value = '';
    previewUnresolved.value = [];

    try {
        const res = await useHttp().post(preview.url({ documentTemplate: template.id }), {
            lang: previewLang.value,
            context: {},
        });
        previewRendered.value = res.rendered;
        previewUnresolved.value = res.unresolved ?? [];
    } catch {
        previewRendered.value = 'Error generating preview.';
    } finally {
        previewLoading.value = false;
    }
}
</script>

<template>
    <Head :title="t('app.admin.documents.pageTitle')" />
    <Heading
        :title="t('app.admin.documents.heading')"
        :description="t('app.admin.documents.description')"
    >
        <Button @click="openCreateSheet">{{ t('app.admin.documents.create') }}</Button>
    </Heading>

    <Table v-if="templates.data.length">
        <TableHeader>
            <TableRow>
                <TableHead>{{ t('app.admin.documents.nameEn') }}</TableHead>
                <TableHead>{{ t('app.admin.documents.type') }}</TableHead>
                <TableHead>{{ t('app.admin.documents.status') }}</TableHead>
                <TableHead>{{ t('app.admin.documents.version') }}</TableHead>
                <TableHead>{{ t('app.admin.documents.format') }}</TableHead>
                <TableHead class="text-right">Actions</TableHead>
            </TableRow>
        </TableHeader>
        <TableBody>
            <TableRow v-for="template in templates.data" :key="template.id">
                <TableCell>
                    <Link
                        :href="`/admin/documents/${template.id}`"
                        class="font-medium hover:underline"
                    >
                        {{ template.name.en }}
                    </Link>
                    <span
                        v-if="template.name.ar"
                        class="text-muted-foreground ml-2 text-sm"
                        dir="rtl"
                        lang="ar"
                    >
                        {{ template.name.ar }}
                    </span>
                </TableCell>
                <TableCell class="capitalize">{{ template.type }}</TableCell>
                <TableCell>
                    <Badge :variant="statusVariant(template.status)">
                        {{ statusLabel(template.status) }}
                    </Badge>
                </TableCell>
                <TableCell>v{{ template.current_version?.version_number ?? 1 }}</TableCell>
                <TableCell class="text-sm text-muted-foreground">
                    {{ template.format === 'in_platform' ? t('app.admin.documents.formatInPlatform') : t('app.admin.documents.formatWordUpload') }}
                </TableCell>
                <TableCell class="text-right">
                    <div class="flex gap-1 justify-end">
                        <Button
                            v-if="template.status === 'draft'"
                            variant="outline"
                            size="sm"
                            @click="confirmActivate(template)"
                        >
                            {{ t('app.admin.documents.activate') }}
                        </Button>
                        <Button
                            v-if="template.status === 'active'"
                            variant="outline"
                            size="sm"
                            @click="confirmArchive(template)"
                        >
                            {{ t('app.admin.documents.archive') }}
                        </Button>
                        <Button variant="ghost" size="icon" @click="openPreview(template)">
                            <Eye class="h-4 w-4" />
                        </Button>
                        <Button variant="ghost" size="sm" @click="confirmDelete(template)">
                            Delete
                        </Button>
                    </div>
                </TableCell>
            </TableRow>
        </TableBody>
    </Table>

    <div v-else class="rounded-lg border border-dashed p-12 text-center">
        <p class="text-muted-foreground">{{ t('app.admin.documents.empty') }}</p>
    </div>

    <Sheet v-model:open="sheetOpen">
        <SheetContent class="overflow-y-auto max-h-screen">
            <SheetHeader>
                <SheetTitle>{{ t('app.admin.documents.create') }}</SheetTitle>
                <SheetDescription>
                    {{ t('app.admin.documents.description') }}
                </SheetDescription>
            </SheetHeader>

            <div class="mt-6 space-y-4">
                <div class="grid gap-2">
                    <Label for="name_en">{{ t('app.admin.documents.nameEn') }} *</Label>
                    <Input id="name_en" v-model="createForm.name_en" dir="ltr" required />
                    <InputError :message="createForm.errors.name_en" />
                </div>

                <div class="grid gap-2">
                    <Label for="name_ar">{{ t('app.admin.documents.nameAr') }}</Label>
                    <Input id="name_ar" v-model="createForm.name_ar" dir="rtl" />
                    <InputError :message="createForm.errors.name_ar" />
                </div>

                <div class="grid gap-2">
                    <Label>{{ t('app.admin.documents.type') }}</Label>
                    <Select v-model="createForm.type">
                        <SelectTrigger>
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="t in templateTypes" :key="t.value" :value="t.value">
                                {{ t.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="createForm.errors.type" />
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <Button variant="outline" @click="sheetOpen = false">Cancel</Button>
                    <Button @click="submitCreate" :disabled="createForm.processing">
                        {{ t('app.admin.documents.create') }}
                    </Button>
                </div>
            </div>
        </SheetContent>
    </Sheet>

    <Sheet v-model:open="previewOpen">
        <SheetContent class="overflow-y-auto max-h-screen w-[600px] sm:max-w-[600px]">
            <SheetHeader>
                <SheetTitle>Preview</SheetTitle>
                <SheetDescription>Template rendered with sample values</SheetDescription>
            </SheetHeader>

            <div class="mt-4 flex gap-2">
                <Button variant="outline" size="sm" :class="previewLang === 'en' ? 'bg-primary/10' : ''" @click="previewLang = 'en'">
                    English
                </Button>
                <Button variant="outline" size="sm" :class="previewLang === 'ar' ? 'bg-primary/10' : ''" @click="previewLang = 'ar'">
                    العربية
                </Button>
            </div>

            <div v-if="previewHasWarnings" class="mt-4 border border-amber-200 bg-amber-50 rounded-lg p-3 flex gap-2 text-sm text-amber-800">
                <AlertCircle class="h-4 w-4 mt-0.5 shrink-0" />
                <div>
                    <p class="font-medium">Unresolved merge fields</p>
                    <p class="text-xs mt-1">
                        <span v-for="(f, i) in previewUnresolved" :key="i">
                            <code>{{ `{{${f.key}}}` }}</code>{{ i < previewUnresolved.length - 1 ? ', ' : '' }}
                        </span>
                    </p>
                    <p class="text-xs mt-1 text-amber-600">These fields have no data or reference. Proceed or cancel.</p>
                </div>
            </div>

            <div class="mt-6 p-4 border rounded-lg bg-muted/30 min-h-[200px] text-sm whitespace-pre-wrap font-mono" :dir="previewLang === 'ar' ? 'rtl' : 'ltr'">
                <span v-if="previewLoading">Loading...</span>
                <template v-else>{{ previewRendered || '(empty preview)' }}</template>
            </div>
        </SheetContent>
    </Sheet>
</template>
