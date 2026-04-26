<script setup lang="ts">
import { Head, router, setLayoutProps } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import { index as leasesIndex, show as leasesShow } from '@/actions/App/Http/Controllers/Leasing/LeaseController';
import { removeKycDocument as kycRemove, submitForApproval as kycSubmit, uploadKyc } from '@/actions/App/Http/Controllers/Leasing/KycController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();

type DocumentType = {
    id: number;
    name: string;
    name_en: string | null;
    name_ar: string | null;
    metadata: { is_required?: boolean } | null;
};

type UploadedDocument = {
    id: number;
    document_type_id: number;
    is_required: boolean;
    original_file_name: string;
    created_at: string;
};

type LeaseDetail = {
    id: number;
    contract_number: string;
    status: { id: number; name: string; name_en: string | null } | null;
    tenant: { id: number; first_name: string; last_name: string } | null;
};

const props = defineProps<{
    lease: LeaseDetail;
    documentTypes: DocumentType[];
    uploadedDocuments: UploadedDocument[];
    progress: { uploaded: number; total: number };
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.leases.pageTitle'), href: leasesIndex.url() },
            { title: props.lease.contract_number, href: leasesShow.url(props.lease.id) },
            { title: t('app.kyc.title'), href: '#' },
        ],
    });
});

const requiredDocumentTypes = computed(() =>
    props.documentTypes.filter((dt) => dt.metadata?.is_required === true),
);

const optionalDocumentTypes = computed(() =>
    props.documentTypes.filter((dt) => dt.metadata?.is_required !== true),
);

function documentsForType(typeId: number): UploadedDocument[] {
    return props.uploadedDocuments.filter((doc) => doc.document_type_id === typeId);
}

function isUploaded(typeId: number): boolean {
    return documentsForType(typeId).length > 0;
}

function uploadDocument(typeId: number, event: Event): void {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];

    if (! file) {
        return;
    }

    const formData = new FormData();
    formData.append('document_type_id', String(typeId));
    formData.append('file', file);

    router.post(uploadKyc.url(props.lease.id), formData, {
        forceFormData: true,
    });
}

function removeDocument(documentId: number): void {
    if (confirm(t('app.kyc.remove') + '?')) {
        router.delete(kycRemove.url(props.lease.id, documentId));
    }
}

function submitForApproval(): void {
    router.post(kycSubmit.url(props.lease.id));
}

const allRequiredUploaded = computed(() =>
    requiredDocumentTypes.value.every((dt) => isUploaded(dt.id)),
);

const progressPercentage = computed(() => {
    if (props.progress.total === 0) {
        return 100;
    }

    return Math.round((props.progress.uploaded / props.progress.total) * 100);
});
</script>

