<script setup lang="ts">
import { Head, Link, router, setLayoutProps, useForm } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { dashboard } from '@/routes';
import { index as documentsIndex } from '@/routes/documents';
import { store as storeExcelSheet, land as storeLandExcelSheet, leads as storeLeadsExcelSheet } from '@/routes/rf/excel-sheets';
import { errors as leadsImportErrors } from '@/routes/rf/excel-sheets/leads';
import { destroy as destroyFile, store as storeFile } from '@/routes/rf/files';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const { t } = useI18n();

type Community = {
    id: number;
    name: string;
};

type MediaFile = {
    id: number;
    url: string;
    name: string;
    notes: string | null;
    collection: string;
    created_at: string;
};

type ExcelImport = {
    id: number;
    type: string;
    file_name: string | null;
    file_path: string;
    status: string;
    rf_community_id: number | null;
    created_at: string;
};

const props = defineProps<{
    communities: Community[];
    mediaFiles: MediaFile[];
    excelImports: ExcelImport[];
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: dashboard() },
            { title: t('app.navigation.documentation'), href: documentsIndex() },
        ],
    });
});

const fileForm = useForm({
    image: null as File | null,
    collection: 'documents',
    notes: '',
});

const excelForm = useForm({
    file: null as File | null,
    rf_community_id: '',
});

const landForm = useForm({
    file: null as File | null,
    rf_community_id: '',
});

const leadsForm = useForm({
    file: null as File | null,
});

function uploadFile() {
    fileForm.post(storeFile().url, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            fileForm.reset();
            router.reload({ only: ['mediaFiles'] });
        },
    });
}

function deleteFile(id: number) {
    router.delete(destroyFile(id).url, {
        preserveScroll: true,
        onSuccess: () => {
            router.reload({ only: ['mediaFiles'] });
        },
    });
}

function uploadExcel() {
    excelForm.post(storeExcelSheet().url, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            excelForm.reset();
            router.reload({ only: ['excelImports'] });
        },
    });
}

function uploadLandExcel() {
    landForm.post(storeLandExcelSheet().url, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            landForm.reset();
            router.reload({ only: ['excelImports'] });
        },
    });
}

function uploadLeadsExcel() {
    leadsForm.post(storeLeadsExcelSheet().url, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            leadsForm.reset();
            router.reload({ only: ['excelImports'] });
        },
    });
}
</script>

