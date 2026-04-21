<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
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
import { dashboard } from '@/routes';
import type { NavGroup, NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
];

const navGroups: NavGroup[] = [
    {
        title: 'Properties',
        icon: Building2,
        items: [
            { title: 'Communities', href: '/communities' },
            { title: 'Buildings', href: '/buildings' },
            { title: 'Units', href: '/units' },
        ],
    },
    {
        title: 'Leasing',
        icon: FileText,
        items: [
            { title: 'Leases', href: '/leases' },
        ],
    },
    {
        title: 'Requests',
        icon: ClipboardList,
        items: [
            { title: 'All Requests', href: '/requests' },
        ],
    },
    {
        title: 'Marketplace',
        icon: Store,
        items: [
            { title: 'Overview', href: '/marketplace' },
            { title: 'Customers', href: '/marketplace/customers' },
            { title: 'Listing', href: '/marketplace/listing' },
            { title: 'Visits', href: '/marketplace/visits' },
        ],
    },
    {
        title: 'Facilities',
        icon: CalendarCheck,
        items: [
            { title: 'Facilities', href: '/facilities' },
            { title: 'Bookings', href: '/facility-bookings' },
        ],
    },
    {
        title: 'Accounting',
        icon: Receipt,
        items: [
            { title: 'Transactions', href: '/transactions' },
        ],
    },
    {
        title: 'Communication',
        icon: Megaphone,
        items: [
            { title: 'Announcements', href: '/announcements' },
        ],
    },
    {
        title: 'Contacts',
        icon: Users,
        items: [
            { title: 'Owners', href: '/owners' },
            { title: 'Tenants', href: '/residents' },
            { title: 'Admins', href: '/admins' },
            { title: 'Professionals', href: '/professionals' },
        ],
    },
    {
        title: 'Visitor Access',
        icon: DoorOpen,
        items: [
            { title: 'Visitor Access History', href: '/visitor-access/history' },
        ],
    },
    {
        title: 'Reports',
        icon: ChartColumnBig,
        items: [
            { title: 'Reports', href: '/dashboard/reports' },
            { title: 'Power BI Reports', href: '/dashboard/power-bi-reports' },
            { title: 'System Reports', href: '/dashboard/system-reports' },
        ],
    },
    {
        title: 'App Settings',
        icon: Settings,
        items: [
            { title: 'Settings Shell', href: '/settings/invoice' },
            { title: 'Settings Facilities', href: '/settings/facilities' },
            { title: 'Settings Forms', href: '/settings/forms' },
            { title: 'Request Categories', href: '/app-settings/request-categories' },
            { title: 'Facility Categories', href: '/app-settings/facility-categories' },
            { title: 'Invoice Settings', href: '/app-settings/invoice' },
            { title: 'General Settings', href: '/app-settings/general' },
        ],
    },
    {
        title: 'Shared',
        icon: Bell,
        items: [
            { title: 'Notifications', href: '/notifications' },
        ],
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
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
            <NavGroups label="Modules" :items="navGroups" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
