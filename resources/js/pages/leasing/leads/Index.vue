<script setup lang="ts">
import { Head, Link, router, setLayoutProps, useForm } from '@inertiajs/vue3';
import { ref, watchEffect } from 'vue';
import InputError from '@/components/InputError.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Sheet, SheetContent, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { Skeleton } from '@/components/ui/skeleton';
import { Spinner } from '@/components/ui/spinner';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Textarea } from '@/components/ui/textarea';
import { useI18n } from '@/composables/useI18n';
import { index as leadsIndex, store as leadsStore, show as leadsShow } from '@/routes/leads';
import { template as importTemplate, preview as importPreview } from '@/actions/App/Http/Controllers/Leasing/LeadImportController';
import type { Lead, Paginated } from '@/types';

const { t, locale } = useI18n();

type LeadSource = { id: number; name: string; name_en: string | null; name_ar: string | null };
type LeadStatus = { id: number; name: string; name_en: string | null; name_ar: string | null };

const props = defineProps<{
    leads: Paginated<Lead> | undefined;
    sources: LeadSource[];
    statuses: LeadStatus[];
    filters: {
        search: string;
        status_id: string;
        source_id: string;
        per_page: string;
    };
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.leads.pageTitle'), href: leadsIndex.url() },
        ],
    });
});

// Filter state
const filters = ref({
    search: props.filters.search ?? '',
    status_id: props.filters.status_id ?? '',
    source_id: props.filters.source_id ?? '',
    per_page: props.filters.per_page ?? '15',
});

const perPageOptions = ['10', '15', '25', '50'];

function applyFilters() {
    router.get(leadsIndex.url(), { ...filters.value }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function resetFilters() {
    filters.value.search = '';
    filters.value.status_id = '';
    filters.value.source_id = '';
    filters.value.per_page = '15';
    applyFilters();
}

// Add Lead drawer
const drawerOpen = ref(false);

// Import sheet
const importSheetOpen = ref(false);

const importForm = useForm<{
    file: File | null;
}>({
    file: null,
});

function handleFileChange(event: Event) {
    const input = event.target as HTMLInputElement;
    importForm.file = input.files?.[0] ?? null;
}

function submitImport() {
    importForm.post(importPreview.url(), {
        forceFormData: true,
        onError: () => {
            // Keep sheet open to show error
        },
    });
}

function closeImportSheet() {
    importSheetOpen.value = false;
    importForm.reset();
    importForm.clearErrors();
}

const form = useForm<{
    name_en: string;
    name_ar: string;
    phone_country_code: string;
    phone_number: string;
    email: string;
    source_id: string;
    notes: string;
}>({
    name_en: '',
    name_ar: '',
    phone_country_code: '+966',
    phone_number: '',
    email: '',
    source_id: '',
    notes: '',
});

function handleDrawerOpenChange(value: boolean) {
    if (!value && form.isDirty) {
        if (!confirm(t('app.admin.users.cancelConfirm'))) {
            return;
        }
    }
    if (!value) {
        form.reset();
        form.clearErrors();
    }
    drawerOpen.value = value;
}

function submitLead() {
    form.post(leadsStore.url(), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            form.clearErrors();
            drawerOpen.value = false;
        },
    });
}

// Helpers
function displaySourceName(source: LeadSource | undefined): string {
    if (!source) return '—';
    return (locale.value === 'ar' ? source.name_ar : source.name_en) ?? source.name;
}

function displayStatusName(status: LeadStatus | undefined): string {
    if (!status) return '—';
    return (locale.value === 'ar' ? status.name_ar : status.name_en) ?? status.name;
}

function statusBadgeVariant(nameEn: string | null): 'default' | 'secondary' | 'outline' | 'destructive' {
    switch (nameEn?.toLowerCase()) {
        case 'new':
            return 'default';
        case 'contacted':
            return 'outline';
        case 'qualified':
            return 'secondary';
        case 'converted':
            return 'default';
        case 'lost':
            return 'destructive';
        default:
            return 'outline';
    }
}

function displayLeadName(lead: Lead): string {
    if (locale.value === 'ar' && lead.name_ar) return lead.name_ar;
    return lead.name_en ?? lead.name ?? '—';
}

