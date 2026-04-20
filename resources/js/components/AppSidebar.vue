<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Building2,
    CalendarCheck,
    ClipboardList,
    DoorOpen,
    FileText,
    LayoutGrid,
    Megaphone,
    Receipt,
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
            { title: 'Visitor Access', href: '/requests?category=visitor-access' },
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
