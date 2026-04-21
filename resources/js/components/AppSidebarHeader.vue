<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Bell } from 'lucide-vue-next';
import { computed } from 'vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItem } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const page = usePage();

const unreadCount = computed(() => {
    const count = page.props.notifications?.unread_count;

    return typeof count === 'number' ? count : 0;
});
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>

        <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <Button variant="outline" size="sm" class="ml-auto">
                    <Bell class="size-4" />
                    <span>Notifications</span>
                    <Badge v-if="unreadCount > 0" class="ml-1" variant="secondary">{{ unreadCount }}</Badge>
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" class="w-64">
                <DropdownMenuLabel>Notifications</DropdownMenuLabel>
                <DropdownMenuSeparator />
                <DropdownMenuItem as-child>
                    <Link href="/notifications">Open Notifications Center</Link>
                </DropdownMenuItem>
                <DropdownMenuItem as-child>
                    <Link href="/notifications/unread-count">Unread Count Endpoint</Link>
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    </header>
</template>
