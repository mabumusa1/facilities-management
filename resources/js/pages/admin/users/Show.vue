<script setup lang="ts">
import { Head, setLayoutProps } from '@inertiajs/vue3';
import { computed, ref, watchEffect } from 'vue';
import Heading from '@/components/Heading.vue';
import { useI18n } from '@/composables/useI18n';
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
};

const props = defineProps<{
    user: User;
    roles: Role[];
    communities: Community[];
    buildings: Building[];
    serviceTypes: ServiceType[];
    assignments: Assignment[] | undefined;
}>();

type Tab = 'roles' | 'overview' | 'activity';
const activeTab = ref<Tab>('roles');

const assignmentCount = computed<number>(() => props.assignments?.length ?? 0);

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
        <!-- Page header -->
        <div class="flex items-start justify-between">
            <div>
                <Heading>{{ user.name }}</Heading>
                <p class="text-muted-foreground text-sm">{{ user.email }}</p>
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
        <div>
            <RolesTab
                v-if="activeTab === 'roles'"
                :user="user"
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
