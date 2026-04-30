<script setup lang="ts">
import { Head, router, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, ref, watchEffect } from 'vue';
import { MoreHorizontal, UserPlus, ArrowRight, MessageSquare, RefreshCw } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Sheet, SheetContent, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { Skeleton } from '@/components/ui/skeleton';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { useI18n } from '@/composables/useI18n';
import {
    show as leadsShow,
    index as leadsIndex,
    update as leadsUpdate,
    assign as leadsAssign,
    unassign as leadsUnassign,
    addNote as leadsAddNote,
    destroy as leadsDestroy,
    checkDuplicate as leadsCheckDuplicate,
    convert as leadsConvert,
} from '@/actions/App/Http/Controllers/Leasing/LeadController';
import type { LeadActivity } from '@/types/models';

const { t, locale, isArabic } = useI18n();

type LeadStatus = {
    id: number;
    name: string;
    name_en: string | null;
    name_ar: string | null;
};

type TeamMember = {
    id: number;
    name: string;
    email: string;
};

type ConvertedContact = {
    id: number;
    type: 'owner' | 'resident';
    name: string;
    email: string | null;
    converted_at: string | null;
    url: string;
};

type LeadDetail = {
    id: number;
    name: string | null;
    name_en: string | null;
    name_ar: string | null;
    phone_number: string;
    phone_country_code: string | null;
    email: string | null;
    notes: string | null;
    lost_reason: string | null;
    created_at: string | null;
    is_converted: boolean;
    converted_contact: ConvertedContact | null;
    status: LeadStatus | null;
    source: { id: number; name: string; name_en: string | null; name_ar: string | null } | null;
    assigned_to: { id: number; name: string; email: string } | null;
};

const props = defineProps<{
    lead: LeadDetail;
    canConvert: boolean;
    statuses: LeadStatus[];
    teamMembers: TeamMember[];
    activities: LeadActivity[] | undefined;
}>();

// ------------------------------------------------------------------ helpers

function displayLeadName(lead: LeadDetail): string {
    if (isArabic.value && lead.name_ar) return lead.name_ar;
    return lead.name_en ?? lead.name ?? '—';
}

function displayStatusName(status: LeadStatus | null): string {
    if (!status) return '—';
    return (isArabic.value ? status.name_ar : status.name_en) ?? status.name;
}

function displaySourceName(source: { name: string; name_en: string | null; name_ar: string | null } | null): string {
    if (!source) return '—';
    return (isArabic.value ? source.name_ar : source.name_en) ?? source.name;
}

function statusBadgeClass(nameEn: string | null): string {
    switch (nameEn?.toLowerCase()) {
        case 'new': return 'bg-blue-100 text-blue-800';
        case 'contacted': return 'bg-amber-100 text-amber-800';
        case 'qualified': return 'bg-green-100 text-green-800';
        case 'converted': return 'bg-teal-100 text-teal-800';
        case 'lost': return 'bg-gray-100 text-gray-600';
        default: return '';
    }
}

function formatDate(isoString: string | null): string {
    if (!isoString) return '—';
    return new Intl.DateTimeFormat(isArabic.value ? 'ar-SA' : 'en-GB', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        numberingSystem: 'latn',
        calendar: 'gregory',
    }).format(new Date(isoString));
}

function formatDateOnly(isoString: string | null): string {
    if (!isoString) return '—';
    return new Intl.DateTimeFormat(isArabic.value ? 'ar-SA' : 'en-GB', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        numberingSystem: 'latn',
        calendar: 'gregory',
    }).format(new Date(isoString));
}

function formatTime(isoString: string | null): string {
    if (!isoString) return '';
    return new Intl.DateTimeFormat(isArabic.value ? 'ar-SA' : 'en-GB', {
        hour: '2-digit',
        minute: '2-digit',
        numberingSystem: 'latn',
        calendar: 'gregory',
    }).format(new Date(isoString));
}

// ------------------------------------------------------------------ layout

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.leads.heading'), href: leadsIndex.url() },
            { title: displayLeadName(props.lead), href: leadsShow.url({ lead: props.lead.id }) },
        ],
    });
});

// ------------------------------------------------------------------ tabs

type Tab = 'details' | 'activity';
const activeTab = ref<Tab>('details');

const activityCount = computed<number>(() => props.activities?.length ?? 0);

// ------------------------------------------------------------------ status form

const statusForm = useForm({
    status_id: props.lead.status?.id ? String(props.lead.status.id) : '',
    lost_reason: props.lead.lost_reason ?? '',
});

const selectedStatusNameEn = computed<string | null>(() => {
    const found = props.statuses.find((s) => String(s.id) === statusForm.status_id);
    return found?.name_en ?? null;
});

