<script setup lang="ts">
import { ref, computed } from 'vue';
import { Upload, FileSpreadsheet, AlertCircle, CheckCircle2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useI18n } from '@/composables/useI18n';
import { upload, validate, execute } from '@/actions/App/Http/Controllers/Properties/UnitImportController';
import { download as downloadTemplate } from '@/actions/App/Http/Controllers/Properties/UnitTemplateController';

const { t } = useI18n();

const props = defineProps<{
    open: boolean;
}>();

const emit = defineEmits<{
    'update:open': [value: boolean];
    'imported': [];
}>();

// ─── Step management ───────────────────────────────────────────────────────
type Step = 1 | 2 | 3 | 4;
const currentStep = ref<Step>(1);

// ─── Step 1: Upload ─────────────────────────────────────────────────────────
const dragOver = ref(false);
const selectedFile = ref<File | null>(null);
const uploadError = ref<string | null>(null);
const isUploading = ref(false);

const importSessionId = ref<number | null>(null);
const detectedHeaders = ref<string[]>([]);
const rowCount = ref(0);
const autoMapping = ref<Record<string, string | null>>({});

function handleDragOver(event: DragEvent) {
    event.preventDefault();
    dragOver.value = true;
}

function handleDragLeave() {
    dragOver.value = false;
}

function handleDrop(event: DragEvent) {
    event.preventDefault();
    dragOver.value = false;
    const file = event.dataTransfer?.files[0];
    if (file) {
        selectFile(file);
    }
}

function handleFileInput(event: Event) {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (file) {
        selectFile(file);
    }
}

function openFileDialog() {
    fileInputRef.value?.click();
}

function handleDropZoneKeydown(event: KeyboardEvent) {
    if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        openFileDialog();
    }
}

const fileInputRef = ref<HTMLInputElement | null>(null);

function selectFile(file: File) {
    uploadError.value = null;
    if (!file.name.endsWith('.xlsx')) {
        uploadError.value = t('app.properties.units.import.errorInvalidFile');
        return;
    }
    if (file.size > 10 * 1024 * 1024) {
        uploadError.value = t('app.properties.units.import.errorFileTooLarge');
        return;
    }
    selectedFile.value = file;
}

async function uploadFile() {
    if (!selectedFile.value) return;

    isUploading.value = true;
    uploadError.value = null;

    const formData = new FormData();
    formData.append('file', selectedFile.value);

    try {
        const response = await fetch(upload.url(), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
            },
            body: formData,
        });

        if (!response.ok) {
            const error = await response.json().catch(() => ({}));
            uploadError.value = error.message ?? 'Upload failed';
            return;
        }

        const data = await response.json();
        importSessionId.value = data.import_session_id;
        detectedHeaders.value = data.headers ?? [];
        rowCount.value = data.row_count ?? 0;
        autoMapping.value = data.auto_mapping ?? {};

        // Initialize userMapping from auto-detected mapping
        Object.keys(autoMapping.value).forEach((field) => {
            userMapping.value[field] = autoMapping.value[field] ?? '';
        });

        currentStep.value = 2;
    } catch {
        uploadError.value = 'An error occurred during upload.';
    } finally {
        isUploading.value = false;
    }
}

// ─── Step 2: Column Mapping ──────────────────────────────────────────────────
const systemFieldLabels = computed<Record<string, string>>(() => ({
    name: t('app.properties.units.import.fieldName'),
    rf_community_id: t('app.properties.units.import.fieldCommunity'),
    rf_building_id: t('app.properties.units.import.fieldBuilding'),
    net_area: t('app.properties.units.import.fieldNetArea'),
    status: t('app.properties.units.import.fieldStatus'),
}));

const userMapping = ref<Record<string, string>>({});
const isMappingValidating = ref(false);

