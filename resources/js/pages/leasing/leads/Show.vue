<script setup lang="ts">
import { Head, router, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, ref, watchEffect } from 'vue';
import { MoreHorizontal, UserPlus, ArrowRight, MessageSquare } from 'lucide-vue-next';
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
    status: LeadStatus | null;
    source: { id: number; name: string; name_en: string | null; name_ar: string | null } | null;
    assigned_to: { id: number; name: string; email: string } | null;
};

const props = defineProps<{
    lead: LeadDetail;
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
        default:
            return '';
    }
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

        <!-- Status + Pipeline Card -->
        <Card>
            <CardContent class="pt-6">
                <div class="grid gap-4">
                    <div class="grid gap-2">
                        <Label>{{ t('app.leads.detail.statusLabel') }}</Label>
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
                    </div>

                    <!-- Lost reason - visible only when Lost selected -->
                    <div v-if="isLostSelected" class="grid gap-2">
                        <Label>{{ t('app.leads.detail.lostReasonLabel') }}</Label>
                        <Textarea
                            v-model="statusForm.lost_reason"
                            dir="auto"
                            :placeholder="t('app.leads.detail.lostReasonPlaceholder')"
                            :maxlength="500"
                            rows="3"
                            aria-label="Lost reason"
                        />
                        <p class="text-muted-foreground text-xs" aria-live="polite">
                            {{ t('app.leads.detail.lostReasonCounter', { n: statusForm.lost_reason.length }) }}
                        </p>
                    </div>

                    <!-- Dirty-state bar -->
                    <div
                        v-if="isStatusDirty"
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
