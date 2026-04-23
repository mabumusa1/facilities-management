<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useI18n } from '@/composables/useI18n';
import { destroy as destroyRoute } from '@/routes/admin/users/role-assignments';
import AssignRoleDrawer from './AssignRoleDrawer.vue';

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
};

const props = defineProps<{
    user: User;
    roles: Role[];
    communities: Community[];
    buildings: Building[];
    serviceTypes: ServiceType[];
    assignments: Assignment[] | undefined;
}>();

const drawerOpen = ref(false);
const confirmingRemovalId = ref<number | null>(null);
const removingId = ref<number | null>(null);

function formatDate(dateStr: string | null): string {
    if (!dateStr) {
        return '—';
    }
    return new Intl.DateTimeFormat('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        numberingSystem: 'latn',
    }).format(new Date(dateStr));
}

function confirmRemove(assignmentId: number) {
    confirmingRemovalId.value = assignmentId;
}

function cancelRemove() {
    confirmingRemovalId.value = null;
}

function removeAssignment(assignmentId: number) {
    removingId.value = assignmentId;
    router.delete(destroyRoute.url({ user: props.user.id, assignment: assignmentId }), {
        preserveScroll: true,
        onFinish: () => {
            removingId.value = null;
            confirmingRemovalId.value = null;
        },
    });
}

function roleName(assignment: Assignment): string {
    return assignment.role_name_en ?? String(assignment.role_id);
}
</script>

<template>
    <div class="flex flex-col gap-4">
        <!-- Header row -->
        <div class="flex items-center justify-between">
            <span />
            <Button size="sm" @click="drawerOpen = true">{{ t('app.admin.users.assignRoleBtn') }}</Button>
        </div>

        <!-- Loading skeleton -->
        <div v-if="assignments === undefined">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>{{ t('app.admin.users.colRole') }}</TableHead>
                        <TableHead>{{ t('app.admin.users.colScope') }}</TableHead>
                        <TableHead>{{ t('app.admin.users.colAssigned') }}</TableHead>
                        <TableHead />
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="i in 3" :key="i">
                        <TableCell><div class="animate-pulse h-4 w-32 rounded bg-gray-200" /></TableCell>
                        <TableCell>
                            <div class="flex gap-1">
                                <div class="animate-pulse h-4 w-20 rounded bg-gray-200" />
                                <div class="animate-pulse h-4 w-16 rounded bg-gray-200" />
                            </div>
                        </TableCell>
                        <TableCell><div class="animate-pulse h-4 w-24 rounded bg-gray-200" /></TableCell>
                        <TableCell><Button variant="ghost" size="icon" disabled>✕</Button></TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <!-- Empty state -->
        <div v-else-if="assignments.length === 0" class="flex flex-col items-center gap-4 py-12 text-center">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-12 w-12 text-gray-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                />
            </svg>
            <div>
                <p class="font-medium">{{ t('app.admin.users.emptyRolesHeading') }}</p>
                <p class="text-muted-foreground text-sm">{{ t('app.admin.users.emptyRolesBody') }}</p>
            </div>
            <Button size="sm" @click="drawerOpen = true">{{ t('app.admin.users.assignRoleBtn') }}</Button>
        </div>

        <!-- Assignments table -->
        <Table v-else>
            <TableHeader>
                <TableRow>
                    <TableHead>{{ t('app.admin.users.colRole') }}</TableHead>
                    <TableHead>{{ t('app.admin.users.colScope') }}</TableHead>
                    <TableHead>{{ t('app.admin.users.colAssigned') }}</TableHead>
                    <TableHead />
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow
                    v-for="assignment in assignments"
                    :key="assignment.id"
                    class="transition-opacity duration-200"
                    :class="{ 'opacity-50': removingId === assignment.id }"
                >
                    <TableCell class="font-medium">{{ roleName(assignment) }}</TableCell>
                    <TableCell>
                        <div class="flex flex-col gap-1">
                            <Badge v-if="assignment.community_name" variant="secondary">{{ assignment.community_name }}</Badge>
                            <Badge v-if="assignment.building_name" variant="secondary">{{ assignment.building_name }}</Badge>
                            <Badge v-if="assignment.service_type_name" variant="secondary">{{ assignment.service_type_name }}</Badge>
                            <span v-if="!assignment.community_name && !assignment.building_name && !assignment.service_type_name" class="text-muted-foreground text-sm">—</span>
                        </div>
                    </TableCell>
                    <TableCell>{{ formatDate(assignment.created_at) }}</TableCell>
                    <TableCell class="text-end">
                        <!-- Inline confirmation -->
                        <div v-if="confirmingRemovalId === assignment.id" class="flex items-center gap-2 justify-end">
                            <span class="text-sm">{{ t('app.admin.users.removePopoverTitle') }}</span>
                            <Button variant="outline" size="sm" @click="cancelRemove">{{ t('app.admin.users.removePopoverCancel') }}</Button>
                            <Button
                                variant="destructive"
                                size="sm"
                                :disabled="removingId === assignment.id"
                                @click="removeAssignment(assignment.id)"
                            >
                                {{ t('app.admin.users.removePopoverConfirm') }}
                            </Button>
                        </div>
                        <Button
                            v-else
                            variant="ghost"
                            size="icon"
                            :aria-label="t('app.admin.users.removeTooltip')"
                            @click="confirmRemove(assignment.id)"
                        >
                            ✕
                        </Button>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>

        <AssignRoleDrawer
            v-model:open="drawerOpen"
            :user="user"
            :roles="roles"
            :communities="communities"
            :buildings="buildings"
            :service-types="serviceTypes"
        />
    </div>
</template>