async function proceedToValidation() {
    if (!importSessionId.value) return;

    isMappingValidating.value = true;

    try {
        const response = await fetch(validate.url(), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
            },
            body: JSON.stringify({
                import_session_id: importSessionId.value,
                mapping: userMapping.value,
            }),
        });

        if (!response.ok) {
            return;
        }

        const data = await response.json();
        validationResult.value = data;
        currentStep.value = 3;
    } finally {
        isMappingValidating.value = false;
    }
}

// ─── Step 3: Validation Preview ──────────────────────────────────────────────
interface ValidationError {
    row: number;
    field: string;
    message: string;
}

interface ValidationResult {
    total_rows: number;
    valid_count: number;
    error_count: number;
    errors: ValidationError[];
}

const validationResult = ref<ValidationResult | null>(null);

const isAllInvalid = computed(() =>
    validationResult.value !== null &&
    validationResult.value.valid_count === 0 &&
    validationResult.value.total_rows > 0
);

// ─── Step 4: Import ──────────────────────────────────────────────────────────
const isImporting = ref(false);
const importProgress = ref(0);
const importResult = ref<{ success_count: number; error_count: number; status: string } | null>(null);

async function executeImport() {
    if (!importSessionId.value || !validationResult.value) return;

    isImporting.value = true;
    currentStep.value = 4;

    try {
        const response = await fetch(execute.url(), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
            },
            body: JSON.stringify({
                import_session_id: importSessionId.value,
                mapping: userMapping.value,
                import_valid_only: true,
            }),
        });

        if (!response.ok) {
            return;
        }

        const data = await response.json();
        importResult.value = data;
        importProgress.value = 100;

        if (data.status === 'completed') {
            emit('imported');
        }
    } finally {
        isImporting.value = false;
    }
}

// ─── Reset / Close ───────────────────────────────────────────────────────────
function close() {
    emit('update:open', false);
}

function resetAndClose() {
    selectedFile.value = null;
    uploadError.value = null;
    importSessionId.value = null;
    detectedHeaders.value = [];
    rowCount.value = 0;
    autoMapping.value = {};
    userMapping.value = {};
    validationResult.value = null;
    importResult.value = null;
    importProgress.value = 0;
    currentStep.value = 1;
    close();
}

// When dialog closes, reset state
function handleOpenChange(value: boolean) {
    if (!value) {
        resetAndClose();
    } else {
        emit('update:open', value);
    }
}
</script>