function displayOwnerName(lead: Lead): string {
    if (!lead.assigned_to) return t('app.leads.table.unassigned');
    const owner = lead.assigned_to;
    if (owner.first_name && owner.last_name) return `${owner.first_name} ${owner.last_name}`;
    return owner.name ?? t('app.leads.table.unassigned');
}

const SKELETON_ROWS = 8;
</script>

<template>
    <Head :title="t('app.leads.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            :title="t('app.leads.heading')"
            :description="t('app.leads.description')"
        >
            <template #actions>
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button variant="outline">
                            {{ t('app.leads.importLeads') }} ▾
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem as-child>
                            <a :href="importTemplate.url()">{{ t('app.leads.downloadTemplate') }}</a>
                        </DropdownMenuItem>
                        <DropdownMenuItem @click="importSheetOpen = true">
                            {{ t('app.leads.importFromExcel') }}
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>

                <Button @click="drawerOpen = true">
                    {{ t('app.leads.addLead') }}
                </Button>
            </template>
        </PageHeader>

        <!-- Filter form -->
        <form class="grid gap-4 rounded-lg border p-4 md:grid-cols-6" @submit.prevent="applyFilters">
            <div class="grid gap-2 md:col-span-2">
                <Label for="lead-search">{{ t('app.leads.filter.search') }}</Label>
                <Input
                    id="lead-search"
                    v-model="filters.search"
                    :placeholder="t('app.leads.filter.searchPlaceholder')"
                />
            </div>

            <div class="grid gap-2">
                <Label for="lead-status">{{ t('app.leads.filter.status') }}</Label>
                <select
                    id="lead-status"
                    v-model="filters.status_id"
                    class="rounded-md border border-input bg-background px-3 py-2 text-sm"
                >
                    <option value="">{{ t('app.leads.filter.allStatuses') }}</option>
                    <option v-for="status in props.statuses" :key="status.id" :value="String(status.id)">
                        {{ displayStatusName(status) }}
                    </option>
                </select>
            </div>

            <div class="grid gap-2">
                <Label for="lead-source">{{ t('app.leads.filter.source') }}</Label>
                <select
                    id="lead-source"
                    v-model="filters.source_id"
                    class="rounded-md border border-input bg-background px-3 py-2 text-sm"
                >
                    <option value="">{{ t('app.leads.filter.allSources') }}</option>
                    <option v-for="source in props.sources" :key="source.id" :value="String(source.id)">
                        {{ displaySourceName(source) }}
                    </option>
                </select>
            </div>

            <div class="grid gap-2">
                <Label for="lead-per-page">{{ t('app.leads.filter.rows') }}</Label>
                <select
                    id="lead-per-page"
                    v-model="filters.per_page"
                    class="rounded-md border border-input bg-background px-3 py-2 text-sm"
                >
                    <option v-for="option in perPageOptions" :key="option" :value="option">{{ option }}</option>
                </select>
            </div>

            <div class="flex items-end gap-2 md:col-span-6">
                <Button type="submit">{{ t('app.leads.filter.apply') }}</Button>
                <Button type="button" variant="outline" @click="resetFilters">{{ t('app.leads.filter.reset') }}</Button>
            </div>
        </form>

        <!-- Table skeleton while leads deferred prop loads -->
        <div v-if="leads === undefined" class="rounded-lg border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>{{ t('app.leads.table.name') }}</TableHead>
                        <TableHead>{{ t('app.leads.table.phone') }}</TableHead>
                        <TableHead>{{ t('app.leads.table.email') }}</TableHead>
                        <TableHead>{{ t('app.leads.table.source') }}</TableHead>
                        <TableHead>{{ t('app.leads.table.status') }}</TableHead>
                        <TableHead>{{ t('app.leads.table.assignedTo') }}</TableHead>
                        <TableHead>{{ t('app.leads.table.createdAt') }}</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="i in SKELETON_ROWS" :key="i">
                        <TableCell><Skeleton class="h-4 w-32" /></TableCell>
                        <TableCell><Skeleton class="h-4 w-24" /></TableCell>
                        <TableCell><Skeleton class="h-4 w-36" /></TableCell>
                        <TableCell><Skeleton class="h-4 w-24" /></TableCell>
                        <TableCell><Skeleton class="h-5 w-16 rounded-full" /></TableCell>
                        <TableCell><Skeleton class="h-4 w-28" /></TableCell>
                        <TableCell><Skeleton class="h-4 w-20" /></TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <!-- Empty state -->
        <div v-else-if="leads.total === 0" class="flex flex-col items-center justify-center gap-4 rounded-lg border py-16 text-center">
            <p class="text-lg font-semibold">{{ t('app.leads.noLeadsYet') }}</p>
            <p class="text-muted-foreground max-w-md text-sm">{{ t('app.leads.noLeadsDescription') }}</p>
            <div class="flex gap-2">
                <Button @click="drawerOpen = true">{{ t('app.leads.addLead') }}</Button>
                <Button variant="outline" @click="importSheetOpen = true">
                    {{ t('app.leads.importFromExcel') }}
                </Button>
            </div>
        </div>

        <!-- Populated table -->
        <template v-else>
            <div class="rounded-lg border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>{{ t('app.leads.table.name') }}</TableHead>
                            <TableHead>{{ t('app.leads.table.phone') }}</TableHead>
                            <TableHead>{{ t('app.leads.table.email') }}</TableHead>
                            <TableHead>{{ t('app.leads.table.source') }}</TableHead>
                            <TableHead>{{ t('app.leads.table.status') }}</TableHead>
                            <TableHead>{{ t('app.leads.table.assignedTo') }}</TableHead>
                            <TableHead>{{ t('app.leads.table.createdAt') }}</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="lead in leads.data"
                            :key="lead.id"
                            class="cursor-pointer hover:bg-muted/50"
                            @click="router.visit(leadsShow.url({ lead: lead.id }))"
                        >
                            <TableCell class="font-medium">
                                <Link :href="leadsShow.url({ lead: lead.id })" class="hover:underline" @click.stop>
                                    {{ displayLeadName(lead) }}
                                </Link>
                            </TableCell>
                            <TableCell dir="ltr">
                                {{ lead.phone_country_code ? `${lead.phone_country_code} ${lead.phone_number}` : lead.phone_number }}
                            </TableCell>
                            <TableCell>{{ lead.email ?? '—' }}</TableCell>
                            <TableCell>{{ displaySourceName(lead.source) }}</TableCell>
                            <TableCell>
                                <Badge :variant="statusBadgeVariant(lead.status?.name_en ?? null)">
                                    {{ displayStatusName(lead.status) }}
                                </Badge>
                            </TableCell>
                            <TableCell>{{ displayOwnerName(lead) }}</TableCell>
                            <TableCell>{{ new Date(lead.created_at).toLocaleDateString() }}</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div class="flex items-center justify-between text-sm text-muted-foreground">
                <p>{{ t('app.leads.showingSummary', { from: leads.from ?? 0, to: leads.to ?? 0, total: leads.total }) }}</p>
                <p>{{ t('app.common.pageSummary', { current: leads.current_page, last: leads.last_page }) }}</p>
            </div>

            <!-- Pagination -->
            <div v-if="leads.links.length > 3" class="flex items-center justify-end gap-1">
                <template v-for="link in leads.links" :key="link.label">
                    <Button
                        v-if="link.url"
                        variant="outline"
                        size="sm"
                        as-child
                        :class="{ 'bg-primary text-primary-foreground': link.active }"
                    >
                        <a :href="link.url" v-html="link.label" />
                    </Button>
                    <Button v-else variant="outline" size="sm" disabled v-html="link.label" />
                </template>
            </div>
        </template>
    </div>

    <!-- Add Lead Drawer -->
    <Sheet :open="drawerOpen" @update:open="handleDrawerOpenChange">
        <SheetContent class="w-full sm:max-w-md" side="right">
            <SheetHeader>
                <SheetTitle>{{ t('app.leads.drawer.title') }}</SheetTitle>
            </SheetHeader>

            <div class="flex flex-col gap-4 overflow-y-auto px-4 py-6">
                <div class="grid gap-2">
                    <Label for="lead-name-en">{{ t('app.leads.drawer.nameEn') }}</Label>
                    <Input id="lead-name-en" v-model="form.name_en" dir="ltr" />
                    <InputError :message="form.errors.name_en" />
                </div>

                <div class="grid gap-2">
                    <Label for="lead-name-ar">{{ t('app.leads.drawer.nameAr') }}</Label>
                    <Input id="lead-name-ar" v-model="form.name_ar" dir="rtl" />
                    <InputError :message="form.errors.name_ar" />
                </div>

                <div class="grid gap-2">
                    <Label for="lead-phone">{{ t('app.leads.drawer.phone') }} *</Label>
                    <div class="flex gap-2" dir="ltr">
                        <Input
                            id="lead-phone-code"
                            v-model="form.phone_country_code"
                            class="w-20"
                            placeholder="+966"
                        />
                        <Input
                            id="lead-phone"
                            v-model="form.phone_number"
                            class="flex-1"
                            placeholder="5XXXXXXXX"
                        />
                    </div>
                    <InputError :message="form.errors.phone_number" />
                    <InputError :message="form.errors.phone_country_code" />
                </div>

                <div class="grid gap-2">
                    <Label for="lead-email">{{ t('app.leads.drawer.email') }}</Label>
                    <Input id="lead-email" v-model="form.email" type="email" dir="ltr" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label>{{ t('app.leads.drawer.source') }} *</Label>
                    <Select
                        :model-value="form.source_id"
                        @update:model-value="(v) => (form.source_id = v ?? '')"
                    >
                        <SelectTrigger>
                            <SelectValue :placeholder="t('app.leads.drawer.selectSource')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="source in props.sources" :key="source.id" :value="String(source.id)">
                                {{ displaySourceName(source) }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.source_id" />
                </div>

                <div class="grid gap-2">
                    <Label for="lead-notes">{{ t('app.leads.drawer.notes') }}</Label>
                    <Textarea id="lead-notes" v-model="form.notes" rows="3" />
                    <InputError :message="form.errors.notes" />
                </div>
            </div>

            <SheetFooter class="px-4 pb-6">
                <Button variant="outline" :disabled="form.processing" @click="handleDrawerOpenChange(false)">
                    {{ t('app.leads.drawer.cancel') }}
                </Button>
                <Button :disabled="form.processing" @click="submitLead">
                    <Spinner v-if="form.processing" />
                    {{ t('app.leads.drawer.save') }}
                </Button>
            </SheetFooter>
        </SheetContent>
    </Sheet>

    <!-- Import Leads Sheet -->
    <Sheet :open="importSheetOpen" @update:open="(open) => { if (!open) closeImportSheet(); }">
        <SheetContent class="w-full sm:max-w-md" side="right">
            <SheetHeader>
                <SheetTitle>{{ t('app.leads.importSheet.heading') }}</SheetTitle>
            </SheetHeader>

            <div class="flex flex-col gap-6 overflow-y-auto px-4 py-6">
                <div class="grid gap-2">
                    <Label for="import-file">
                        {{ t('app.leads.importSheet.heading') }}
                    </Label>
                    <Input
                        id="import-file"
                        type="file"
                        accept=".xlsx,.xls"
                        :disabled="importForm.processing"
                        @change="handleFileChange"
                    />
                    <InputError :message="importForm.errors.file" />
                    <p class="text-muted-foreground text-xs" aria-live="polite">
                        {{ t('app.leads.importSheet.fileHint') }} ·
                        {{ t('app.leads.importSheet.maxSize') }} ·
                        {{ t('app.leads.importSheet.maxRows') }}
                    </p>
                </div>

                <p class="text-sm">
                    <a :href="importTemplate.url()" class="text-primary underline-offset-4 hover:underline">
                        {{ t('app.leads.importSheet.templateLink') }}
                    </a>
                </p>
            </div>

            <SheetFooter class="px-4 pb-6">
                <Button variant="outline" :disabled="importForm.processing" @click="closeImportSheet">
                    {{ t('app.leads.importSheet.cancel') }}
                </Button>
                <Button :disabled="importForm.processing || !importForm.file" @click="submitImport">
                    <Spinner v-if="importForm.processing" />
                    {{ importForm.processing ? t('app.leads.importSheet.uploading') : t('app.leads.importSheet.uploadCta') }}
                </Button>
            </SheetFooter>
        </SheetContent>
    </Sheet>
</template>
