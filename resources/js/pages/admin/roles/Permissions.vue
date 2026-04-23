<script setup lang="ts">
import { Head, Link, setLayoutProps } from '@inertiajs/vue3';
import { computed, ref, watch, watchEffect } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { useI18n } from '@/composables/useI18n';
import { index as rolesIndex } from '@/routes/admin/roles';
import { syncPermissions } from '@/actions/App/Http/Controllers/Admin/RoleController';

interface Role {
    id: number;
    name_en: string;
    name_ar: string;
    type: string;
    is_system: boolean;
}

interface Preset {
    label: string;
    permissions: string[];
}

const props = defineProps<{
    role: Role;
    subjects: string[];
    actions: string[];
    presets: Preset[];
    permissions: string[] | null;
}>();

const { t, isArabic } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.admin.roles.breadcrumb'), href: rolesIndex.url() },
            { title: t('app.admin.roles.permissions.tab'), href: '#' },
        ],
    });
});

// Matrix state: subject -> action -> boolean
type Matrix = Record<string, Record<string, boolean>>;

function buildMatrixFromPermissions(permissionNames: string[]): Matrix {
    const matrix: Matrix = {};
    for (const subject of props.subjects) {
        matrix[subject] = {};
        for (const action of props.actions) {
            matrix[subject][action] = permissionNames.includes(`${subject}.${action}`);
        }
    }
    return matrix;
}

const matrix = ref<Matrix>({});
const initialSnapshot = ref<string>('');
const isSaving = ref(false);
const saveError = ref<string | null>(null);

// Build matrix once permissions prop loads (deferred)
watch(
    () => props.permissions,
    (perms) => {
        if (perms !== null) {
            matrix.value = buildMatrixFromPermissions(perms);
            initialSnapshot.value = JSON.stringify(matrix.value);
        }
    },
    { immediate: true },
);

const isDirty = computed(() => {
    if (props.permissions === null) {
        return false;
    }
    return JSON.stringify(matrix.value) !== initialSnapshot.value;
});

function nonViewActions(): string[] {
    return props.actions.filter((a) => a !== 'VIEW');
}

function toggleCell(subject: string, action: string, value: boolean): void {
    if (props.role.is_system) {
        return;
    }

    matrix.value[subject][action] = value;

    // Auto-check VIEW if any other action is enabled
    if (action !== 'VIEW' && value) {
        matrix.value[subject]['VIEW'] = true;
    }

    // Uncheck all other actions if VIEW is unchecked
    if (action === 'VIEW' && ! value) {
        for (const a of nonViewActions()) {
            matrix.value[subject][a] = false;
        }
    }
}

function toggleRow(subject: string, value: boolean): void {
    if (props.role.is_system) {
        return;
    }
    for (const action of props.actions) {
        matrix.value[subject][action] = value;
    }
    // If enabling, VIEW should be on. If disabling all, uncheck VIEW too (already done above).
}

function toggleColumn(action: string, value: boolean): void {
    if (props.role.is_system) {
        return;
    }
    for (const subject of props.subjects) {
        matrix.value[subject][action] = value;
        // Auto-check VIEW when enabling a non-VIEW action
        if (action !== 'VIEW' && value) {
            matrix.value[subject]['VIEW'] = true;
        }
        // Uncheck all other actions when disabling VIEW for the entire column
        if (action === 'VIEW' && ! value) {
            for (const a of nonViewActions()) {
                matrix.value[subject][a] = false;
            }
        }
    }
}

function rowState(subject: string): 'all' | 'none' | 'indeterminate' {
    const cells = props.actions.map((a) => matrix.value[subject]?.[a] ?? false);
    const checkedCount = cells.filter(Boolean).length;
    if (checkedCount === 0) {
        return 'none';
    }
    if (checkedCount === props.actions.length) {
        return 'all';
    }
    return 'indeterminate';
}