<template>
    <Dialog :open="props.open" @update:open="handleOpenChange">
        <DialogContent class="max-w-2xl">
            <DialogHeader>
                <DialogTitle>{{ t('app.properties.units.import.modalTitle') }}</DialogTitle>
            </DialogHeader>

            <!-- Step indicator -->
            <ol class="flex items-center gap-2 text-sm" aria-label="Import steps">
                <li
                    v-for="(label, idx) in [
                        t('app.properties.units.import.step1'),
                        t('app.properties.units.import.step2'),
                        t('app.properties.units.import.step3'),
                        t('app.properties.units.import.step4'),
                    ]"
                    :key="idx"
                    :aria-current="currentStep === idx + 1 ? 'step' : undefined"
                    class="flex items-center gap-1"
                >
                    <span
                        :class="[
                            'flex h-6 w-6 items-center justify-center rounded-full text-xs font-semibold',
                            currentStep === idx + 1
                                ? 'bg-primary text-primary-foreground'
                                : currentStep > idx + 1
                                  ? 'bg-green-500 text-white'
                                  : 'bg-muted text-muted-foreground',
                        ]"
                    >
                        {{ idx + 1 }}
                    </span>
                    <span :class="currentStep === idx + 1 ? 'font-medium' : 'text-muted-foreground'">
                        {{ label }}
                    </span>
                    <span v-if="idx < 3" class="text-muted-foreground">›</span>
                </li>
            </ol>

            <!-- Step 1: Upload -->
            <div v-if="currentStep === 1" class="flex flex-col gap-4">
                <a
                    :href="downloadTemplate.url()"
                    class="flex w-fit items-center gap-1 text-sm text-primary underline-offset-4 hover:underline"
                >
                    <FileSpreadsheet class="h-4 w-4" />
                    {{ t('app.properties.units.import.downloadTemplate') }}
                </a>

                <!-- Drop zone -->
                <div
                    :class="[
                        'flex flex-col items-center justify-center rounded-lg border-2 border-dashed p-10 transition-colors',
                        dragOver ? 'border-primary bg-primary/5' : 'border-muted-foreground/30',
                        uploadError ? 'border-destructive' : '',
                    ]"
                    role="button"
                    tabindex="0"
                    :aria-label="t('app.properties.units.import.dropZoneText')"
                    @click="openFileDialog"
                    @keydown="handleDropZoneKeydown"
                    @dragover="handleDragOver"
                    @dragleave="handleDragLeave"
                    @drop="handleDrop"
                >
                    <Upload class="mb-3 h-10 w-10 text-muted-foreground" />
                    <p class="text-center text-sm text-muted-foreground">
                        {{ selectedFile ? selectedFile.name : t('app.properties.units.import.dropZoneText') }}
                    </p>
                    <p class="mt-1 text-center text-xs text-muted-foreground">
                        {{ t('app.properties.units.import.acceptedFormat') }}
                    </p>
                    <input
                        ref="fileInputRef"
                        type="file"
                        accept=".xlsx"
                        class="hidden"
                        @change="handleFileInput"
                    />
                </div>

                <p v-if="uploadError" role="alert" class="flex items-center gap-1 text-sm text-destructive">
                    <AlertCircle class="h-4 w-4" />
                    {{ uploadError }}
                </p>

                <div class="flex justify-end gap-2">
                    <Button variant="outline" @click="close">{{ t('app.actions.cancel') }}</Button>
                    <Button :disabled="!selectedFile || isUploading" @click="uploadFile">
                        {{ isUploading ? '...' : t('app.actions.next') }}
                    </Button>
                </div>
            </div>

            <!-- Step 2: Column Mapping -->
            <div v-else-if="currentStep === 2" class="flex flex-col gap-4">
                <p class="text-sm text-muted-foreground">
                    {{ selectedFile?.name }} · {{ t('app.properties.units.import.rowsDetected', { n: rowCount }) }}
                </p>

                <div class="rounded-md border">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-muted/50">
                                <th class="p-3 text-start font-medium">
                                    {{ t('app.properties.units.import.systemField') }}
                                </th>
                                <th class="p-3 text-start font-medium">
                                    {{ t('app.properties.units.import.uploadedColumn') }}
                                </th>
                                <th class="p-3 text-start font-medium"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(label, field) in systemFieldLabels"
                                :key="field"
                                class="border-b last:border-0"
                            >
                                <td class="p-3 font-medium">{{ label }}</td>
                                <td class="p-3">
                                    <Select v-model="userMapping[field]">
                                        <SelectTrigger class="w-48">
                                            <SelectValue
                                                :placeholder="t('app.properties.units.import.selectColumn')"
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="">
                                                {{ t('app.properties.units.import.selectColumn') }}
                                            </SelectItem>
                                            <SelectItem
                                                v-for="header in detectedHeaders"
                                                :key="header"
                                                :value="header"
                                            >
                                                {{ header }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </td>
                                <td class="p-3">
                                    <span
                                        :class="[
                                            'rounded-full px-2 py-0.5 text-xs font-medium',
                                            userMapping[field]
                                                ? 'bg-green-100 text-green-700'
                                                : 'bg-amber-100 text-amber-700',
                                        ]"
                                    >
                                        {{
                                            userMapping[field]
                                                ? t('app.properties.units.import.matched')
                                                : t('app.properties.units.import.unmatched')
                                        }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-between gap-2">
                    <Button variant="outline" @click="currentStep = 1">{{ t('app.actions.back') }}</Button>
                    <Button :disabled="isMappingValidating" @click="proceedToValidation">
                        {{ isMappingValidating ? '...' : t('app.actions.next') }}
                    </Button>
                </div>
            </div>

            <!-- Step 3: Validation Preview -->
            <div v-else-if="currentStep === 3 && validationResult" class="flex flex-col gap-4">
                <!-- Summary badges -->
                <div class="flex gap-3">
                    <div class="flex items-center gap-1 rounded-md bg-green-50 px-3 py-1.5 text-sm font-medium text-green-700">
                        <CheckCircle2 class="h-4 w-4" />
                        {{ t('app.properties.units.import.validRows', { n: validationResult.valid_count }) }}
                    </div>
                    <div class="flex items-center gap-1 rounded-md bg-red-50 px-3 py-1.5 text-sm font-medium text-red-700">
                        <AlertCircle class="h-4 w-4" />
                        {{ t('app.properties.units.import.errorRows', { n: validationResult.error_count }) }}
                    </div>
                </div>

                <!-- All invalid -->
                <p v-if="isAllInvalid" role="alert" class="text-sm text-destructive">
                    {{ t('app.properties.units.import.allRowsInvalid', { n: validationResult.total_rows }) }}
                </p>

                <!-- No data rows -->
                <p v-if="validationResult.total_rows === 0" class="text-sm text-muted-foreground">
                    {{ t('app.properties.units.import.noDataRows') }}
                </p>

                <!-- Error table -->
                <div v-if="validationResult.errors.length > 0" class="max-h-60 overflow-y-auto rounded-md border">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 bg-background">
                            <tr class="border-b">
                                <th class="p-2 text-start font-medium">
                                    {{ t('app.properties.units.import.errorRow') }}
                                </th>
                                <th class="p-2 text-start font-medium">
                                    {{ t('app.properties.units.import.errorField') }}
                                </th>
                                <th class="p-2 text-start font-medium">
                                    {{ t('app.properties.units.import.errorMessage') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(err, i) in validationResult.errors"
                                :key="i"
                                class="border-b last:border-0"
                                :role="i === 0 ? 'alert' : undefined"
                            >
                                <td class="p-2">#{{ err.row }}</td>
                                <td class="p-2 text-muted-foreground">{{ err.field }}</td>
                                <td class="p-2 text-destructive">{{ err.message }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-between gap-2">
                    <Button variant="outline" @click="currentStep = 2">{{ t('app.actions.back') }}</Button>
                    <div class="flex gap-2">
                        <Button variant="outline" @click="resetAndClose">
                            {{ t('app.properties.units.import.cancelAndFix') }}
                        </Button>
                        <Button :disabled="isAllInvalid || validationResult.total_rows === 0" @click="executeImport">
                            {{ t('app.properties.units.import.importValidOnly', { n: validationResult.valid_count }) }}
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Step 4: Import Progress -->
            <div v-else-if="currentStep === 4" class="flex flex-col gap-4">
                <div v-if="!importResult">
                    <p class="mb-3 text-sm font-medium">
                        {{ t('app.properties.units.import.progressLabel', { n: validationResult?.valid_count ?? 0 }) }}
                    </p>
                    <div
                        class="h-3 w-full overflow-hidden rounded-full bg-muted"
                        role="progressbar"
                        :aria-valuenow="importProgress"
                        aria-valuemin="0"
                        aria-valuemax="100"
                    >
                        <div
                            class="h-full bg-primary transition-all"
                            :style="{ width: `${importProgress}%` }"
                        />
                    </div>
                    <p class="mt-2 text-xs text-muted-foreground">
                        {{ t('app.properties.units.import.progressNote') }}
                    </p>
                </div>

                <div v-else class="flex flex-col gap-3">
                    <div v-if="importResult.status === 'queued'" class="rounded-md bg-blue-50 p-4 text-sm text-blue-700">
                        {{ t('app.properties.units.import.queuedMessage') }}
                    </div>
                    <div v-else class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                        {{
                            t('app.properties.units.import.completionToast', {
                                n: importResult.success_count,
                                e: importResult.error_count,
                            })
                        }}
                    </div>
                    <div class="flex justify-end">
                        <Button @click="resetAndClose">{{ t('app.actions.close') }}</Button>
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