const isLostSelected = computed<boolean>(() => selectedStatusNameEn.value?.toLowerCase() === 'lost');

const isStatusDirty = computed<boolean>(
    () =>
        statusForm.status_id !== String(props.lead.status?.id ?? '') ||
        statusForm.lost_reason !== (props.lead.lost_reason ?? ''),
);

function discardStatusChanges(): void {
    statusForm.status_id = props.lead.status?.id ? String(props.lead.status.id) : '';
    statusForm.lost_reason = props.lead.lost_reason ?? '';
    statusForm.clearErrors();
}

function saveStatus(): void {
    statusForm.put(leadsUpdate.url({ lead: props.lead.id }), {
        preserveScroll: true,
    });
}

// ------------------------------------------------------------------ assign sheet

const assignSheetOpen = ref(false);
const assignSearch = ref('');
const selectedMemberId = ref<number | null>(null);

const filteredTeamMembers = computed<TeamMember[]>(() => {
    const q = assignSearch.value.trim().toLowerCase();
    if (!q) return props.teamMembers;
    return props.teamMembers.filter(
        (m) => m.name.toLowerCase().includes(q) || m.email.toLowerCase().includes(q),
    );
});

const assignForm = useForm({ user_id: '' });

function openAssignSheet(): void {
    assignSearch.value = '';
    selectedMemberId.value = null;
    assignForm.reset();
    assignSheetOpen.value = true;
}

function confirmAssign(): void {
    if (selectedMemberId.value === null) return;
    assignForm.user_id = String(selectedMemberId.value);
    assignForm.post(leadsAssign.url({ lead: props.lead.id }), {
        preserveScroll: true,
        onSuccess: () => {
            assignSheetOpen.value = false;
        },
    });
}

const unassignForm = useForm({});

function handleUnassign(): void {
    unassignForm.post(leadsUnassign.url({ lead: props.lead.id }), {
        preserveScroll: true,
    });
}

// ------------------------------------------------------------------ add note

const showNoteCompose = ref(false);
const noteForm = useForm({ note: '' });

function openNoteCompose(): void {
    noteForm.reset();
    noteForm.clearErrors();
    showNoteCompose.value = true;
}

function cancelNote(): void {
    showNoteCompose.value = false;
    noteForm.reset();
    noteForm.clearErrors();
}

function saveNote(): void {
    noteForm.post(leadsAddNote.url({ lead: props.lead.id }), {
        preserveScroll: true,
        onSuccess: () => {
            showNoteCompose.value = false;
            noteForm.reset();
        },
    });
}

// ------------------------------------------------------------------ delete dialog

const deleteDialogOpen = ref(false);
const deleteForm = useForm({});

function confirmDelete(): void {
    deleteForm.delete(leadsDestroy.url({ lead: props.lead.id }), {
        onSuccess: () => {
            deleteDialogOpen.value = false;
        },
    });
}

// ------------------------------------------------------------------ activity grouping

type ActivityGroup = {
    dateLabel: string;
    items: LeadActivity[];
};

const groupedActivities = computed<ActivityGroup[]>(() => {
    if (!props.activities) return [];
    const groups: Record<string, LeadActivity[]> = {};
    for (const activity of props.activities) {
        const dateKey = activity.created_at
            ? formatDateOnly(activity.created_at)
            : '—';
        if (!groups[dateKey]) groups[dateKey] = [];
        groups[dateKey].push(activity);
    }
    return Object.entries(groups).map(([dateLabel, items]) => ({ dateLabel, items }));
});

function activityDescription(activity: LeadActivity): string {
    const data = activity.data ?? {};
    switch (activity.type) {
        case 'assigned':
            return t('app.leads.detail.activityAssigned', { name: data.to ?? '' });
        case 'unassigned':
            return t('app.leads.detail.activityUnassigned', { name: data.from ?? '' });
        case 'status_change': {
            const from = isArabic.value ? (data.from_ar ?? data.from ?? '') : (data.from ?? '');
            const to = isArabic.value ? (data.to_ar ?? data.to ?? '') : (data.to ?? '');
            return t('app.leads.detail.activityStatusChange', { from, to });
        }
        case 'note':
            return data.note ?? '';
        case 'converted':
            return t('app.leads.detail.activityConverted', { type: data.contact_type ?? '' });
        default:
            return '';
    }
}

// ------------------------------------------------------------------ convert

type DedupMatch = {
    id: number;
    type: 'owner' | 'resident';
    name: string;
    email: string | null;
    phone_number: string | null;
};

// Multi-step: 'drawer' → 'dedup' → 'link-confirm' | 'create-confirm' → done
type ConvertStep = 'closed' | 'drawer' | 'dedup' | 'link-confirm' | 'create-confirm';

