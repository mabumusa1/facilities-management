<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { computed, ref, watchEffect } from 'vue';
import { MoreHorizontal } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { useI18n } from '@/composables/useI18n';
import { deactivate, reactivate, resendInvitation, revokeInvitation, sendPasswordReset } from '@/routes/admin/users';
import { index as usersIndexRoute } from '@/routes/admin/users';
import RolesTab from './partials/RolesTab.vue';

const { t } = useI18n();

type Role = {
    id: number;
    name: string;
    name_en: string | null;
    name_ar: string | null;
    scope_level: 'none' | 'manager' | 'serviceManager';
};

type Community = {
    id: number;
    name: string;
};

type Building = {
    id: number;
    name: string;
    rf_community_id: number | null;
};

type ServiceType = {
    id: number;
    name: string;
};

type Assignment = {
    id: number;
    role_id: number;
    role_name_en: string | null;
    role_name_ar: string | null;
    community_id: number | null;
    community_name: string | null;
    building_id: number | null;
    building_name: string | null;
    service_type_id: number | null;
    service_type_name: string | null;
    created_at?: string | null;
};

type User = {
    id: number;
    name: string;
    email: string;
    status: string;
};

const props = defineProps<{
    user: User;
    roles: Role[];
    communities: Community[];
    buildings: Building[];
    serviceTypes: ServiceType[];
    assignments: Assignment[] | undefined;
    currentUserId: number;
}>();

type Tab = 'roles' | 'overview' | 'activity';
const activeTab = ref<Tab>('roles');

const isSelf = computed(() => props.user.id === props.currentUserId);

const assignmentCount = computed<number>(() => props.assignments?.length ?? 0);

function statusBadgeVariant(status: string) {
    switch (status) {
        case 'active':
            return 'default';
        case 'invitation_pending':
            return 'outline';
        case 'deactivated':
            return 'secondary';
        default:
            return 'outline';
    }
}

function statusLabel(status: string) {
    switch (status) {
        case 'active':
            return t('app.admin.users.statusActive');
        case 'invitation_pending':
            return t('app.admin.users.statusInvitationPending');
        case 'deactivated':
            return t('app.admin.users.statusDeactivated');
        default:
            return status;
    }
}

function handleDeactivate() {
    router.post(deactivate.url({ user: props.user.id }), {}, { preserveScroll: true });
}

function handleReactivate() {
    router.post(reactivate.url({ user: props.user.id }), {}, { preserveScroll: true });
}

function handleSendPasswordReset() {
    router.post(sendPasswordReset.url({ user: props.user.id }), {}, { preserveScroll: true });
}

function handleResendInvite() {
    router.post(resendInvitation.url({ user: props.user.id }), {}, { preserveScroll: true });
}

function handleRevokeInvite() {
    router.post(revokeInvitation.url({ user: props.user.id }), {}, { preserveScroll: true });
}

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.userManagement'), href: usersIndexRoute.url() },
            { title: props.user.name, href: '#' },
        ],
    });
});
</script>

