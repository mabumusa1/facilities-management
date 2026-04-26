<script setup lang="ts">
import { Head, useForm, setLayoutProps } from '@inertiajs/vue3';
import { ref, watchEffect } from 'vue';
import { index, show, assign, addNote } from '@/actions/App/Http/Controllers/Services/AdminServiceRequestController';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { useI18n } from '@/composables/useI18n';

type LocalCategory = {
    id: number;
    name_en: string;
    name_ar: string;
};

type LocalStatus = {
    id: number;
    name: string;
    name_en: string | null;
    name_ar: string | null;
};

type AdminServiceRequestDetail = {
    id: number;
    request_code: string | null;
    urgency: string;
    priority: string | null;
    room_location: string | null;
    description: string | null;
    sla_response_due_at: string | null;
    sla_resolution_due_at: string | null;
    assigned_to_user_id: number | null;
    assigned_at: string | null;
    created_at: string | null;
    category: LocalCategory | null;
    subcategory: LocalCategory | null;
    status: LocalStatus | null;
    unit: { id: number; name: string } | null;
    community: { id: number; name: string } | null;
    assigned_to: { id: number; name: string } | null;
    requester_name: string | null;
    requester_phone: string | null;
};

type InternalNote = {
    id: number;
    body: string;
    sender_name: string | null;
    created_at: string | null;
};

type Assignee = {
    id: number;
    name: string;
};

const props = defineProps<{
    serviceRequest: AdminServiceRequestDetail;
    internalNotes: InternalNote[];
    assignees: Assignee[];
    priorityOptions: string[];
}>();

const { isArabic, t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.serviceRequests.triagePageTitle'), href: index.url() },
            { title: props.serviceRequest.request_code ?? String(props.serviceRequest.id), href: show.url(props.serviceRequest.id) },
        ],
    });
});

const assignForm = useForm({
    assigned_to_user_id: props.serviceRequest.assigned_to_user_id ? String(props.serviceRequest.assigned_to_user_id) : '',
    priority: props.serviceRequest.priority ?? '',
});

const noteForm = useForm({
    body: '',
});

const showNotesHistory = ref(true);

function localizedName(item: LocalCategory): string {
    return isArabic.value ? item.name_ar : item.name_en;
}

function localizedStatusName(status: LocalStatus): string {
    if (isArabic.value) {
        return status.name_ar ?? status.name_en ?? status.name;
    }
    return status.name_en ?? status.name;
}

function formatDate(isoString: string | null): string {
    if (! isoString) return '—';
    return new Date(isoString).toLocaleDateString(isArabic.value ? 'ar' : 'en', {
        dateStyle: 'medium',
        timeStyle: 'short',
    });
}

function priorityLabel(priority: string): string {
    const map: Record<string, string> = {
        low: t('app.serviceRequests.priorityLow'),
        medium: t('app.serviceRequests.priorityMedium'),
        high: t('app.serviceRequests.priorityHigh'),
        urgent: t('app.serviceRequests.priorityUrgent'),
    };
    return map[priority] ?? priority;
}

function submitAssign() {
    assignForm.transform((data) => ({
        ...data,
        assigned_to_user_id: data.assigned_to_user_id ? Number(data.assigned_to_user_id) : null,
        priority: data.priority || null,
    })).patch(assign.url(props.serviceRequest.id), {
        preserveScroll: true,
    });
}

function submitNote() {
    noteForm.post(addNote.url(props.serviceRequest.id), {
        preserveScroll: true,
        onSuccess: () => noteForm.reset(),
    });
}

const isSlaOverdue = props.serviceRequest.sla_response_due_at
    && new Date(props.serviceRequest.sla_response_due_at) < new Date()
    && ! props.serviceRequest.assigned_to_user_id;
</script>