function columnState(action: string): 'all' | 'none' | 'indeterminate' {
    const cells = props.subjects.map((s) => matrix.value[s]?.[action] ?? false);
    const checkedCount = cells.filter(Boolean).length;
    if (checkedCount === 0) {
        return 'none';
    }
    if (checkedCount === props.subjects.length) {
        return 'all';
    }
    return 'indeterminate';
}

function allState(): 'all' | 'none' | 'indeterminate' {
    let total = 0;
    let checked = 0;
    for (const subject of props.subjects) {
        for (const action of props.actions) {
            total++;
            if (matrix.value[subject]?.[action]) {
                checked++;
            }
        }
    }
    if (checked === 0) {
        return 'none';
    }
    if (checked === total) {
        return 'all';
    }
    return 'indeterminate';
}

function toggleAll(value: boolean): void {
    if (props.role.is_system) {
        return;
    }
    for (const subject of props.subjects) {
        for (const action of props.actions) {
            matrix.value[subject][action] = value;
        }
    }
}

function applyPreset(label: string): void {
    const preset = props.presets.find((p) => p.label === label);
    if (! preset) {
        return;
    }
    matrix.value = buildMatrixFromPermissions(preset.permissions);
}

function buildEnabledPermissions(): string[] {
    const enabled: string[] = [];
    for (const subject of props.subjects) {
        for (const action of props.actions) {
            if (matrix.value[subject]?.[action]) {
                enabled.push(`${subject}.${action}`);
            }
        }
    }
    return enabled;
}

async function save(): Promise<void> {
    if (! isDirty.value || isSaving.value) {
        return;
    }

    isSaving.value = true;
    saveError.value = null;

    const route = syncPermissions(props.role.id);

    try {
        const response = await fetch(route.url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCsrfToken(),
                Accept: 'application/json',
            },
            body: JSON.stringify({ permissions: buildEnabledPermissions() }),
        });

        if (! response.ok) {
            const data = await response.json().catch(() => ({}));
            saveError.value = (data as { message?: string }).message ?? t('app.admin.roles.permissions.saveError');
            return;
        }

        initialSnapshot.value = JSON.stringify(matrix.value);
    } catch {
        saveError.value = t('app.admin.roles.permissions.saveError');
    } finally {
        isSaving.value = false;
    }
}

function cancel(): void {
    if (props.permissions !== null) {
        matrix.value = buildMatrixFromPermissions(props.permissions);
        initialSnapshot.value = JSON.stringify(matrix.value);
    }
    saveError.value = null;
}

function getCsrfToken(): string {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
}

function subjectLabel(subject: string): string {
    return t(`app.admin.roles.permissions.subjects.${subject}`, undefined, subject);
}

function actionLabel(action: string): string {
    return t(`app.admin.roles.permissions.actions.${action}`, undefined, action);
}

// Warn on unsaved changes
function handleBeforeUnload(e: BeforeUnloadEvent): void {
    if (isDirty.value) {
        e.preventDefault();
    }
}

watchEffect((onCleanup) => {
    window.addEventListener('beforeunload', handleBeforeUnload);
    onCleanup(() => window.removeEventListener('beforeunload', handleBeforeUnload));
});

const selectedPreset = ref<string>('');
</script>