<template>
    <Head :title="t('app.kyc.pageTitle', { number: lease.contract_number })" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">
                    {{ t('app.kyc.title') }}
                </h2>
                <p class="text-muted-foreground text-sm">
                    {{ lease.contract_number }}
                    <span v-if="lease.tenant">
                        &mdash; {{ lease.tenant.first_name }} {{ lease.tenant.last_name }}
                    </span>
                </p>
            </div>

            <Badge v-if="lease.status" variant="secondary">
                {{ lease.status.name_en ?? lease.status.name }}
            </Badge>
        </div>

        <!-- Progress bar -->
        <div class="space-y-1">
            <p class="text-muted-foreground text-sm">
                {{ t('app.kyc.completion', { n: progress.uploaded, total: progress.total }) }}
            </p>
            <div
                role="progressbar"
                :aria-valuenow="progressPercentage"
                aria-valuemin="0"
                aria-valuemax="100"
                class="bg-muted h-2 w-full overflow-hidden rounded-full"
            >
                <div
                    class="h-full rounded-full bg-primary transition-all"
                    :style="{ width: `${progressPercentage}%` }"
                />
            </div>
        </div>

        <!-- Required Documents -->
        <Card>
            <CardHeader>
                <CardTitle>
                    <h3>{{ t('app.kyc.requiredSection') }}</h3>
                </CardTitle>
            </CardHeader>
            <CardContent>
                <div v-if="requiredDocumentTypes.length === 0" class="text-muted-foreground text-sm">
                    {{ t('app.kyc.noDocumentTypes') }}
                </div>
                <ul v-else class="divide-y">
                    <li
                        v-for="docType in requiredDocumentTypes"
                        :key="docType.id"
                        class="flex items-center justify-between py-3"
                    >
                        <div class="flex items-center gap-3">
                            <span
                                class="text-lg"
                                :class="isUploaded(docType.id) ? 'text-green-600' : 'text-muted-foreground'"
                                aria-hidden="true"
                            >
                                {{ isUploaded(docType.id) ? '✓' : '○' }}
                            </span>
                            <div>
                                <p class="text-sm font-medium">{{ docType.name_en ?? docType.name }}</p>
                                <p v-if="!isUploaded(docType.id)" class="text-destructive text-xs">
                                    ⚠ {{ t('app.leases.create.required') }}
                                </p>
                                <ul v-else>
                                    <li
                                        v-for="doc in documentsForType(docType.id)"
                                        :key="doc.id"
                                        class="text-muted-foreground text-xs"
                                    >
                                        {{ doc.original_file_name }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <label
                                :for="`upload-${docType.id}`"
                                class="inline-flex cursor-pointer items-center rounded-md border px-3 py-1 text-xs font-medium hover:bg-accent"
                                :aria-label="`${t('app.kyc.upload')} ${docType.name_en ?? docType.name}`"
                            >
                                {{ t('app.kyc.upload') }}
                            </label>
                            <input
                                :id="`upload-${docType.id}`"
                                type="file"
                                class="sr-only"
                                accept=".pdf,.jpg,.jpeg,.png,.heic"
                                @change="uploadDocument(docType.id, $event)"
                            />
                            <button
                                v-for="doc in documentsForType(docType.id)"
                                :key="doc.id"
                                type="button"
                                class="text-destructive text-xs hover:underline"
                                @click="removeDocument(doc.id)"
                            >
                                {{ t('app.kyc.remove') }}
                            </button>
                        </div>
                    </li>
                </ul>
            </CardContent>
        </Card>

        <!-- Optional Documents -->
        <Card v-if="optionalDocumentTypes.length > 0">
            <CardHeader>
                <CardTitle>
                    <h3>{{ t('app.kyc.optionalSection') }}</h3>
                </CardTitle>
            </CardHeader>
            <CardContent>
                <ul class="divide-y">
                    <li
                        v-for="docType in optionalDocumentTypes"
                        :key="docType.id"
                        class="flex items-center justify-between py-3"
                    >
                        <div class="flex items-center gap-3">
                            <span
                                class="text-lg"
                                :class="isUploaded(docType.id) ? 'text-green-600' : 'text-muted-foreground'"
                                aria-hidden="true"
                            >
                                {{ isUploaded(docType.id) ? '✓' : '○' }}
                            </span>
                            <div>
                                <p class="text-sm font-medium">{{ docType.name_en ?? docType.name }}</p>
                                <ul v-if="isUploaded(docType.id)">
                                    <li
                                        v-for="doc in documentsForType(docType.id)"
                                        :key="doc.id"
                                        class="text-muted-foreground text-xs"
                                    >
                                        {{ doc.original_file_name }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <label
                                :for="`upload-opt-${docType.id}`"
                                class="inline-flex cursor-pointer items-center rounded-md border px-3 py-1 text-xs font-medium hover:bg-accent"
                                :aria-label="`${t('app.kyc.upload')} ${docType.name_en ?? docType.name}`"
                            >
                                {{ t('app.kyc.upload') }}
                            </label>
                            <input
                                :id="`upload-opt-${docType.id}`"
                                type="file"
                                class="sr-only"
                                accept=".pdf,.jpg,.jpeg,.png,.heic"
                                @change="uploadDocument(docType.id, $event)"
                            />
                            <button
                                v-for="doc in documentsForType(docType.id)"
                                :key="doc.id"
                                type="button"
                                class="text-destructive text-xs hover:underline"
                                @click="removeDocument(doc.id)"
                            >
                                {{ t('app.kyc.remove') }}
                            </button>
                        </div>
                    </li>
                </ul>
            </CardContent>
        </Card>

        <!-- Submit section -->
        <div class="flex flex-col items-end gap-2">
            <div
                v-if="!allRequiredUploaded"
                role="alert"
                class="text-destructive text-sm"
            >
                {{ t('app.kyc.submitBlocked', {
                    n: requiredDocumentTypes.filter((dt) => !isUploaded(dt.id)).length,
                    list: requiredDocumentTypes
                        .filter((dt) => !isUploaded(dt.id))
                        .map((dt) => dt.name_en ?? dt.name)
                        .join(', ')
                }) }}
            </div>

            <Button
                :disabled="!allRequiredUploaded"
                @click="submitForApproval"
            >
                {{ t('app.kyc.submitForApproval') }}
            </Button>
        </div>
    </div>
</template>
