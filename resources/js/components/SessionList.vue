<script setup lang="ts">
import { Monitor, Smartphone, Globe, Clock, X, ShieldAlert } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import { useSessions } from '@/composables/useSessions';
import { useI18n } from '@/composables/useI18n';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import { Skeleton } from '@/components/ui/skeleton';
import { Spinner } from '@/components/ui/spinner';
import { cn } from '@/lib/utils';
import type { ActiveSession } from '@/types';

const { t } = useI18n();
const { sessions, loading, revoking, revokingAll, error, fetchSessions, revokeSession, revokeAllSessions } = useSessions();

const confirmRevokeTarget = ref<ActiveSession | null>(null);
const showRevokeAllDialog = ref<boolean>(false);
const showRevokeSingleDialog = ref<boolean>(false);

onMounted(() => {
    fetchSessions();
});

const getDeviceIcon = (device: string) => {
    const lower = device.toLowerCase();
    if (lower.includes('mobile') || lower.includes('phone')) return Smartphone;
    if (lower.includes('tablet')) return Smartphone;

    return Monitor;
};

const handleRevokeSingle = async () => {
    if (!confirmRevokeTarget.value) return;

    const ok = await revokeSession(confirmRevokeTarget.value.id);
    if (ok) {
        showRevokeSingleDialog.value = false;
        confirmRevokeTarget.value = null;
    }
};

const handleRevokeAll = async () => {
    const ok = await revokeAllSessions();
    if (ok) {
        showRevokeAllDialog.value = false;
    }
};

const openRevokeSingleDialog = (session: ActiveSession) => {
    confirmRevokeTarget.value = session;
    showRevokeSingleDialog.value = true;
};
</script>

<template>
    <div class="space-y-6">
        <div class="space-y-1">
            <h2 class="text-lg font-semibold tracking-tight">
                {{ t('app.settings.security.sessions.title') }}
            </h2>
            <p class="text-sm text-muted-foreground">
                {{ t('app.settings.security.sessions.description') }}
            </p>
        </div>

        <div
            v-if="error"
            class="flex items-center gap-2 rounded-md border border-destructive/50 bg-destructive/10 p-4"
            role="alert"
        >
            <ShieldAlert class="size-4 text-destructive" />
            <p class="text-sm text-destructive">{{ t('app.settings.security.sessions.driverNotConfigured') }}</p>
        </div>

        <div v-if="loading" class="space-y-3" role="list">
            <div
                v-for="i in 3"
                :key="i"
                class="flex items-center justify-between rounded-lg border p-4"
            >
                <div class="flex items-center gap-3">
                    <Skeleton class="size-8 rounded-full" />
                    <div class="space-y-2">
                        <Skeleton class="h-4 w-40" />
                        <Skeleton class="h-3 w-28" />
                    </div>
                </div>
                <Skeleton class="h-8 w-24" />
            </div>
        </div>

        <div
            v-else-if="sessions.length > 0"
            class="space-y-3"
            role="list"
        >
            <div
                v-for="session in sessions"
                :key="session.id"
                role="listitem"
                :class="cn(
                    'flex flex-col gap-3 rounded-lg border p-4 sm:flex-row sm:items-center sm:justify-between',
                    { 'border-primary/50 bg-primary/5': session.is_current },
                )"
            >
                <div class="flex items-start gap-3">
                    <component
                        :is="getDeviceIcon(session.agent.device)"
                        class="mt-0.5 size-5 shrink-0 text-muted-foreground"
                    />
                    <div class="space-y-1">
                        <p class="text-sm font-medium leading-none">
                            {{ session.agent.browser }}
                            <span class="font-normal text-muted-foreground">
                                on {{ session.agent.platform }}
                            </span>
                        </p>
                        <p
                            v-if="session.location"
                            class="flex items-center gap-1 text-xs text-muted-foreground"
                        >
                            <Globe class="size-3" />
                            {{ session.location }}
                        </p>
                        <p class="flex items-center gap-1 text-xs text-muted-foreground">
                            <Clock class="size-3" />
                            {{ t('app.settings.security.sessions.lastActive') }}: {{ session.last_activity_diff }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-center">
                    <Badge
                        v-if="session.is_current"
                        variant="outline"
                        class="text-xs"
                    >
                        {{ t('app.settings.security.sessions.currentSession') }}
                    </Badge>
                    <Button
                        v-if="!session.is_current"
                        variant="destructive"
                        size="sm"
                        :disabled="revoking === session.id"
                        :aria-label="`Revoke ${session.agent.browser} session on ${session.agent.platform}`"
                        @click="openRevokeSingleDialog(session)"
                    >
                        <Spinner v-if="revoking === session.id" class="size-3" />
                        <X v-else class="size-3" />
                        {{ t('app.settings.security.sessions.revoke') }}
                    </Button>
                </div>
            </div>

            <div
                v-if="sessions.filter((s) => !s.is_current).length > 0"
                class="pt-2"
            >
                <Button
                    variant="outline"
                    :disabled="revokingAll"
                    @click="showRevokeAllDialog = true"
                >
                    <Spinner v-if="revokingAll" class="size-3" />
                    {{ t('app.settings.security.sessions.revokeAll') }}
                </Button>
            </div>

            <p
                v-if="sessions.filter((s) => !s.is_current).length === 0 && !loading"
                class="text-sm text-muted-foreground"
            >
                {{ t('app.settings.security.sessions.onlyOne') }}
            </p>
        </div>

        <div
            v-else-if="!loading && !error"
            class="rounded-lg border p-4"
        >
            <p class="text-sm text-muted-foreground">
                {{ t('app.settings.security.sessions.driverNotConfigured') }}
            </p>
        </div>

        <!-- Confirm single revoke dialog -->
        <Dialog v-model:open="showRevokeSingleDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ t('app.settings.security.sessions.revokeConfirmTitle') }}</DialogTitle>
                    <DialogDescription>
                        {{ t('app.settings.security.sessions.revokeConfirmBody') }}
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button
                        variant="outline"
                        @click="showRevokeSingleDialog = false"
                    >
                        Cancel
                    </Button>
                    <Button
                        variant="destructive"
                        :disabled="revoking === confirmRevokeTarget?.id"
                        @click="handleRevokeSingle"
                    >
                        <Spinner
                            v-if="revoking === confirmRevokeTarget?.id"
                            class="size-3"
                        />
                        {{ t('app.settings.security.sessions.revoke') }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Confirm revoke-all dialog -->
        <Dialog v-model:open="showRevokeAllDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ t('app.settings.security.sessions.revokeAllConfirmTitle') }}</DialogTitle>
                    <DialogDescription>
                        {{ t('app.settings.security.sessions.revokeAllConfirmBody') }}
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button
                        variant="outline"
                        @click="showRevokeAllDialog = false"
                    >
                        Cancel
                    </Button>
                    <Button
                        variant="destructive"
                        :disabled="revokingAll"
                        @click="handleRevokeAll"
                    >
                        <Spinner v-if="revokingAll" class="size-3" />
                        {{ t('app.settings.security.sessions.revokeAll') }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