<template>
    <div>
        <Head :title="t('app.admin.roles.permissions.pageTitle', { name: role.name_en })" />

        <!-- Page header -->
        <div class="mb-6 flex items-start justify-between gap-4">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-semibold">
                        {{ isArabic ? role.name_ar : role.name_en }}
                    </h1>
                    <Badge v-if="role.is_system" variant="secondary">{{ t('app.admin.roles.systemBadge') }}</Badge>
                </div>
                <div class="text-sm text-muted-foreground">
                    {{ isArabic ? role.name_ar : role.name_en }}
                    <span v-if="role.name_ar && ! isArabic" class="ms-2" dir="rtl" lang="ar">{{ role.name_ar }}</span>
                </div>
            </div>
            <Link :href="rolesIndex.url()">
                <Button variant="outline" size="sm">← {{ t('app.admin.roles.breadcrumb') }}</Button>
            </Link>
        </div>

        <!-- System role banner -->
        <div
            v-if="role.is_system"
            role="status"
            class="mb-4 flex items-center gap-3 rounded-md border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800 dark:border-blue-800 dark:bg-blue-950 dark:text-blue-200"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ t('app.admin.roles.permissions.systemBanner') }}
        </div>

        <!-- Preset selector -->
        <div v-if="! role.is_system" class="mb-4 flex items-center gap-3">
            <span class="text-sm font-medium">{{ t('app.admin.roles.permissions.applyPreset') }}</span>
            <Select v-model="selectedPreset" @update:model-value="applyPreset">
                <SelectTrigger class="w-56">
                    <SelectValue :placeholder="t('app.admin.roles.permissions.presetPlaceholder')" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="preset in presets" :key="preset.label" :value="preset.label">
                        {{ preset.label }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <TooltipProvider>
                <Tooltip>
                    <TooltipTrigger as-child>
                        <button type="button" class="inline-flex items-center text-muted-foreground" :aria-label="t('app.admin.roles.permissions.presetTooltip')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    </TooltipTrigger>
                    <TooltipContent class="max-w-xs">{{ t('app.admin.roles.permissions.presetTooltip') }}</TooltipContent>
                </Tooltip>
            </TooltipProvider>
        </div>

        <!-- Permission matrix -->
        <div class="overflow-x-auto rounded-md border">
            <!-- Loading skeleton -->
            <Table v-if="permissions === null" role="grid" :aria-label="t('app.admin.roles.permissions.pageTitle', { name: role.name_en })">
                <TableHeader>
                    <TableRow>
                        <TableHead scope="col" class="sticky inset-inline-start-0 bg-background min-w-[180px]">
                            <div class="h-4 animate-pulse rounded bg-muted w-24" />
                        </TableHead>
                        <TableHead v-for="action in actions" :key="action" scope="col" class="text-center min-w-[72px]">
                            <div class="h-4 animate-pulse rounded bg-muted w-12 mx-auto" />
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="i in 8" :key="i">
                        <TableCell class="sticky inset-inline-start-0 bg-background">
                            <div class="h-4 animate-pulse rounded bg-muted w-32" />
                        </TableCell>
                        <TableCell v-for="action in actions" :key="action" class="text-center">
                            <div class="h-4 w-4 animate-pulse rounded bg-muted mx-auto" />
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <!-- Loaded matrix -->
            <Table v-else role="grid" :aria-label="t('app.admin.roles.permissions.pageTitle', { name: role.name_en })">
                <TableHeader class="sticky top-0 z-10 bg-background">
                    <!-- Column labels row -->
                    <TableRow>
                        <TableHead scope="col" class="sticky inset-inline-start-0 bg-background min-w-[180px]">
                            {{ t('app.admin.roles.permissions.allSubjects') }}
                        </TableHead>
                        <TableHead
                            v-for="action in actions"
                            :key="action"
                            scope="col"
                            class="text-center min-w-[72px]"
                        >
                            {{ actionLabel(action) }}
                        </TableHead>
                    </TableRow>
                    <!-- Column "select all" checkboxes row -->
                    <TableRow>
                        <TableHead scope="col" class="sticky inset-inline-start-0 bg-background">
                            <input
                                type="checkbox"
                                class="size-4 cursor-pointer"
                                :checked="allState() === 'all'"
                                :indeterminate="allState() === 'indeterminate'"
                                :disabled="role.is_system"
                                :aria-label="t('app.admin.roles.permissions.allSubjects')"
                                :aria-disabled="role.is_system ? 'true' : undefined"
                                @change="toggleAll(($event.target as HTMLInputElement).checked)"
                            />
                        </TableHead>
                        <TableHead
                            v-for="action in actions"
                            :key="action"
                            scope="col"
                            class="text-center"
                        >
                            <TooltipProvider v-if="action === 'VIEW'">
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <input
                                            type="checkbox"
                                            class="size-4 cursor-pointer"
                                            :checked="columnState(action) === 'all'"
                                            :indeterminate="columnState(action) === 'indeterminate'"
                                            :disabled="role.is_system"
                                            :aria-label="`${t('app.admin.roles.permissions.all')} ${actionLabel(action)}`"
                                            :aria-disabled="role.is_system ? 'true' : undefined"
                                            @change="toggleColumn(action, ($event.target as HTMLInputElement).checked)"
                                        />
                                    </TooltipTrigger>
                                    <TooltipContent>{{ t('app.admin.roles.permissions.viewRequiredTooltip') }}</TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                            <input
                                v-else
                                type="checkbox"
                                class="size-4 cursor-pointer"
                                :checked="columnState(action) === 'all'"
                                :indeterminate="columnState(action) === 'indeterminate'"
                                :disabled="role.is_system"
                                :aria-label="`${t('app.admin.roles.permissions.all')} ${actionLabel(action)}`"
                                :aria-disabled="role.is_system ? 'true' : undefined"
                                @change="toggleColumn(action, ($event.target as HTMLInputElement).checked)"
                            />
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="subject in subjects" :key="subject">
                        <TableHead
                            scope="row"
                            class="sticky inset-inline-start-0 bg-background font-normal"
                        >
                            <div class="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    class="size-4 cursor-pointer"
                                    :checked="rowState(subject) === 'all'"
                                    :indeterminate="rowState(subject) === 'indeterminate'"
                                    :disabled="role.is_system"
                                    :aria-label="`${t('app.admin.roles.permissions.allSubjects')} ${subjectLabel(subject)}`"
                                    :aria-disabled="role.is_system ? 'true' : undefined"
                                    @change="toggleRow(subject, ($event.target as HTMLInputElement).checked)"
                                />
                                <span
                                    :lang="isArabic ? 'ar' : 'en'"
                                    :dir="isArabic ? 'rtl' : 'ltr'"
                                    style="line-height: 1.6"
                                >
                                    {{ subjectLabel(subject) }}
                                </span>
                            </div>
                        </TableHead>
                        <TableCell
                            v-for="action in actions"
                            :key="action"
                            role="gridcell"
                            class="text-center"
                            :class="{ 'opacity-50': role.is_system }"
                        >
                            <input
                                type="checkbox"
                                class="size-4"
                                :class="{ 'cursor-pointer': ! role.is_system, 'cursor-not-allowed': role.is_system }"
                                :checked="matrix[subject]?.[action] ?? false"
                                :disabled="role.is_system"
                                :aria-label="`${actionLabel(action)} ${t('app.admin.roles.permissions.allSubjects')} ${subjectLabel(subject)}`"
                                :aria-disabled="role.is_system ? 'true' : undefined"
                                @change="toggleCell(subject, action, ($event.target as HTMLInputElement).checked)"
                            />
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <!-- Error banner -->
        <div
            v-if="saveError"
            role="alert"
            class="mt-4 flex items-center gap-3 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-950 dark:text-red-200"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            {{ saveError }}
        </div>

        <!-- Sticky action bar (editable role only) -->
        <div
            v-if="! role.is_system"
            class="sticky bottom-0 mt-4 flex items-center justify-between gap-4 rounded-md border bg-background px-4 py-3 shadow-sm"
        >
            <div class="flex items-center gap-2 text-sm text-muted-foreground" aria-live="polite">
                <span v-if="isDirty" class="flex items-center gap-1.5">
                    <span class="inline-block size-2 rounded-full bg-orange-500" />
                    {{ t('app.admin.roles.permissions.unsavedChanges') }}
                </span>
            </div>
            <div class="flex items-center gap-2">
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="! isDirty || isSaving"
                    @click="cancel"
                >
                    {{ t('app.admin.roles.permissions.cancel') }}
                </Button>
                <Button
                    size="sm"
                    :disabled="! isDirty || isSaving"
                    :aria-disabled="! isDirty || isSaving ? 'true' : undefined"
                    @click="save"
                >
                    {{ isSaving ? t('app.admin.roles.permissions.saving') : t('app.admin.roles.permissions.savePermissions') }}
                </Button>
            </div>
        </div>
    </div>
</template>
