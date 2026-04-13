import { Link } from '@inertiajs/react';
import {
    BookOpen,
    Building2,
    FolderGit2,
    Home,
    LayoutGrid,
    MapPin,
    Users,
    Wrench,
} from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { index as buildingsIndex } from '@/routes/buildings';
import { index as communitiesIndex } from '@/routes/communities';
import { index as contactsIndex } from '@/routes/contacts';
import { index as serviceRequestsIndex } from '@/routes/service-requests';
import { index as unitsIndex } from '@/routes/units';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Communities',
        href: communitiesIndex(),
        icon: MapPin,
    },
    {
        title: 'Buildings',
        href: buildingsIndex(),
        icon: Building2,
    },
    {
        title: 'Units',
        href: unitsIndex(),
        icon: Home,
    },
    {
        title: 'Contacts',
        href: contactsIndex(),
        icon: Users,
    },
    {
        title: 'Service Requests',
        href: serviceRequestsIndex(),
        icon: Wrench,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/react-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#react',
        icon: BookOpen,
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