<template>
    <Head :title="serviceRequest.request_code ?? t('app.serviceRequests.triagePageTitle')" />

    <div class="mx-auto max-w-5xl p-4 space-y-6">
        <!-- Header -->
        <div class="flex items-start justify-between gap-4">
            <div>
                <bdi
                    dir="ltr"
                    class="text-2xl font-bold font-mono text-primary"
                >
                    {{ serviceRequest.request_code }}
                </bdi>
                <p class="text-muted-foreground text-sm mt-1">
                    {{ t('app.serviceRequests.detailSubmitted') }}: {{ formatDate(serviceRequest.created_at) }}
                </p>
            </div>
            <Badge
                v-if="isSlaOverdue"
                variant="destructive"
                class="text-sm"
            >
                ⚠ {{ t('app.serviceRequests.slaOverdue') }}
            </Badge>
        </div>

        <!-- Summary cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <!-- Resident -->
            <div class="rounded-lg border bg-card p-4">
                <p class="text-xs font-medium text-muted-foreground uppercase">
                    {{ t('app.serviceRequests.detailResident') }}
                </p>
                <p class="mt-1 font-semibold">{{ serviceRequest.requester_name ?? '—' }}</p>
                <p
                    v-if="serviceRequest.requester_phone"
                    class="text-sm text-muted-foreground"
                >
                    {{ serviceRequest.requester_phone }}
                </p>
            </div>

            <!-- Location -->
            <div class="rounded-lg border bg-card p-4">
                <p class="text-xs font-medium text-muted-foreground uppercase">
                    {{ t('app.serviceRequests.detailLocation') }}
                </p>
                <p class="mt-1 font-semibold">
                    {{ serviceRequest.community?.name ?? '—' }}
                    <template v-if="serviceRequest.unit">
                        · {{ serviceRequest.unit.name }}
                    </template>
                </p>
                <p
                    v-if="serviceRequest.room_location"
                    class="text-sm text-muted-foreground"
                >
                    {{ serviceRequest.room_location }}
                </p>
            </div>

            <!-- Category -->
            <div class="rounded-lg border bg-card p-4">
                <p class="text-xs font-medium text-muted-foreground uppercase">
                    {{ t('app.serviceRequests.detailCategory') }}
                </p>
                <p class="mt-1 font-semibold">
                    {{ serviceRequest.category ? localizedName(serviceRequest.category) : '—' }}
                </p>
                <p
                    v-if="serviceRequest.subcategory"
                    class="text-sm text-muted-foreground"
                >
                    {{ localizedName(serviceRequest.subcategory) }}
                </p>
            </div>

            <!-- Urgency -->
            <div class="rounded-lg border bg-card p-4">
                <p class="text-xs font-medium text-muted-foreground uppercase">
                    {{ t('app.serviceRequests.detailUrgency') }}
                </p>
                <div class="mt-1">
                    <Badge :variant="serviceRequest.urgency === 'urgent' ? 'destructive' : 'secondary'">
                        {{ serviceRequest.urgency === 'urgent'
                            ? t('app.serviceRequests.urgencyUrgent')
                            : t('app.serviceRequests.urgencyNormal') }}
                    </Badge>
                </div>
            </div>

            <!-- Status -->
            <div class="rounded-lg border bg-card p-4">
                <p class="text-xs font-medium text-muted-foreground uppercase">
                    {{ t('app.serviceRequests.detailStatus') }}
                </p>
                <div class="mt-1">
                    <Badge
                        v-if="serviceRequest.status"
                        variant="outline"
                    >
                        {{ localizedStatusName(serviceRequest.status) }}
                    </Badge>
                    <span
                        v-else
                        class="text-muted-foreground"
                    >—</span>
                </div>
            </div>

            <!-- Currently assigned -->
            <div class="rounded-lg border bg-card p-4">
                <p class="text-xs font-medium text-muted-foreground uppercase">
                    {{ t('app.serviceRequests.colAssigned') }}
                </p>
                <p class="mt-1 font-semibold">
                    {{ serviceRequest.assigned_to?.name ?? '—' }}
                </p>
            </div>
        </div>

        <!-- Description -->
        <div class="rounded-lg border bg-card p-4 space-y-2">
            <p class="text-sm font-medium">{{ t('app.serviceRequests.detailDescription') }}</p>
            <p class="text-sm text-foreground whitespace-pre-wrap">{{ serviceRequest.description ?? '—' }}</p>
        </div>

        <!-- Triage actions -->
        <div class="rounded-lg border bg-card p-4 space-y-4">
            <p class="font-semibold">{{ t('app.serviceRequests.assignTechnicianLabel') }}</p>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Assignee picker -->
                <div class="flex flex-col gap-1">
                    <Label>{{ t('app.serviceRequests.assignTechnicianLabel') }}</Label>
                    <Select v-model="assignForm.assigned_to_user_id">
                        <SelectTrigger>
                            <SelectValue :placeholder="t('app.serviceRequests.assignPlaceholder')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="assignee in assignees"
                                :key="assignee.id"
                                :value="String(assignee.id)"
                            >
                                {{ assignee.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="assignForm.errors.assigned_to_user_id" />
                </div>

                <!-- Priority picker -->
                <div class="flex flex-col gap-1">
                    <Label>{{ t('app.serviceRequests.priorityLabel') }}</Label>
                    <Select v-model="assignForm.priority">
                        <SelectTrigger>
                            <SelectValue :placeholder="t('app.serviceRequests.priorityLabel')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="option in priorityOptions"
                                :key="option"
                                :value="option"
                            >
                                {{ priorityLabel(option) }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="assignForm.errors.priority" />
                </div>
            </div>

            <!-- Sticky action bar -->
            <div class="sticky bottom-0 flex justify-end gap-3 border-t bg-card pt-4">
                <Button
                    variant="outline"
                    :href="index.url()"
                    as="a"
                >
                    {{ t('app.serviceRequests.backToList') }}
                </Button>
                <Button
                    :disabled="assignForm.processing || ! assignForm.assigned_to_user_id"
                    @click="submitAssign"
                >
                    <span v-if="assignForm.processing">{{ t('app.serviceRequests.submitting') }}</span>
                    <span v-else>{{ t('app.serviceRequests.assignButton') }}</span>
                </Button>
            </div>
        </div>

        <!-- Internal notes -->
        <div class="rounded-lg border bg-card p-4 space-y-4">
            <p class="font-semibold">{{ t('app.serviceRequests.internalNotesLabel') }}</p>

            <div class="flex flex-col gap-2">
                <Label>{{ t('app.serviceRequests.internalNotesLabel') }}</Label>
                <Textarea
                    v-model="noteForm.body"
                    dir="auto"
                    :placeholder="t('app.serviceRequests.notePlaceholder')"
                    :aria-label="t('app.serviceRequests.internalNotesLabel')"
                    rows="3"
                />
                <InputError :message="noteForm.errors.body" />
                <div class="flex justify-end">
                    <Button
                        variant="secondary"
                        :disabled="noteForm.processing || ! noteForm.body.trim()"
                        @click="submitNote"
                    >
                        {{ t('app.serviceRequests.addNoteButton') }}
                    </Button>
                </div>
            </div>

            <!-- Notes history -->
            <div
                v-if="internalNotes.length > 0"
                role="log"
                :aria-label="t('app.serviceRequests.notesHistoryLabel')"
                class="space-y-3"
            >
                <button
                    class="flex items-center gap-2 text-sm font-medium text-muted-foreground hover:text-foreground transition-colors"
                    @click="showNotesHistory = ! showNotesHistory"
                >
                    ▾ {{ t('app.serviceRequests.notesHistoryLabel') }} ({{ internalNotes.length }})
                </button>

                <div
                    v-if="showNotesHistory"
                    class="space-y-3"
                >
                    <div
                        v-for="note in internalNotes"
                        :key="note.id"
                        class="rounded-md border bg-muted/30 p-3 text-sm"
                    >
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="font-medium">{{ note.sender_name ?? '—' }}</span>
                            <span class="text-xs text-muted-foreground">{{ formatDate(note.created_at) }}</span>
                        </div>
                        <p
                            dir="auto"
                            class="text-foreground whitespace-pre-wrap"
                        >
                            {{ note.body }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
