<script setup lang="ts">
import { Head, Link, router, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, ref, watchEffect } from 'vue';
import { approve as approveAction, reject as rejectAction } from '@/actions/App/Http/Controllers/Leasing/ApprovalController';
import { amend as amendAction } from '@/actions/App/Http/Controllers/Leasing/LeaseController';
import { index as noticesIndex } from '@/actions/App/Http/Controllers/Leasing/LeaseNoticeController';
import { Badge } from '@/components/ui/badge';
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
import { Textarea } from '@/components/ui/textarea';
import { useI18n } from '@/composables/useI18n';
import type { Lease } from '@/types';

const props = defineProps<{
    lease: Lease;
    canApprove: boolean;
    canAmend: boolean;
    isPendingApplication: boolean;
    noticesCount: number;
}>();

const { t } = useI18n();

const tenantName = computed(() => {
    if (props.lease.tenant?.name) {
        return props.lease.tenant.name;
    }

    const firstName = props.lease.tenant?.first_name ?? '';
    const lastName = props.lease.tenant?.last_name ?? '';

    return `${firstName} ${lastName}`.trim();
});

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.leases.pageTitle'), href: '/leases' },
            { title: t('app.leases.show.breadcrumb'), href: '#' },
        ],
    });
});

// ── Approve ────────────────────────────────────────────────────────────────────
const approveForm = useForm({});

function handleApprove() {
    approveForm.post(approveAction.url(props.lease.id));
}

// ── Reject dialog ──────────────────────────────────────────────────────────────
const rejectDialogOpen = ref(false);

const rejectForm = useForm({
    rejection_reason: '',
});

function openRejectDialog() {
    rejectDialogOpen.value = true;
}

function closeRejectDialog() {
    rejectDialogOpen.value = false;
    rejectForm.reset();
    rejectForm.clearErrors();
}

function confirmReject() {
    rejectForm.post(rejectAction.url(props.lease.id), {
        onSuccess: () => closeRejectDialog(),
    });
}

// ── Helpers ────────────────────────────────────────────────────────────────────
function deleteLease() {
    if (confirm(t('app.leases.show.confirmDeletePrompt'))) {
        router.delete(`/leases/${props.lease.id}`);
    }
}
</script>

