<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, setLayoutProps, useForm } from '@inertiajs/vue3';
import { index, create, show, cancel } from '@/actions/App/Http/Controllers/VisitorAccess/VisitorInvitationController';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';

const { t } = useI18n();

interface Invitation {
    id: number;
    visitor_name: string;
    visitor_phone: string | null;
    visitor_purpose: string;
    expected_at: string | null;
    valid_until: string | null;
    status: string;
    qr_code_token: string;
}

const props = defineProps<{
    activeInvitations: Invitation[];
    pastInvitations: Invitation[];
}>();

watch(
    () => t('app.visitorAccess.myVisitors.pageTitle'),
    () => {
        setLayoutProps({
            breadcrumbs: [
                { title: t('app.navigation.dashboard'), href: '/dashboard' },
                { title: t('app.visitorAccess.myVisitors.pageTitle'), href: index.url() },
            ],
        });
    },
    { immediate: true },
);

const cancelDialogOpen = ref(false);
const invitationToCancel = ref<Invitation | null>(null);
const cancelForm = useForm({});

function openCancelDialog(invitation: Invitation) {
    invitationToCancel.value = invitation;
    cancelDialogOpen.value = true;
}

function closeCancelDialog() {
    cancelDialogOpen.value = false;
    invitationToCancel.value = null;
}

function confirmCancel() {
    if (! invitationToCancel.value) {
        return;
    }

    cancelForm.post(cancel.url(invitationToCancel.value.id), {
        onSuccess: () => closeCancelDialog(),
    });
}

function purposeLabel(purpose: string): string {
    const map: Record<string, string> = {
        visit: t('app.visitorAccess.myVisitors.purposeVisit'),
        delivery: t('app.visitorAccess.myVisitors.purposeDelivery'),
        service: t('app.visitorAccess.myVisitors.purposeService'),
        other: t('app.visitorAccess.myVisitors.purposeOther'),
    };

    return map[purpose] ?? purpose;
}

function statusVariant(status: string): 'default' | 'secondary' | 'outline' | 'destructive' {
    if (status === 'active' || status === 'used') {
        return 'default';
    }

    if (status === 'expired') {
        return 'outline';
    }

    return 'secondary';
}

function statusLabel(status: string): string {
    const map: Record<string, string> = {
        active: t('app.visitorAccess.myVisitors.statusActive'),
        used: t('app.visitorAccess.myVisitors.statusUsed'),
        expired: t('app.visitorAccess.myVisitors.statusExpired'),
        cancelled: t('app.visitorAccess.myVisitors.statusCancelled'),
    };

    return map[status] ?? status;
}

function formatDateTime(iso: string | null): string {
    if (! iso) {
        return t('app.common.notAvailable');
    }

    return new Intl.DateTimeFormat(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(iso));
}
</script>

<template>
    <Head :title="t('app.visitorAccess.myVisitors.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading
                variant="small"
                :title="t('app.visitorAccess.myVisitors.pageTitle')"
                :description="t('app.visitorAccess.myVisitors.description')"
            />
            <Button as-child>
                <Link :href="create.url()">
                    {{ t('app.visitorAccess.myVisitors.registerCta') }}
                </Link>
            </Button>
        </div>

        <!-- Empty state -->
        <div
            v-if="props.activeInvitations.length === 0 && props.pastInvitations.length === 0"
            class="flex flex-col items-center justify-center gap-4 py-16 text-center"
        >
            <p class="text-muted-foreground text-sm">{{ t('app.visitorAccess.myVisitors.emptyHeading') }}</p>
            <Button as-child>
                <Link :href="create.url()">
                    {{ t('app.visitorAccess.myVisitors.registerCta') }}
                </Link>
            </Button>
        </div>

        <!-- Active invitations -->
        <div v-if="props.activeInvitations.length > 0" class="flex flex-col gap-3">
            <h2 class="text-sm font-semibold">
                {{ t('app.visitorAccess.myVisitors.activeSection') }} ({{ props.activeInvitations.length }})
            </h2>

            <Card v-for="invitation in props.activeInvitations" :key="invitation.id">
                <CardContent class="flex items-start justify-between gap-4 p-4">
                    <div class="flex flex-col gap-1 text-sm">
                        <p class="font-medium">{{ invitation.visitor_name }}</p>
                        <p class="text-muted-foreground">
                            {{ purposeLabel(invitation.visitor_purpose) }} ·
                            {{ formatDateTime(invitation.expected_at) }}
                        </p>
                        <p v-if="invitation.visitor_phone" class="text-muted-foreground" dir="ltr">
                            {{ invitation.visitor_phone }}
                        </p>
                    </div>
                    <div class="flex shrink-0 flex-col items-end gap-2">
                        <Badge
                            variant="default"
                            :aria-label="t('app.visitorAccess.myVisitors.statusAriaLabel', { status: t('app.visitorAccess.myVisitors.statusActive') })"
                        >
                            {{ t('app.visitorAccess.myVisitors.statusActive') }}
                        </Badge>
                        <Button variant="ghost" size="sm" as-child>
                            <Link :href="show.url(invitation.id)">
                                {{ t('app.visitorAccess.myVisitors.viewQr') }}
                            </Link>
                        </Button>
                        <Button variant="outline" size="sm" @click="openCancelDialog(invitation)">
                            {{ t('app.visitorAccess.myVisitors.cancelCta') }}
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Past invitations -->
        <div v-if="props.pastInvitations.length > 0" class="flex flex-col gap-3">
            <h2 class="text-sm font-semibold">
                {{ t('app.visitorAccess.myVisitors.pastSection') }} ({{ props.pastInvitations.length }})
            </h2>

            <Card v-for="invitation in props.pastInvitations" :key="invitation.id">
                <CardContent class="flex items-start justify-between gap-4 p-4">
                    <div class="flex flex-col gap-1 text-sm">
                        <p class="font-medium">{{ invitation.visitor_name }}</p>
                        <p class="text-muted-foreground">
                            {{ purposeLabel(invitation.visitor_purpose) }} ·
                            {{ formatDateTime(invitation.expected_at) }}
                        </p>
                    </div>
                    <div class="shrink-0">
                        <Badge
                            :variant="statusVariant(invitation.status)"
                            :aria-label="t('app.visitorAccess.myVisitors.statusAriaLabel', { status: statusLabel(invitation.status) })"
                        >
                            {{ statusLabel(invitation.status) }}
                        </Badge>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Cancel confirmation dialog -->
        <Dialog :open="cancelDialogOpen" @update:open="(val) => { if (!val) closeCancelDialog(); }">
            <DialogContent role="alertdialog" aria-modal="true">
                <DialogHeader>
                    <DialogTitle>{{ t('app.visitorAccess.myVisitors.cancelDialogTitle') }}</DialogTitle>
                    <DialogDescription>
                        <span v-if="invitationToCancel">
                            {{ invitationToCancel.visitor_name }} ·
                            {{ purposeLabel(invitationToCancel.visitor_purpose) }} ·
                            {{ formatDateTime(invitationToCancel.expected_at) }}
                        </span>
                    </DialogDescription>
                </DialogHeader>
                <p class="text-muted-foreground text-sm">
                    {{ t('app.visitorAccess.myVisitors.cancelDialogBody') }}
                </p>
                <DialogFooter>
                    <Button variant="outline" :disabled="cancelForm.processing" @click="closeCancelDialog">
                        {{ t('app.visitorAccess.myVisitors.keepCta') }}
                    </Button>
                    <Button variant="destructive" :disabled="cancelForm.processing" @click="confirmCancel">
                        {{ t('app.visitorAccess.myVisitors.cancelCta') }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