<template>
    <Head :title="t('app.documents.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            variant="small"
            :title="t('app.documents.heading')"
            :description="t('app.documents.description')"
        />

        <div class="grid gap-4 lg:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('app.documents.uploadDocument') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <form class="space-y-3" @submit.prevent="uploadFile">
                        <div class="grid gap-2">
                            <Label for="document_file">{{ t('app.documents.file') }}</Label>
                            <Input id="document_file" type="file" @change="fileForm.image = (($event.target as HTMLInputElement).files?.[0] ?? null)" />
                            <InputError :message="fileForm.errors.image" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="document_collection">{{ t('app.documents.collection') }}</Label>
                            <Input id="document_collection" v-model="fileForm.collection" :placeholder="t('app.documents.collectionPlaceholder')" />
                            <InputError :message="fileForm.errors.collection" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="document_notes">{{ t('app.documents.notes') }}</Label>
                            <Input id="document_notes" v-model="fileForm.notes" :placeholder="t('app.documents.optionalNotes')" />
                            <InputError :message="fileForm.errors.notes" />
                        </div>
                        <Button :disabled="fileForm.processing">{{ t('app.documents.uploadFile') }}</Button>
                    </form>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>{{ t('app.documents.imports') }}</CardTitle>
                </CardHeader>
                <CardContent class="space-y-6">
                    <form class="space-y-3" @submit.prevent="uploadExcel">
                        <p class="text-sm font-medium">{{ t('app.documents.generalExcelImport') }}</p>
                        <div class="grid gap-2">
                            <Label>{{ t('app.documents.community') }}</Label>
                            <select v-model="excelForm.rf_community_id" class="rounded-md border border-input bg-background px-3 py-2 text-sm">
                                <option value="">{{ t('app.documents.selectCommunity') }}</option>
                                <option v-for="community in props.communities" :key="community.id" :value="String(community.id)">
                                    {{ community.name }}
                                </option>
                            </select>
                            <InputError :message="excelForm.errors.rf_community_id" />
                        </div>
                        <div class="grid gap-2">
                            <Label>{{ t('app.documents.excelFile') }}</Label>
                            <Input type="file" @change="excelForm.file = (($event.target as HTMLInputElement).files?.[0] ?? null)" />
                            <InputError :message="excelForm.errors.file" />
                        </div>
                        <Button :disabled="excelForm.processing">{{ t('app.documents.uploadExcel') }}</Button>
                    </form>

                    <form class="space-y-3" @submit.prevent="uploadLandExcel">
                        <p class="text-sm font-medium">{{ t('app.documents.landImport') }}</p>
                        <div class="grid gap-2">
                            <Label>{{ t('app.documents.community') }}</Label>
                            <select v-model="landForm.rf_community_id" class="rounded-md border border-input bg-background px-3 py-2 text-sm">
                                <option value="">{{ t('app.documents.selectCommunity') }}</option>
                                <option v-for="community in props.communities" :key="community.id" :value="String(community.id)">
                                    {{ community.name }}
                                </option>
                            </select>
                            <InputError :message="landForm.errors.rf_community_id" />
                        </div>
                        <div class="grid gap-2">
                            <Label>{{ t('app.documents.excelFile') }}</Label>
                            <Input type="file" @change="landForm.file = (($event.target as HTMLInputElement).files?.[0] ?? null)" />
                            <InputError :message="landForm.errors.file" />
                        </div>
                        <Button :disabled="landForm.processing">{{ t('app.documents.uploadLandFile') }}</Button>
                    </form>

                    <form class="space-y-3" @submit.prevent="uploadLeadsExcel">
                        <p class="text-sm font-medium">{{ t('app.documents.leadsImport') }}</p>
                        <div class="grid gap-2">
                            <Label>{{ t('app.documents.excelFile') }}</Label>
                            <Input type="file" @change="leadsForm.file = (($event.target as HTMLInputElement).files?.[0] ?? null)" />
                            <InputError :message="leadsForm.errors.file" />
                        </div>
                        <div class="flex items-center gap-2">
                            <Button :disabled="leadsForm.processing">{{ t('app.documents.uploadLeadsFile') }}</Button>
                            <Button variant="outline" as-child>
                                <Link :href="leadsImportErrors().url">{{ t('app.documents.viewLeadsImportErrors') }}</Link>
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.documents.uploadedFiles') }}</CardTitle>
            </CardHeader>
            <CardContent class="space-y-2">
                <div v-for="media in props.mediaFiles" :key="media.id" class="flex items-center justify-between gap-3 rounded-md border p-3 text-sm">
                    <div class="space-y-1">
                        <a :href="media.url" class="font-medium underline" target="_blank" rel="noreferrer">{{ media.name }}</a>
                        <p class="text-muted-foreground">{{ t('app.documents.collection') }}: {{ media.collection }} {{ media.notes ? `• ${media.notes}` : '' }}</p>
                    </div>
                    <Button variant="destructive" size="sm" @click="deleteFile(media.id)">{{ t('app.actions.delete') }}</Button>
                </div>
                <p v-if="props.mediaFiles.length === 0" class="text-muted-foreground text-sm">{{ t('app.documents.noUploadedFiles') }}</p>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.documents.recentImports') }}</CardTitle>
            </CardHeader>
            <CardContent class="space-y-2">
                <div v-for="importItem in props.excelImports" :key="importItem.id" class="flex items-center justify-between gap-3 rounded-md border p-3 text-sm">
                    <div class="space-y-1">
                        <p class="font-medium">{{ importItem.file_name ?? t('app.documents.importFallback', { id: importItem.id }) }}</p>
                        <p class="text-muted-foreground">{{ t('app.documents.type') }}: {{ importItem.type }} - {{ t('app.documents.community') }}: {{ importItem.rf_community_id ?? t('app.common.notAvailable') }}</p>
                    </div>
                    <Badge variant="secondary">{{ importItem.status }}</Badge>
                </div>
                <p v-if="props.excelImports.length === 0" class="text-muted-foreground text-sm">{{ t('app.documents.noImports') }}</p>
            </CardContent>
        </Card>
    </div>
</template>