<template>
    <div>
        <Head :title="t('app.leases.show.pageTitle', { contract: lease.contract_number })" />

        <div class="flex flex-col gap-6 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight">{{ lease.contract_number }}</h2>
                    <p class="text-muted-foreground text-sm">{{ tenantName || t('app.common.notAvailable') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <Button v-if="!lease.is_sub_lease" variant="secondary" as-child>
                        <Link :href="`/leases/${lease.id}/subleases/create`">{{ t('app.leases.show.createSublease') }}</Link>
                    </Button>
                    <Button v-if="canAmend" variant="secondary" as-child>
                        <Link :href="amendAction.url(lease.id)">{{ t('app.leases.amend.pageTitle') }}</Link>
                    </Button>
                    <Button variant="secondary" as-child>
                        <Link :href="noticesIndex.url(lease.id)">
                            {{ t('app.leases.notices.title') }}
                            <Badge v-if="noticesCount > 0" class="ms-1">{{ noticesCount }}</Badge>
                        </Link>
                    </Button>
                    <Button variant="outline" as-child>
                        <Link :href="`/leases/${lease.id}/edit`">{{ t('app.actions.edit') }}</Link>
                    </Button>
                    <Button variant="destructive" @click="deleteLease">{{ t('app.actions.delete') }}</Button>
                </div>
            </div>

            <!-- Approval Actions — visible only to managers when lease is pending -->
            <Card v-if="canApprove && isPendingApplication" class="border-amber-300 bg-amber-50 dark:bg-amber-950/20">
                <CardHeader>
                    <CardTitle>{{ t('app.leases.approval.pendingReview') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-3">
                        <Button
                            class="bg-green-600 hover:bg-green-700 focus-visible:ring-green-600"
                            :disabled="approveForm.processing"
                            @click="handleApprove"
                        >
                            {{ t('app.leases.approval.approve') }}
                        </Button>
                        <Button
                            variant="destructive"
                            :disabled="approveForm.processing"
                            @click="openRejectDialog"
                        >
                            {{ t('app.leases.approval.reject') }}
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Approval Timeline — shown after a decision -->
            <Card v-if="lease.approved_at || lease.rejected_at">
                <CardHeader>
                    <CardTitle>{{ t('app.leases.approval.timeline') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <ol class="space-y-2 text-sm">
                        <li v-if="lease.approved_at" class="flex items-start gap-2">
                            <Badge class="bg-green-600 text-white">{{ t('app.leases.approval.approvedStatus') }}</Badge>
                            <div>
                                <span class="font-medium">{{ t('app.leases.approval.approvedBy') }}</span>
                                {{ lease.approved_by?.name ?? '—' }}
                                <span class="text-muted-foreground ms-1">{{ lease.approved_at }}</span>
                            </div>
                        </li>
                        <li v-if="lease.rejected_at" class="flex flex-col gap-1">
                            <div class="flex items-start gap-2">
                                <Badge variant="destructive">{{ t('app.leases.approval.rejectedStatus') }}</Badge>
                                <div>
                                    <span class="font-medium">{{ t('app.leases.approval.rejectedBy') }}</span>
                                    {{ lease.rejected_by?.name ?? '—' }}
                                    <span class="text-muted-foreground ms-1">{{ lease.rejected_at }}</span>
                                </div>
                            </div>
                            <p v-if="lease.rejection_reason" class="text-muted-foreground ms-6 mt-1 italic">
                                {{ lease.rejection_reason }}
                            </p>
                        </li>
                    </ol>
                </CardContent>
            </Card>

            <Card v-if="lease.is_sub_lease && lease.parent_lease">
                <CardHeader><CardTitle>{{ t('app.leases.show.parentLease') }}</CardTitle></CardHeader>
                <CardContent>
                    <Link :href="`/leases/${lease.parent_lease.id}`" class="text-primary underline">
                        {{ lease.parent_lease.contract_number }}
                    </Link>
                </CardContent>
            </Card>

            <div class="grid gap-4 md:grid-cols-4">
                <Card>
                    <CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.leases.show.status') }}</CardTitle></CardHeader>
                    <CardContent><Badge>{{ lease.status?.name ?? '—' }}</Badge></CardContent>
                </Card>
                <Card>
                    <CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.leases.show.totalAmount') }}</CardTitle></CardHeader>
                    <CardContent><div class="text-2xl font-bold">{{ lease.rental_total_amount }}</div></CardContent>
                </Card>
                <Card>
                    <CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.leases.show.unpaid') }}</CardTitle></CardHeader>
                    <CardContent><div class="text-2xl font-bold text-destructive">{{ lease.total_unpaid_amount ?? '0' }}</div></CardContent>
                </Card>
                <Card>
                    <CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.leases.show.type') }}</CardTitle></CardHeader>
                    <CardContent><Badge variant="secondary">{{ lease.tenant_type }}</Badge></CardContent>
                </Card>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <Card>
                    <CardHeader><CardTitle>{{ t('app.leases.show.duration') }}</CardTitle></CardHeader>
                    <CardContent class="space-y-2">
                        <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.leases.show.start') }}</span><span>{{ lease.start_date }}</span></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.leases.show.end') }}</span><span>{{ lease.end_date }}</span></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.leases.show.handover') }}</span><span>{{ lease.handover_date }}</span></div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader><CardTitle>{{ t('app.leases.show.financial') }}</CardTitle></CardHeader>
                    <CardContent class="space-y-2">
                        <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.leases.show.rentalType') }}</span><span>{{ lease.rental_type }}</span></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.leases.show.securityDeposit') }}</span><span>{{ lease.security_deposit_amount ?? '—' }}</span></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.leases.show.subLease') }}</span><Badge :variant="lease.is_sub_lease ? 'default' : 'secondary'">{{ lease.is_sub_lease ? t('app.common.yes') : t('app.common.no') }}</Badge></div>
                    </CardContent>
                </Card>
            </div>

            <Card v-if="lease.units && lease.units.length > 0">
                <CardHeader><CardTitle>{{ t('app.leases.show.units') }}</CardTitle></CardHeader>
                <CardContent>
                    <div class="space-y-2">
                        <Link
                            v-for="unit in lease.units"
                            :key="unit.id"
                            :href="`/units/${unit.id}`"
                            class="flex items-center justify-between rounded-md border p-3 hover:bg-muted/50"
                        >
                            <span class="font-medium">{{ unit.name }}</span>
                        </Link>
                    </div>
                </CardContent>
            </Card>

            <Card v-if="lease.additional_fees && lease.additional_fees.length > 0">
                <CardHeader><CardTitle>{{ t('app.leases.show.additionalFees') }}</CardTitle></CardHeader>
                <CardContent>
                    <div class="space-y-2">
                        <div v-for="fee in lease.additional_fees" :key="fee.id" class="flex items-center justify-between rounded-md border p-3">
                            <span class="font-medium">{{ fee.name ?? fee.description ?? `Fee #${fee.id}` }}</span>
                            <span class="text-muted-foreground text-sm">{{ fee.amount ?? '—' }}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card v-if="lease.escalations && lease.escalations.length > 0">
                <CardHeader><CardTitle>{{ t('app.leases.show.escalations') }}</CardTitle></CardHeader>
                <CardContent>
                    <div class="space-y-2">
                        <div v-for="esc in lease.escalations" :key="esc.id" class="flex items-center justify-between rounded-md border p-3">
                            <span class="font-medium">{{ esc.type ?? `Escalation #${esc.id}` }}</span>
                            <span class="text-muted-foreground text-sm">{{ esc.rate ?? esc.amount ?? '—' }}{{ esc.rate ? '%' : '' }}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card v-if="lease.subleases && lease.subleases.length > 0">
                <CardHeader><CardTitle>{{ t('app.leases.show.subLeases') }}</CardTitle></CardHeader>
                <CardContent>
                    <div class="space-y-2">
                        <Link
                            v-for="sublease in lease.subleases"
                            :key="sublease.id"
                            :href="`/leases/${sublease.id}`"
                            class="flex items-center justify-between rounded-md border p-3 hover:bg-muted/50"
                        >
                            <span class="font-medium">{{ sublease.contract_number }}</span>
                            <span class="text-muted-foreground text-sm">{{ sublease.status?.name_en ?? sublease.status?.name ?? '—' }}</span>
                        </Link>
                    </div>
                </CardContent>
            </Card>

            <div class="grid gap-4 md:grid-cols-2">
                <Card v-if="lease.created_by">
                    <CardHeader><CardTitle>{{ t('app.leases.show.createdBy') }}</CardTitle></CardHeader>
                    <CardContent>
                        <span>{{ lease.created_by.first_name }} {{ lease.created_by.last_name }}</span>
                    </CardContent>
                </Card>
                <Card v-if="lease.deal_owner">
                    <CardHeader><CardTitle>{{ t('app.leases.show.dealOwner') }}</CardTitle></CardHeader>
                    <CardContent>
                        <span>{{ lease.deal_owner.first_name }} {{ lease.deal_owner.last_name }}</span>
                    </CardContent>
                </Card>
            </div>

            <!-- Amendment History -->
            <Card v-if="lease.amendments && lease.amendments.length > 0">
                <CardHeader>
                    <CardTitle>{{ t('app.leases.amend.historyTitle') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <ol class="space-y-4">
                        <li v-for="amendment in lease.amendments" :key="amendment.id" class="border-s-2 border-muted ps-4">
                            <div class="mb-1 flex items-center gap-2">
                                <span class="text-sm font-semibold">
                                    {{ t('app.leases.amend.historyDetail', { n: amendment.amendment_number }) }}
                                </span>
                                <span class="text-muted-foreground text-xs">{{ amendment.created_at }}</span>
                            </div>
                            <p class="text-muted-foreground text-xs">
                                {{ t('app.leases.amend.historyMadeBy', { name: amendment.amended_by?.name ?? '—' }) }}
                            </p>
                            <p v-if="amendment.reason" class="mt-1 text-sm italic">{{ amendment.reason }}</p>
                            <table v-if="amendment.changes && Object.keys(amendment.changes).length > 0" class="mt-2 w-full text-sm" aria-label="Amendment changes">
                                <thead>
                                    <tr class="text-muted-foreground text-xs">
                                        <th class="pb-1 text-start font-medium">{{ t('app.common.field') }}</th>
                                        <th class="pb-1 text-start font-medium">{{ t('app.leases.amend.currentLabel') }}</th>
                                        <th class="pb-1 text-start font-medium">{{ t('app.leases.amend.newLabel') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(diff, field) in amendment.changes"
                                        :key="field"
                                        :aria-label="`${field} changed from ${diff.from} to ${diff.to}`"
                                    >
                                        <td class="py-0.5 font-mono text-xs">{{ field }}</td>
                                        <td class="py-0.5 text-muted-foreground">{{ diff.from ?? '—' }}</td>
                                        <td class="py-0.5">{{ diff.to ?? '—' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <p v-if="amendment.addendum_media_id" class="mt-1 text-xs text-green-600">
                                {{ t('app.leases.amend.historyAddendumSigned') }}
                            </p>
                            <p v-else class="text-muted-foreground mt-1 text-xs">
                                {{ t('app.leases.amend.historyNoAddendum') }}
                            </p>
                        </li>
                    </ol>
                </CardContent>
            </Card>
        </div>

        <!-- Reject Dialog -->
        <Dialog :open="rejectDialogOpen" @update:open="(v) => { if (!v) closeRejectDialog() }">
            <DialogContent class="sm:max-w-md" aria-labelledby="reject-dialog-title">
                <DialogHeader>
                    <DialogTitle id="reject-dialog-title">{{ t('app.leases.approval.rejectTitle') }}</DialogTitle>
                    <DialogDescription>{{ t('app.leases.approval.rejectDesc') }}</DialogDescription>
                </DialogHeader>

                <div class="space-y-2">
                    <label for="rejection-reason" class="text-sm font-medium">
                        {{ t('app.leases.approval.rejectReason') }} *
                    </label>
                    <Textarea
                        id="rejection-reason"
                        v-model="rejectForm.rejection_reason"
                        :placeholder="t('app.leases.approval.rejectReasonPlaceholder')"
                        rows="4"
                        autofocus
                    />
                    <div v-if="rejectForm.errors.rejection_reason" role="alert" class="text-destructive text-sm">
                        {{ rejectForm.errors.rejection_reason }}
                    </div>
                </div>

                <DialogFooter class="gap-2">
                    <Button variant="outline" :disabled="rejectForm.processing" @click="closeRejectDialog">
                        {{ t('app.leases.approval.cancel') }}
                    </Button>
                    <Button
                        variant="destructive"
                        :disabled="rejectForm.processing || rejectForm.rejection_reason.length < 10"
                        @click="confirmReject"
                    >
                        {{ t('app.leases.approval.confirmReject') }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
