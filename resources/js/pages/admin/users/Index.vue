<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { ref, watchEffect } from 'vue';
import { MoreHorizontal } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { useI18n } from '@/composables/useI18n';
import type { PaginationLink } from '@/types';
import { deactivate, reactivate, resendInvitation, revokeInvitation, sendPasswordReset } from '@/routes/admin/users';
import { destroy, index, store, update } from '@/routes/admin/users';
import CreateUserDrawer from './partials/CreateUserDrawer.vue';

const { t } = useI18n();

type RoleOption = {
    value: string;
    label: string;
};

type Membership = {
    id: number;
    user_id: number;
    name: string;
    email: string;
    status: string;
    role: string;
    created_at: string | null;
};

const props = defineProps<{
    memberships: {
        data: Membership[];
        links: PaginationLink[];
    };
    roles: RoleOption[];
    currentTenant: {
        id: number;
        name: string;
    };
    currentUserId: number;
}>();

const drawerOpen = ref(false);

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.userManagement'), href: index.url() },
        ],
    });
});

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

function removeMembership(membershipId: number) {
    router.delete(destroy.url({ membership: membershipId }), {
        preserveScroll: true,
    });
}

function handleDeactivate(membership: Membership) {
    if (membership.user_id === props.currentUserId) return;
    router.post(deactivate.url({ user: membership.user_id }), {}, { preserveScroll: true });
}

function handleReactivate(membership: Membership) {
    router.post(reactivate.url({ user: membership.user_id }), {}, { preserveScroll: true });
}

function handleSendPasswordReset(membership: Membership) {
    router.post(sendPasswordReset.url({ user: membership.user_id }), {}, { preserveScroll: true });
}

function handleResendInvite(membership: Membership) {
    router.post(resendInvitation.url({ user: membership.user_id }), {}, { preserveScroll: true });
}

function handleRevokeInvite(membership: Membership) {
    router.post(revokeInvitation.url({ user: membership.user_id }), {}, { preserveScroll: true });
}

function isSelf(membership: Membership) {
    return membership.user_id === props.currentUserId;
}

function saveRole(membershipId: number, role: string) {
    const roleDraft = { role };
    router.put(update.url({ membership: membershipId }), roleDraft, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head :title="t('app.admin.users.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            variant="small"
            :title="t('app.admin.users.heading')"
            :description="t('app.admin.users.description')"
        />

        <div class="rounded-lg border p-4">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-muted-foreground">{{ t('app.admin.users.currentAccount') }}</h2>
                <div class="flex items-center gap-2">
                    <Badge variant="secondary">{{ currentTenant.name }}</Badge>
                    <Button variant="default" size="sm" @click="drawerOpen = true">
                        + {{ t('app.admin.users.inviteUser') }}
                    </Button>
                </div>
            </div>
        </div>

        <div class="rounded-lg border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>{{ t('app.admin.users.name') }}</TableHead>
                        <TableHead>{{ t('app.admin.users.email') }}</TableHead>
                        <TableHead>{{ t('app.admin.users.role') }}</TableHead>
                        <TableHead>{{ t('app.admin.users.status') }}</TableHead>
                        <TableHead>{{ t('app.admin.users.actions') }}</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="membership in memberships.data" :key="membership.id">
                        <TableCell>
                            <Link :href="store.url({ user: membership.user_id })" class="text-primary hover:underline">
                                {{ membership.name }}
                            </Link>
                        </TableCell>
                        <TableCell>{{ membership.email }}</TableCell>
                        <TableCell>
                            <select
                                :value="membership.role"
                                class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring h-9 w-full rounded-md border px-3 py-1 text-sm shadow-sm focus-visible:ring-1 focus-visible:outline-none"
                                @change="saveRole(membership.id, ($event.target as HTMLSelectElement).value)"
                            >
                                <option v-for="role in roles" :key="role.value" :value="role.value">
                                    {{ role.label }}
                                </option>
                            </select>
                        </TableCell>
                        <TableCell>
                            <Badge :variant="statusBadgeVariant(membership.status)">
                                <span :lang="statusBadgeVariant(membership.status) === 'default' ? 'en' : 'ar'">
                                    {{ statusLabel(membership.status) }}
                                </span>
                            </Badge>
                        </TableCell>
                        <TableCell class="text-end">
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button variant="ghost" size="icon" :aria-label="t('app.admin.users.moreActions')">
                                        <MoreHorizontal class="size-4" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end">
                                    <DropdownMenuItem as-child>
                                        <Link :href="store.url({ user: membership.user_id })">{{ t('app.admin.users.viewDetails') }}</Link>
                                    </DropdownMenuItem>

                                    <template v-if="membership.status === 'active'">
                                        <Tooltip v-if="isSelf(membership)">
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
                                        <DropdownMenuItem v-else class="text-destructive" @click="handleDeactivate(membership)">
                                            {{ t('app.admin.users.deactivate') }}
                                        </DropdownMenuItem>
                                        <DropdownMenuItem @click="handleSendPasswordReset(membership)">
                                            {{ t('app.admin.users.sendPasswordReset') }}
                                        </DropdownMenuItem>
                                    </template>

                                    <template v-if="membership.status === 'deactivated'">
                                        <DropdownMenuItem @click="handleReactivate(membership)">
                                            {{ t('app.admin.users.reactivate') }}
                                        </DropdownMenuItem>
                                    </template>

                                    <template v-if="membership.status === 'invitation_pending'">
                                        <DropdownMenuItem @click="handleResendInvite(membership)">
                                            {{ t('app.admin.users.resendInvitation') }}
                                        </DropdownMenuItem>
                                        <DropdownMenuItem class="text-destructive" @click="handleRevokeInvite(membership)">
                                            {{ t('app.admin.users.revokeInvitation') }}
                                        </DropdownMenuItem>
                                    </template>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="memberships.data.length === 0">
                        <TableCell :colspan="5" class="text-center text-muted-foreground">
                            {{ t('app.admin.users.empty') }}
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <div v-if="memberships.links.length > 3" class="flex items-center justify-end gap-1">
            <template v-for="link in memberships.links" :key="link.label">
                <Button
                    v-if="link.url"
                    variant="outline"
                    size="sm"
                    as-child
                    :class="{ 'bg-primary text-primary-foreground': link.active }"
                >
                    <Link :href="link.url" v-html="link.label" />
                </Button>
                <Button
                    v-else
                    variant="outline"
                    size="sm"
                    disabled
                    v-html="link.label"
                />
            </template>
        </div>

        <CreateUserDrawer
            v-model:open="drawerOpen"
            :roles="roles"
        />
    </div>
</template>
