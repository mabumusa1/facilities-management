<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { dashboard } from '@/routes';
import { index as documentsIndex } from '@/routes/documents';
import { store as storeExcelSheet, land as storeLandExcelSheet, leads as storeLeadsExcelSheet } from '@/routes/rf/excel-sheets';
import { errors as leadsImportErrors } from '@/routes/rf/excel-sheets/leads';
import { destroy as destroyFile, store as storeFile } from '@/routes/rf/files';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

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

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Documents', href: documentsIndex() },
        ],
    },
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
    <Head title="Documents" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" title="Documents Center" description="Upload files, run imports, and manage document artifacts." />

        <div class="grid gap-4 lg:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle>Upload Document</CardTitle>
                </CardHeader>
                <CardContent>
                    <form class="space-y-3" @submit.prevent="uploadFile">
                        <div class="grid gap-2">
                            <Label for="document_file">File</Label>
                            <Input id="document_file" type="file" @change="(event) => fileForm.image = ((event.target as HTMLInputElement).files?.[0] ?? null)" />
                            <InputError :message="fileForm.errors.image" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="document_collection">Collection</Label>
                            <Input id="document_collection" v-model="fileForm.collection" placeholder="documents" />
                            <InputError :message="fileForm.errors.collection" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="document_notes">Notes</Label>
                            <Input id="document_notes" v-model="fileForm.notes" placeholder="Optional notes" />
                            <InputError :message="fileForm.errors.notes" />
                        </div>
                        <Button :disabled="fileForm.processing">Upload File</Button>
                    </form>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Imports</CardTitle>
                </CardHeader>
                <CardContent class="space-y-6">
                    <form class="space-y-3" @submit.prevent="uploadExcel">
                        <p class="text-sm font-medium">General Excel Import</p>
                        <div class="grid gap-2">
                            <Label>Community</Label>
                            <select v-model="excelForm.rf_community_id" class="rounded-md border border-input bg-background px-3 py-2 text-sm">
                                <option value="">Select community</option>
                                <option v-for="community in props.communities" :key="community.id" :value="String(community.id)">
                                    {{ community.name }}
                                </option>
                            </select>
                            <InputError :message="excelForm.errors.rf_community_id" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Excel File</Label>
                            <Input type="file" @change="(event) => excelForm.file = ((event.target as HTMLInputElement).files?.[0] ?? null)" />
                            <InputError :message="excelForm.errors.file" />
                        </div>
                        <Button :disabled="excelForm.processing">Upload Excel</Button>
                    </form>

                    <form class="space-y-3" @submit.prevent="uploadLandExcel">
                        <p class="text-sm font-medium">Land Import</p>
                        <div class="grid gap-2">
                            <Label>Community</Label>
                            <select v-model="landForm.rf_community_id" class="rounded-md border border-input bg-background px-3 py-2 text-sm">
                                <option value="">Select community</option>
                                <option v-for="community in props.communities" :key="community.id" :value="String(community.id)">
                                    {{ community.name }}
                                </option>
                            </select>
                            <InputError :message="landForm.errors.rf_community_id" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Excel File</Label>
                            <Input type="file" @change="(event) => landForm.file = ((event.target as HTMLInputElement).files?.[0] ?? null)" />
                            <InputError :message="landForm.errors.file" />
                        </div>
                        <Button :disabled="landForm.processing">Upload Land File</Button>
                    </form>

                    <form class="space-y-3" @submit.prevent="uploadLeadsExcel">
                        <p class="text-sm font-medium">Leads Import</p>
                        <div class="grid gap-2">
                            <Label>Excel File</Label>
                            <Input type="file" @change="(event) => leadsForm.file = ((event.target as HTMLInputElement).files?.[0] ?? null)" />
                            <InputError :message="leadsForm.errors.file" />
                        </div>
                        <div class="flex items-center gap-2">
                            <Button :disabled="leadsForm.processing">Upload Leads File</Button>
                            <Button variant="outline" as-child>
                                <Link :href="leadsImportErrors().url">View Leads Import Errors</Link>
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Uploaded Files</CardTitle>
            </CardHeader>
            <CardContent class="space-y-2">
                <div v-for="media in props.mediaFiles" :key="media.id" class="flex items-center justify-between gap-3 rounded-md border p-3 text-sm">
                    <div class="space-y-1">
                        <a :href="media.url" class="font-medium underline" target="_blank" rel="noreferrer">{{ media.name }}</a>
                        <p class="text-muted-foreground">Collection: {{ media.collection }} {{ media.notes ? `• ${media.notes}` : '' }}</p>
                    </div>
                    <Button variant="destructive" size="sm" @click="deleteFile(media.id)">Delete</Button>
                </div>
                <p v-if="props.mediaFiles.length === 0" class="text-muted-foreground text-sm">No uploaded files yet.</p>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Recent Imports</CardTitle>
            </CardHeader>
            <CardContent class="space-y-2">
                <div v-for="importItem in props.excelImports" :key="importItem.id" class="flex items-center justify-between gap-3 rounded-md border p-3 text-sm">
                    <div class="space-y-1">
                        <p class="font-medium">{{ importItem.file_name ?? `Import #${importItem.id}` }}</p>
                        <p class="text-muted-foreground">Type: {{ importItem.type }} - Community: {{ importItem.rf_community_id ?? 'N/A' }}</p>
                    </div>
                    <Badge variant="secondary">{{ importItem.status }}</Badge>
                </div>
                <p v-if="props.excelImports.length === 0" class="text-muted-foreground text-sm">No imports yet.</p>
            </CardContent>
        </Card>
    </div>
</template>