<template>
    <Head :title="t('app.admin.users.showPageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <!-- Deactivated info banner -->
        <div v-if="user.status === 'deactivated'" class="rounded-lg border border-amber-200 bg-amber-50 p-4" role="status">
            <p class="text-amber-800 text-sm">{{ t('app.admin.users.deactivatedBanner') }}</p>
        </div>

        <!-- Page header -->
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
                <div>
                    <Heading>{{ user.name }}</Heading>
                    <p class="text-muted-foreground text-sm">{{ user.email }}</p>
                </div>
                <Badge :variant="statusBadgeVariant(user.status)">
                    {{ statusLabel(user.status) }}
                </Badge>
            </div>
            <div class="flex items-center gap-2">
                <Button
                    v-if="user.status === 'deactivated'"
                    variant="default"
                    size="sm"
                    @click="handleReactivate"
                >
                    {{ t('app.admin.users.reactivate') }}
                </Button>

                <Button
                    v-if="user.status === 'active'"
                    variant="outline"
                    size="sm"
                    @click="handleSendPasswordReset"
                >
                    {{ t('app.admin.users.sendPasswordReset') }}
                </Button>

                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button variant="ghost" size="icon" :aria-label="t('app.admin.users.moreActions')">
                            <MoreHorizontal class="size-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <template v-if="user.status === 'active'">
                            <Tooltip v-if="isSelf">
                                <TooltipTrigger as-child>
                                    <div>
                                        <DropdownMenuItem class="text-destructive opacity-50 cursor-not-allowed" :disabled="true">
                                            {{ t('app.admin.users.deactivate') }}
                                        </DropdownMenuItem>
                                    </div>
                                </TooltipTrigger>
                                <TooltipContent placement="bottom">
                                    {{ t('app.admin.users.cannotDeactivateSelf') }}
                                </TooltipContent>
                            </Tooltip>
                            <DropdownMenuItem v-else class="text-destructive" @click="handleDeactivate">
                                {{ t('app.admin.users.deactivate') }}
                            </DropdownMenuItem>
                        </template>

                        <template v-if="user.status === 'deactivated'">
                            <DropdownMenuItem @click="handleSendPasswordReset">
                                {{ t('app.admin.users.sendPasswordReset') }}
                            </DropdownMenuItem>
                        </template>

                        <template v-if="user.status === 'invitation_pending'">
                            <DropdownMenuItem @click="handleResendInvite">
                                {{ t('app.admin.users.resendInvitation') }}
                            </DropdownMenuItem>
                            <DropdownMenuItem class="text-destructive" @click="handleRevokeInvite">
                                {{ t('app.admin.users.revokeInvitation') }}
                            </DropdownMenuItem>
                        </template>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </div>

        <!-- Tabs -->
        <div class="border-b">
            <nav class="-mb-px flex gap-6">
                <button
                    class="pb-2 text-sm font-medium transition-colors"
                    :class="activeTab === 'roles' ? 'border-b-2 border-current' : 'text-muted-foreground hover:text-foreground'"
                    @click="activeTab = 'roles'"
                >
                    {{ t('app.admin.users.tabRoles') }}
                    <span v-if="assignments !== undefined && assignments.length > 0" class="ms-1 text-xs">({{ assignmentCount }})</span>
                    <span v-else-if="assignments !== undefined && assignments.length === 0" />
                    <span v-else class="ms-1 h-2 w-2 inline-block rounded-full bg-current" />
                </button>
                <button
                    class="pb-2 text-sm font-medium transition-colors text-muted-foreground hover:text-foreground"
                    :class="activeTab === 'overview' ? 'border-b-2 border-current text-foreground' : ''"
                    @click="activeTab = 'overview'"
                >
                    {{ t('app.admin.users.tabOverview') }}
                </button>
                <button
                    class="pb-2 text-sm font-medium transition-colors text-muted-foreground hover:text-foreground"
                    :class="activeTab === 'activity' ? 'border-b-2 border-current text-foreground' : ''"
                    @click="activeTab = 'activity'"
                >
                    {{ t('app.admin.users.tabActivity') }}
                </button>
            </nav>
        </div>

        <!-- Tab panels -->
        <div :class="{ 'opacity-50 pointer-events-none': user.status === 'deactivated' }">
            <RolesTab
                v-if="activeTab === 'roles'"
                :user="{ id: user.id, name: user.name, email: user.email }"
                :roles="roles"
                :communities="communities"
                :buildings="buildings"
                :service-types="serviceTypes"
                :assignments="assignments"
            />

            <div v-else-if="activeTab === 'overview'" class="text-muted-foreground py-8 text-center text-sm">
                {{ t('app.admin.users.tabOverview') }}
            </div>

            <div v-else-if="activeTab === 'activity'" class="text-muted-foreground py-8 text-center text-sm">
                {{ t('app.admin.users.tabActivity') }}
            </div>
        </div>
    </div>
</template>
