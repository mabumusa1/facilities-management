<script setup lang="ts">
import { Head, router, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, ref, watchEffect } from 'vue';
import PageHeader from '@/components/PageHeader.vue';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Skeleton } from '@/components/ui/skeleton';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useI18n } from '@/composables/useI18n';
import {
    confirm as importConfirm,
    errorReport as importErrorReport,
} from '@/actions/App/Http/Controllers/Leasing/LeadImportController';
import { index as leadsIndex } from '@/routes/leads';

const { t } = useI18n();

const props = defineProps<{
    excelSheet: {
        id: number;
        file_name: string | null;
        total_rows: number;
        valid_count: number;
        error_count: number;
        errors: Array<{ row: number; field: string; message: string }>;
        valid_rows: Array<Record<string, unknown>>;
    };
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.leads.pageTitle'), href: leadsIndex.url() },
            { title: t('app.leads.review.heading'), href: '' },
        ],
    });
});

const fileName = computed(() => props.excelSheet.file_name ?? 'import');
const totalRows = computed(() => props.excelSheet.total_rows);
const validCount = computed(() => props.excelSheet.valid_count);
const errorCount = computed(() => props.excelSheet.error_count);
const errors = computed(() => props.excelSheet.errors ?? []);

const isAllValid = computed(() => errorCount.value === 0 && validCount.value > 0);
const isAllInvalid = computed(() => validCount.value === 0);
const hasMixed = computed(() => validCount.value > 0 && errorCount.value > 0);

const confirmOpen = ref(false);

const form = useForm({});

function openConfirm() {
    if (hasMixed.value) {
        confirmOpen.value = true;
    } else {
        submitImport();
    }
}

function submitImport() {
    confirmOpen.value = false;
    form.post(importConfirm.url({ excelSheet: props.excelSheet.id }), {
        onSuccess: () => {
            // Redirect handled by server
        },
    });
}

function cancel() {
    router.visit(leadsIndex.url());
}

const pageTitle = computed(() =>
    t('app.leads.review.pageTitle', { filename: fileName.value }),
);
</script>

<template>
    <Head :title="pageTitle" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            :title="pageTitle"
            :description="isAllValid ? '' : t('app.leads.review.description')"
        >
            <template v-if="errors.length > 0" #actions>
                <Button variant="outline" as-child>
                    <a :href="importErrorReport.url({ excelSheet: excelSheet.id })">
                        {{ t('app.leads.review.downloadErrorReport') }}
                    </a>
                </Button>
            </template>
        </PageHeader>

        <!-- Summary stat cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">
                        {{ t('app.leads.review.totalRows') }}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-3xl font-bold">{{ totalRows }}</p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">
                        {{ t('app.leads.review.validRows') }}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-3xl font-bold" :class="validCount > 0 ? 'text-green-600 dark:text-green-400' : ''">
                        {{ validCount }}
                    </p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">
                        {{ t('app.leads.review.errorRows') }}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-3xl font-bold" :class="errorCount > 0 ? 'text-destructive' : ''">
                        {{ errorCount }}
                    </p>
                </CardContent>
            </Card>
        </div>

        <!-- All-valid success alert -->
        <Alert v-if="isAllValid" role="alert">
            <AlertTitle>{{ t('app.leads.review.allValidAlert', { n: validCount }) }}</AlertTitle>
        </Alert>

        <!-- All-invalid error alert -->
        <Alert v-else-if="isAllInvalid" variant="destructive" role="alert">
            <AlertTitle>{{ t('app.leads.review.allInvalidAlert') }}</AlertTitle>
        </Alert>

        <!-- Error table -->
        <Card v-if="errors.length > 0">
            <CardHeader>
                <CardTitle class="flex items-center justify-between">
                    {{ t('app.leads.review.downloadErrorReport') }}
                    <Button variant="outline" size="sm" as-child>
                        <a :href="importErrorReport.url({ excelSheet: excelSheet.id })">
                            {{ t('app.leads.review.downloadErrorReport') }}
                        </a>
                    </Button>
                </CardTitle>
            </CardHeader>
            <CardContent>
                <div class="overflow-x-auto">
                    <Table role="grid">
                        <TableHeader>
                            <TableRow>
                                <TableHead class="sticky inset-inline-start-0 bg-background">
                                    {{ t('app.leads.review.tableRowNum') }}
                                </TableHead>
                                <TableHead>{{ t('app.leads.review.tableField') }}</TableHead>
                                <TableHead>{{ t('app.leads.review.tableError') }}</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="(err, index) in errors" :key="index">
                                <TableCell class="sticky inset-inline-start-0 bg-background font-medium">
                                    {{ err.row }}
                                </TableCell>
                                <TableCell>{{ err.field }}</TableCell>
                                <TableCell>{{ err.message }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </CardContent>
        </Card>

        <!-- Skeleton loading state (should not normally appear here, but defensive) -->
        <div v-if="totalRows === 0 && errors.length === 0" class="space-y-3">
            <Skeleton class="h-10 w-full" />
            <Skeleton class="h-10 w-full" />
            <Skeleton class="h-10 w-3/4" />
        </div>

        <!-- Action footer -->
        <div class="flex items-center justify-between gap-4">
            <Button variant="outline" @click="cancel">
                {{ t('app.leads.review.cancelLink') }}
            </Button>

            <Button
                :disabled="isAllInvalid || form.processing"
                :aria-disabled="isAllInvalid"
                :title="isAllInvalid ? t('app.leads.review.importDisabledTooltip') : undefined"
                @click="openConfirm"
            >
                <span v-if="hasMixed">
                    {{ t('app.leads.review.importCta', { n: validCount }) }}
                </span>
                <span v-else>
                    {{ t('app.leads.review.importAllCta', { n: validCount }) }}
                </span>
            </Button>
        </div>
    </div>

    <!-- Confirm dialog (mixed rows only) -->
    <Dialog v-model:open="confirmOpen" role="alertdialog">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ t('app.leads.review.confirmTitle') }}</DialogTitle>
                <DialogDescription>
                    {{ t('app.leads.review.confirmBody', { n: validCount, m: errorCount }) }}
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" @click="confirmOpen = false">
                    {{ t('app.common.cancel') }}
                </Button>
                <Button :disabled="form.processing" @click="submitImport">
                    {{ t('app.leads.review.confirmCta', { n: validCount }) }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