const convertStep = ref<ConvertStep>('closed');
const convertContactType = ref<'owner' | 'resident'>('owner');
const dedupMatch = ref<DedupMatch | null>(null);
const dedupChoice = ref<'link' | 'create'>('link');
const convertProcessing = ref(false);
const convertError = ref<string | null>(null);
const dedupCheckError = ref<string | null>(null);

function openConvertDrawer(): void {
    convertContactType.value = 'owner';
    dedupMatch.value = null;
    dedupChoice.value = 'link';
    convertError.value = null;
    dedupCheckError.value = null;
    convertStep.value = 'drawer';
}

function closeConvert(): void {
    convertStep.value = 'closed';
    convertProcessing.value = false;
    convertError.value = null;
    dedupCheckError.value = null;
}

async function handleConvertCta(): Promise<void> {
    convertError.value = null;
    dedupCheckError.value = null;
    convertProcessing.value = true;

    // 1. Check for duplicate
    try {
        const resp = await fetch(leadsCheckDuplicate.url({ lead: props.lead.id }), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        });

        if (!resp.ok) {
            dedupCheckError.value = t('app.leads.conversion.errorDedupCheck');
            convertProcessing.value = false;
            return;
        }

        const json = await resp.json() as { duplicate: boolean; match?: DedupMatch };

        if (json.duplicate && json.match) {
            dedupMatch.value = json.match;
            dedupChoice.value = 'link';
            convertStep.value = 'dedup';
            convertProcessing.value = false;
            return;
        }
    } catch {
        dedupCheckError.value = t('app.leads.conversion.errorDedupCheck');
        convertProcessing.value = false;
        return;
    }

    // 2. No duplicate — submit directly
    await submitConversion(false, null);
}

function handleDedupContinue(): void {
    if (dedupChoice.value === 'link') {
        convertStep.value = 'link-confirm';
    } else {
        convertStep.value = 'create-confirm';
    }
}

async function submitConversion(linkToExisting: boolean, existingId: number | null): Promise<void> {
    convertError.value = null;
    convertProcessing.value = true;

    const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null)?.content ?? '';

    try {
        const resp = await fetch(leadsConvert.url({ lead: props.lead.id }), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                contact_type: convertContactType.value,
                link_to_existing: linkToExisting,
                existing_contact_id: existingId,
            }),
        });

        if (!resp.ok) {
            convertError.value = t('app.leads.conversion.errorFailed');
            convertStep.value = 'drawer';
            convertProcessing.value = false;
            return;
        }

        // Success — reload the page with a fresh visit
        closeConvert();
        router.reload({ preserveScroll: false });
    } catch {
        convertError.value = t('app.leads.conversion.errorFailed');
        convertStep.value = 'drawer';
        convertProcessing.value = false;
    }
}

async function confirmLink(): Promise<void> {
    if (dedupMatch.value === null) return;
    await submitConversion(true, dedupMatch.value.id);
}

async function confirmCreateAnyway(): Promise<void> {
    await submitConversion(false, null);
}
</script>

