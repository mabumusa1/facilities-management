<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import {
    Bell,
    ChartColumnBig,
    Building2,
    CalendarCheck,
    ClipboardList,
    DoorOpen,
    FileText,
    LayoutGrid,
    Megaphone,
    Store,
    Receipt,
    Settings,
    Users,
} from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavGroups from '@/components/NavGroups.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useI18n } from '@/composables/useI18n';
import { dashboard } from '@/routes';
import type { NavGroup, NavItem } from '@/types';
import { computed } from 'vue';

const { isArabic, t } = useI18n();
const page = usePage();

const canManageAccountAdministration = computed(() => {
    const roles = page.props.auth?.roles;

    if (!Array.isArray(roles)) {
        return false;
    }

    return roles.includes('accountAdmins') || roles.includes('admins');
});

const mainNavItems = computed<NavItem[]>(() => [
    {
        title: t('app.navigation.dashboard'),
        href: dashboard(),
        icon: LayoutGrid,
    },
]);

const navGroups = computed<NavGroup[]>(() => [
    {
        title: t('app.navigation.properties'),
        icon: Building2,
        items: [
            { title: t('app.navigation.communities'), href: '/communities' },
            { title: t('app.navigation.buildings'), href: '/buildings' },
            { title: t('app.navigation.units'), href: '/units' },
        ],
    },
    {
        title: t('app.navigation.leasing'),
        icon: FileText,
        items: [
            { title: t('app.navigation.leases'), href: '/leases' },
            { title: t('app.navigation.leads'), href: '/leads' },
        ],
    },
    {
        title: t('app.navigation.requests'),
        icon: ClipboardList,
        items: [
            { title: t('app.navigation.allRequests'), href: '/requests' },
        ],
    },
    {
        title: t('app.navigation.marketplace'),
        icon: Store,
        items: [
            { title: t('app.navigation.overview'), href: '/marketplace' },
            { title: t('app.navigation.customers'), href: '/marketplace/customers' },
            { title: t('app.navigation.listing'), href: '/marketplace/listing' },
            { title: t('app.navigation.visits'), href: '/marketplace/visits' },
        ],
    },
    {
        title: t('app.navigation.facilities'),
        icon: CalendarCheck,
        items: [
            { title: t('app.navigation.facilities'), href: '/facilities' },
            { title: t('app.navigation.bookings'), href: '/facility-bookings' },
        ],
    },
    {
        title: t('app.navigation.accounting'),
        icon: Receipt,
        items: [
            { title: t('app.navigation.transactions'), href: '/transactions' },
        ],
    },
    {
        title: t('app.navigation.communication'),
        icon: Megaphone,
        items: [
            { title: t('app.navigation.announcements'), href: '/announcements' },
        ],
    },
    {
        title: t('app.navigation.contacts'),
        icon: Users,
        items: [
            { title: t('app.navigation.owners'), href: '/owners' },
            { title: t('app.navigation.tenants'), href: '/residents' },
            { title: t('app.navigation.admins'), href: '/admins' },
            { title: t('app.navigation.professionals'), href: '/professionals' },
        ],
    },
    {
        title: t('app.navigation.visitorAccess'),
        icon: DoorOpen,
        items: [
            { title: t('app.navigation.visitorAccessHistory'), href: '/visitor-access/history' },
        ],
    },
    {
        title: t('app.navigation.reports'),
        icon: ChartColumnBig,
        items: [
            { title: t('app.navigation.reports'), href: '/dashboard/reports' },
            { title: t('app.navigation.systemReports'), href: '/dashboard/system-reports' },
        ],
    },
    {
        title: t('app.navigation.appSettings'),
        icon: Settings,
        items: [
            { title: t('app.navigation.settingsShell'), href: '/settings/invoice' },
            { title: t('app.navigation.settingsFacilities'), href: '/settings/facilities' },
            { title: t('app.navigation.settingsForms'), href: '/settings/forms' },
            { title: t('app.navigation.requestCategories'), href: '/app-settings/request-categories' },
            { title: t('app.navigation.facilityCategories'), href: '/app-settings/facility-categories' },
            { title: t('app.navigation.companyProfile'), href: '/app-settings/company-profile' },
            { title: t('app.navigation.invoiceSettings'), href: '/app-settings/invoice' },
            { title: t('app.navigation.generalSettings'), href: '/app-settings/general' },
            ...(canManageAccountAdministration.value
                ? [
                    { title: t('app.navigation.userManagement'), href: '/admin/users' },
                    { title: t('app.navigation.accountSubscriptions'), href: '/admin/subscriptions' },
                ]
                : []),
        ],
    },
    {
        title: t('app.navigation.shared'),
        icon: Bell,
        items: [
            { title: t('app.navigation.notifications'), href: '/notifications' },
        ],
    },
]);
</script>

<template>
    <Sidebar :dir="isArabic ? 'rtl' : 'ltr'" :side="isArabic ? 'right' : 'left'" collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
            <NavGroups :label="t('app.navigation.modules')" :items="navGroups" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