<template>
    <Head :title="t('app.leads.detail.pageTitle', { name: displayLeadName(lead) })" />

    <div class="flex flex-col gap-6 p-4">
        <!-- Header -->
        <div class="flex items-start justify-between gap-4">
            <div class="flex flex-col gap-1">
                <h2 class="text-2xl font-bold">{{ displayLeadName(lead) }}</h2>
                <div v-if="lead.name_ar && !isArabic" class="text-muted-foreground text-sm" dir="rtl" lang="ar">
                    {{ lead.name_ar }}
                </div>
                <div class="flex items-center gap-2">
                    <Badge class="text-xs" :class="statusBadgeClass(lead.status?.name_en ?? null)">
                        {{ displayStatusName(lead.status) }}
                    </Badge>
                    <span v-if="lead.source" class="text-muted-foreground text-sm">· {{ displaySourceName(lead.source) }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <!-- Convert to Contact — shown only for Qualified, non-converted leads with permission -->
                <Button v-if="canConvert" size="sm" @click="openConvertDrawer">
                    {{ t('app.leads.conversion.convertBtn') }}
                </Button>

                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button variant="outline" size="sm" :aria-label="t('app.leads.detail.moreActions')">
                            <MoreHorizontal class="h-4 w-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem class="text-destructive focus:text-destructive" @click="deleteDialogOpen = true">
                            {{ t('app.leads.detail.deleteLead') }}
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </div>

        <!-- Converted Contact Card -->
        <Card v-if="lead.is_converted && lead.converted_contact">
            <CardHeader>
                <CardTitle>{{ t('app.leads.conversion.convertedCardTitle') }}</CardTitle>
            </CardHeader>
            <CardContent>
                <a
                    :href="lead.converted_contact.url"
                    class="flex items-center justify-between rounded-md border p-3 transition-colors hover:bg-muted"
                    :aria-label="t('app.leads.conversion.convertedViewLink', { name: lead.converted_contact.name, type: lead.converted_contact.type })"
                >
                    <div class="flex flex-col gap-0.5">
                        <span class="font-medium">{{ lead.converted_contact.name }}</span>
                        <span v-if="lead.converted_contact.email" class="text-muted-foreground text-sm" dir="ltr">
                            {{ lead.converted_contact.email }}
                        </span>
                        <span class="text-muted-foreground text-xs">
                            {{ t('app.leads.conversion.convertedDateLabel') }}:
                            {{ formatDateOnly(lead.converted_contact.converted_at) }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <Badge variant="secondary" class="capitalize">{{ lead.converted_contact.type }}</Badge>
                        <ArrowRight class="h-4 w-4 rtl:scale-x-[-1]" />
                    </div>
                </a>
            </CardContent>
        </Card>

        <!-- Status + Pipeline Card -->
        <Card>
            <CardContent class="pt-6">
                <div class="grid gap-4">
                    <div class="grid gap-2">
                        <Label>{{ t('app.leads.detail.statusLabel') }}</Label>
                        <!-- Read-only when converted -->
                        <div v-if="lead.is_converted" class="rounded-md border px-3 py-2 text-sm bg-muted/50">
                            <span :aria-label="`${t('app.leads.detail.statusLabel')}: ${displayStatusName(lead.status)} (read-only)`">
                                {{ displayStatusName(lead.status) }}
                            </span>
                        </div>
                        <template v-else>
                            <Select
                                :model-value="statusForm.status_id"
                                @update:model-value="(v) => { statusForm.status_id = v ?? ''; }"
                            >
                                <SelectTrigger>
                                    <SelectValue :placeholder="t('app.leads.detail.statusPlaceholder')" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="status in statuses" :key="status.id" :value="String(status.id)">
                                        {{ displayStatusName(status) }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="statusForm.errors.status_id" />
                        </template>
                    </div>

                    <!-- Lost reason - visible only when Lost selected -->
                    <div v-if="isLostSelected && !lead.is_converted" class="grid gap-2">
                        <Label>{{ t('app.leads.detail.lostReasonLabel') }}</Label>
                        <Textarea
                            v-model="statusForm.lost_reason"
                            dir="auto"
                            :placeholder="t('app.leads.detail.lostReasonPlaceholder')"
                            :maxlength="500"
                            rows="3"
                            :aria-label="t('app.leads.detail.lostReasonLabel')"
                        />
                        <p class="text-muted-foreground text-xs" aria-live="polite">
                            {{ t('app.leads.detail.lostReasonCounter', { n: statusForm.lost_reason.length }) }}
                        </p>
                    </div>

                    <!-- Dirty-state bar -->
                    <div
                        v-if="isStatusDirty && !lead.is_converted"
                        class="flex items-center justify-between rounded-md border bg-muted/50 px-3 py-2"
                        role="status"
                        aria-live="polite"
                    >
                        <span class="text-muted-foreground flex items-center gap-2 text-sm">
                            <span class="inline-block h-2 w-2 rounded-full bg-amber-500" />
                            {{ t('app.leads.detail.unsavedChanges') }}
                        </span>
                        <div class="flex gap-2">
                            <Button variant="outline" size="sm" :disabled="statusForm.processing" @click="discardStatusChanges">
                                {{ t('app.leads.detail.discard') }}
                            </Button>
                            <Button size="sm" :disabled="statusForm.processing" @click="saveStatus">
                                <Spinner v-if="statusForm.processing" class="h-4 w-4" />
                                {{ t('app.leads.detail.saveChanges') }}
                            </Button>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Tabs -->
        <div>
            <!-- Tab bar -->
            <div role="tablist" class="border-b">
                <button
                    id="tab-details"
                    role="tab"
                    :aria-selected="activeTab === 'details'"
                    aria-controls="tabpanel-details"
                    class="px-4 py-2 text-sm font-medium transition-colors"
                    :class="activeTab === 'details' ? 'border-b-2 border-primary text-primary' : 'text-muted-foreground hover:text-foreground'"
                    @click="activeTab = 'details'"
                >
                    {{ t('app.leads.detail.tabDetails') }}
                </button>
                <button
                    id="tab-activity"
                    role="tab"
                    :aria-selected="activeTab === 'activity'"
                    aria-controls="tabpanel-activity"
                    class="px-4 py-2 text-sm font-medium transition-colors"
                    :class="activeTab === 'activity' ? 'border-b-2 border-primary text-primary' : 'text-muted-foreground hover:text-foreground'"
                    @click="activeTab = 'activity'"
                >
                    {{
                        activities !== undefined && activityCount > 0
                            ? t('app.leads.detail.tabActivityCount', { count: activityCount })
                            : t('app.leads.detail.tabActivity')
                    }}
                </button>
            </div>

            <!-- Details Tab Panel -->
            <div
                id="tabpanel-details"
                role="tabpanel"
                aria-labelledby="tab-details"
                :hidden="activeTab !== 'details'"
                class="mt-4 flex flex-col gap-4"
            >
                <!-- Contact Info -->
                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('app.leads.detail.contactInfoTitle') }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <dl class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-3 text-sm">
                            <dt class="text-muted-foreground font-medium">{{ t('app.leads.detail.phoneLabel') }}</dt>
                            <dd dir="ltr">
                                {{ lead.phone_country_code ? `${lead.phone_country_code} ${lead.phone_number}` : lead.phone_number }}
                            </dd>

                            <dt class="text-muted-foreground font-medium">{{ t('app.leads.detail.emailLabel') }}</dt>
                            <dd>{{ lead.email ?? '—' }}</dd>

                            <dt class="text-muted-foreground font-medium">{{ t('app.leads.detail.sourceLabel') }}</dt>
                            <dd>{{ displaySourceName(lead.source) }}</dd>

                            <dt class="text-muted-foreground font-medium">{{ t('app.leads.detail.createdLabel') }}</dt>
                            <dd>{{ formatDate(lead.created_at) }}</dd>
                        </dl>
                    </CardContent>
                </Card>

                <!-- Assignment Card -->
                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('app.leads.detail.assignmentTitle') }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-muted-foreground text-sm font-medium">{{ t('app.leads.detail.assignedToLabel') }}</span>
                                <span v-if="lead.assigned_to" class="font-medium">
                                    {{ lead.assigned_to.name }}
                                    <span class="text-muted-foreground ml-1 text-xs">({{ lead.assigned_to.email }})</span>
                                </span>
                                <span v-else class="text-muted-foreground italic">{{ t('app.leads.detail.unassigned') }}</span>
                            </div>
                            <div class="flex gap-2">
                                <Button v-if="!lead.assigned_to" size="sm" @click="openAssignSheet">
                                    {{ t('app.leads.detail.assignBtn') }}
                                </Button>
                                <template v-else>
                                    <Button variant="outline" size="sm" @click="openAssignSheet">
                                        {{ t('app.leads.detail.changeBtn') }}
                                    </Button>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        :disabled="unassignForm.processing"
                                        @click="handleUnassign"
                                    >
                                        <Spinner v-if="unassignForm.processing" class="h-4 w-4" />
                                        {{ t('app.leads.detail.unassignBtn') }}
                                    </Button>
                                </template>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Notes Card -->
                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('app.leads.detail.notesTitle') }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p v-if="lead.notes" class="text-sm whitespace-pre-wrap">{{ lead.notes }}</p>
                        <p v-else class="text-muted-foreground text-sm italic">{{ t('app.leads.detail.notesPlaceholder') }}</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Activity Tab Panel -->
            <div
                id="tabpanel-activity"
                role="tabpanel"
                aria-labelledby="tab-activity"
                :hidden="activeTab !== 'activity'"
                class="mt-4 flex flex-col gap-4"
            >
                <!-- Add Note button row -->
                <div class="flex justify-end">
                    <Button variant="secondary" size="sm" @click="openNoteCompose">
                        {{ t('app.leads.detail.addNote') }}
                    </Button>
                </div>

                <!-- Compose area -->
                <div v-if="showNoteCompose" class="rounded-lg border p-4">
                    <div class="grid gap-3">
                        <Textarea
                            v-model="noteForm.note"
                            dir="auto"
                            :placeholder="t('app.leads.detail.composePlaceholder')"
                            :maxlength="2000"
                            rows="4"
                        />
                        <InputError :message="noteForm.errors.note" />
                        <div class="flex justify-end gap-2">
                            <Button variant="outline" size="sm" :disabled="noteForm.processing" @click="cancelNote">
                                {{ t('app.leads.detail.composeCancel') }}
                            </Button>
                            <Button size="sm" :disabled="noteForm.processing" @click="saveNote">
                                <Spinner v-if="noteForm.processing" class="h-4 w-4" />
                                {{ t('app.leads.detail.composeSave') }}
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- Activity feed skeleton -->
                <div v-if="activities === undefined" class="flex flex-col gap-3" aria-busy="true">
                    <div v-for="i in 5" :key="i" class="flex gap-3">
                        <Skeleton class="h-5 w-5 rounded-full" />
                        <div class="flex flex-1 flex-col gap-1">
                            <Skeleton class="h-4 w-3/4" />
                            <Skeleton class="h-3 w-1/3" />
                        </div>
                    </div>
                </div>

                <!-- Empty state -->
                <div
                    v-else-if="groupedActivities.length === 0"
                    class="flex flex-col items-center justify-center gap-4 rounded-lg border py-16 text-center"
                >
                    <p class="text-lg font-semibold">{{ t('app.leads.detail.emptyActivityHeading') }}</p>
                    <p class="text-muted-foreground max-w-md text-sm">{{ t('app.leads.detail.emptyActivityBody') }}</p>
                    <Button @click="openNoteCompose">{{ t('app.leads.detail.emptyActivityCta') }}</Button>
                </div>

                <!-- Activity feed -->
                <ul v-else class="flex flex-col gap-4" aria-label="Activity feed">
                    <template v-for="group in groupedActivities" :key="group.dateLabel">
                        <!-- Date separator -->
                        <li role="separator" :aria-label="group.dateLabel" class="text-muted-foreground text-xs font-semibold uppercase">
                            {{ group.dateLabel }}
                        </li>

                        <!-- Activity entries -->
                        <li v-for="activity in group.items" :key="activity.id" class="flex items-start gap-3">
                            <!-- Icon -->
                            <span class="bg-muted mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full">
                                <UserPlus v-if="activity.type === 'assigned' || activity.type === 'unassigned'" class="h-4 w-4" />
                                <ArrowRight v-else-if="activity.type === 'status_change'" class="h-4 w-4" />
                                <RefreshCw v-else-if="activity.type === 'converted'" class="h-4 w-4" />
                                <MessageSquare v-else class="h-4 w-4" />
                            </span>

                            <div class="flex flex-1 flex-col gap-1">
                                <p class="text-sm font-medium">
                                    {{ activity.type === 'note' ? t('app.leads.detail.activityNote') : activityDescription(activity) }}
                                </p>
                                <p v-if="activity.type === 'note'" class="text-muted-foreground text-sm whitespace-pre-wrap">
                                    {{ activity.data?.note }}
                                </p>
                                <p class="text-muted-foreground text-xs">
                                    {{ t('app.leads.detail.activityActor', {
                                        name: activity.actor?.name ?? '—',
                                        time: formatTime(activity.created_at),
                                    }) }}
                                </p>
                            </div>
                        </li>
                    </template>
                </ul>
            </div>
        </div>
    </div>

    <!-- Assign Sheet -->
    <Sheet :open="assignSheetOpen" @update:open="(v) => { if (!v) assignSheetOpen = false; }">
        <SheetContent side="right" class="w-full sm:max-w-md">
            <SheetHeader>
                <SheetTitle>{{ t('app.leads.detail.sheetTitle') }}</SheetTitle>
            </SheetHeader>

            <div class="flex flex-col gap-4 overflow-y-auto px-4 py-4">
                <!-- Search -->
                <Input
                    v-model="assignSearch"
                    :placeholder="t('app.leads.detail.sheetSearch')"
                    type="search"
                />

                <!-- Results -->
                <div class="flex flex-col gap-1">
                    <template v-if="assignSearch.trim() === ''">
                        <p class="text-muted-foreground text-sm">{{ t('app.leads.detail.sheetEmptyDefault') }}</p>
                    </template>
                    <template v-else-if="filteredTeamMembers.length === 0">
                        <p class="text-muted-foreground text-sm">
                            {{ t('app.leads.detail.sheetEmptyNoResults', { query: assignSearch }) }}
                        </p>
                    </template>
                    <button
                        v-for="member in filteredTeamMembers"
                        :key="member.id"
                        type="button"
                        class="flex items-center justify-between rounded-md px-3 py-2 text-start text-sm transition-colors hover:bg-muted"
                        :class="selectedMemberId === member.id ? 'bg-muted font-medium' : ''"
                        @click="selectedMemberId = member.id"
                    >
                        <div>
                            <div>{{ member.name }}</div>
                            <div class="text-muted-foreground text-xs">{{ member.email }}</div>
                        </div>
                        <span
                            v-if="lead.assigned_to?.id === member.id"
                            class="text-muted-foreground text-xs"
                        >
                            {{ t('app.leads.detail.currentlyAssigned') }}
                        </span>
                    </button>
                </div>

                <InputError :message="assignForm.errors.user_id" />
            </div>

            <SheetFooter class="px-4 pb-6">
                <Button variant="outline" :disabled="assignForm.processing" @click="assignSheetOpen = false">
                    {{ t('app.leads.detail.sheetCancel') }}
                </Button>
                <Button
                    :disabled="selectedMemberId === null || assignForm.processing"
                    :aria-disabled="selectedMemberId === null"
                    @click="confirmAssign"
                >
                    <Spinner v-if="assignForm.processing" class="h-4 w-4" />
                    {{ t('app.leads.detail.sheetConfirm') }}
                </Button>
            </SheetFooter>
        </SheetContent>
    </Sheet>

    <!-- Convert to Contact Drawer -->
    <Sheet :open="convertStep === 'drawer'" @update:open="(v) => { if (!v) closeConvert(); }">
        <SheetContent side="right" class="w-full sm:max-w-md">
            <SheetHeader>
                <SheetTitle>{{ t('app.leads.conversion.drawerTitle') }}</SheetTitle>
                <p class="text-muted-foreground text-sm">{{ t('app.leads.conversion.drawerSubtitle') }}</p>
            </SheetHeader>

            <div class="flex flex-col gap-6 overflow-y-auto px-4 py-6">
                <!-- Contact type radio group -->
                <div role="radiogroup" aria-labelledby="convert-type-label" class="flex flex-col gap-3">
                    <p id="convert-type-label" class="text-sm font-medium">{{ t('app.leads.conversion.typeLabel') }}</p>

                    <label class="flex cursor-pointer items-start gap-3 rounded-md border p-3 transition-colors hover:bg-muted" :class="convertContactType === 'owner' ? 'border-primary bg-primary/5' : ''">
                        <input
                            v-model="convertContactType"
                            type="radio"
                            value="owner"
                            class="mt-0.5"
                        />
                        <div>
                            <p class="text-sm font-medium">{{ t('app.leads.conversion.typeOwner') }}</p>
                            <p class="text-muted-foreground text-xs">{{ t('app.leads.conversion.typeOwnerHint') }}</p>
                        </div>
                    </label>

                    <label class="flex cursor-pointer items-start gap-3 rounded-md border p-3 transition-colors hover:bg-muted" :class="convertContactType === 'resident' ? 'border-primary bg-primary/5' : ''">
                        <input
                            v-model="convertContactType"
                            type="radio"
                            value="resident"
                            class="mt-0.5"
                        />
                        <div>
                            <p class="text-sm font-medium">{{ t('app.leads.conversion.typeResident') }}</p>
                            <p class="text-muted-foreground text-xs">{{ t('app.leads.conversion.typeResidentHint') }}</p>
                        </div>
                    </label>
                </div>

                <!-- Data preview -->
                <div>
                    <p class="mb-3 text-sm font-medium">{{ t('app.leads.conversion.dataPreviewTitle') }}</p>
                    <dl class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-2 text-sm">
                        <dt class="text-muted-foreground">{{ t('app.leads.conversion.fieldFirstName') }}</dt>
                        <dd dir="ltr">{{ lead.name_en?.split(' ')[0] ?? '—' }}</dd>

                        <dt class="text-muted-foreground">{{ t('app.leads.conversion.fieldLastName') }}</dt>
                        <dd dir="ltr">{{ lead.name_en?.split(' ').slice(1).join(' ') || '—' }}</dd>

                        <dt class="text-muted-foreground">{{ t('app.leads.conversion.fieldEmail') }}</dt>
                        <dd dir="ltr">{{ lead.email ?? '—' }}</dd>

                        <dt class="text-muted-foreground">{{ t('app.leads.conversion.fieldPhone') }}</dt>
                        <dd dir="ltr">
                            {{ lead.phone_country_code ? `${lead.phone_country_code} ${lead.phone_number}` : lead.phone_number }}
                        </dd>
                    </dl>
                </div>

                <!-- Dedup check error -->
                <div v-if="dedupCheckError" role="alert" class="flex items-center gap-2 rounded-md border border-destructive/50 bg-destructive/10 px-3 py-2 text-sm text-destructive">
                    {{ dedupCheckError }}
                </div>

                <!-- General convert error -->
                <div v-if="convertError" role="alert" class="flex items-center gap-2 rounded-md border border-destructive/50 bg-destructive/10 px-3 py-2 text-sm text-destructive">
                    {{ convertError }}
                </div>
            </div>

            <SheetFooter class="px-4 pb-6">
                <Button variant="outline" :disabled="convertProcessing" @click="closeConvert">
                    {{ t('app.leads.conversion.cancel') }}
                </Button>
                <Button :disabled="convertProcessing" @click="handleConvertCta">
                    <Spinner v-if="convertProcessing" class="h-4 w-4" :aria-label="t('app.leads.conversion.convertCtaLoading')" />
                    {{ convertProcessing ? t('app.leads.conversion.convertCtaLoading') : t('app.leads.conversion.convertCta') }}
                </Button>
            </SheetFooter>
        </SheetContent>
    </Sheet>

    <!-- Dedup Warning Dialog -->
    <Dialog :open="convertStep === 'dedup'" @update:open="(v) => { if (!v) closeConvert(); }">
        <DialogContent role="alertdialog" class="sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>{{ t('app.leads.conversion.dedupTitle') }}</DialogTitle>
                <DialogDescription>{{ t('app.leads.conversion.dedupBody') }}</DialogDescription>
            </DialogHeader>

            <!-- Matched contact card -->
            <div v-if="dedupMatch" role="region" aria-label="Matched contact" class="rounded-md border p-3">
                <p class="font-medium">{{ dedupMatch.name }}</p>
                <p v-if="dedupMatch.email" class="text-muted-foreground text-sm" dir="ltr">{{ dedupMatch.email }}</p>
                <p v-if="dedupMatch.phone_number" class="text-muted-foreground text-sm" dir="ltr">{{ dedupMatch.phone_number }}</p>
                <Badge variant="secondary" class="mt-1 capitalize">{{ dedupMatch.type }}</Badge>
            </div>

            <!-- Link or create radio group -->
            <div role="radiogroup" class="flex flex-col gap-3 pt-2">
                <label class="flex cursor-pointer items-start gap-3 rounded-md border p-3 hover:bg-muted" :class="dedupChoice === 'link' ? 'border-primary bg-primary/5' : ''">
                    <input v-model="dedupChoice" type="radio" value="link" class="mt-0.5" />
                    <span class="text-sm">{{ t('app.leads.conversion.dedupLinkOption') }}</span>
                </label>
                <label class="flex cursor-pointer items-start gap-3 rounded-md border p-3 hover:bg-muted" :class="dedupChoice === 'create' ? 'border-primary bg-primary/5' : ''">
                    <input v-model="dedupChoice" type="radio" value="create" class="mt-0.5" />
                    <span class="text-sm">{{ t('app.leads.conversion.dedupCreateOption') }}</span>
                </label>
            </div>

            <DialogFooter>
                <Button variant="outline" @click="closeConvert">
                    {{ t('app.leads.conversion.cancel') }}
                </Button>
                <Button @click="handleDedupContinue">
                    {{ t('app.leads.conversion.dedupCta') }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>

    <!-- Link Confirm Dialog (non-destructive) -->
    <Dialog :open="convertStep === 'link-confirm'" @update:open="(v) => { if (!v) closeConvert(); }">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>{{ t('app.leads.conversion.linkConfirmTitle') }}</DialogTitle>
                <DialogDescription>{{ t('app.leads.conversion.linkConfirmBody') }}</DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" :disabled="convertProcessing" @click="closeConvert">
                    {{ t('app.leads.conversion.cancel') }}
                </Button>
                <Button :disabled="convertProcessing" @click="confirmLink">
                    <Spinner v-if="convertProcessing" class="h-4 w-4" />
                    {{ t('app.leads.conversion.linkConfirmCta') }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>

    <!-- Create Anyway Confirm Dialog (destructive alertdialog) -->
    <Dialog :open="convertStep === 'create-confirm'" @update:open="(v) => { if (!v) closeConvert(); }">
        <DialogContent role="alertdialog" class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>{{ t('app.leads.conversion.createAnywayTitle') }}</DialogTitle>
                <DialogDescription>{{ t('app.leads.conversion.createAnywayWarning') }}</DialogDescription>
            </DialogHeader>
            <DialogFooter class="flex-row-reverse sm:flex-row-reverse">
                <Button variant="destructive" :disabled="convertProcessing" @click="confirmCreateAnyway">
                    <Spinner v-if="convertProcessing" class="h-4 w-4" />
                    {{ t('app.leads.conversion.createAnywayCta') }}
                </Button>
                <Button variant="outline" :disabled="convertProcessing" @click="closeConvert">
                    {{ t('app.leads.conversion.cancel') }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>

    <!-- Delete Dialog -->
    <Dialog :open="deleteDialogOpen" @update:open="(v) => { if (!v) deleteDialogOpen = false; }">
        <DialogContent role="alertdialog">
            <DialogHeader>
                <DialogTitle>{{ t('app.leads.detail.deleteTitle') }}</DialogTitle>
                <DialogDescription>{{ t('app.leads.detail.deleteBody') }}</DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" :disabled="deleteForm.processing" @click="deleteDialogOpen = false">
                    {{ t('app.leads.detail.sheetCancel') }}
                </Button>
                <Button variant="destructive" :disabled="deleteForm.processing" @click="confirmDelete">
                    <Spinner v-if="deleteForm.processing" class="h-4 w-4" />
                    {{ t('app.leads.detail.deleteConfirm') }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
