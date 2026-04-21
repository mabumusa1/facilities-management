<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavGroup } from '@/types';

defineProps<{
    label?: string;
    items: NavGroup[];
}>();

const { isCurrentUrl } = useCurrentUrl();

function isGroupActive(group: NavGroup): boolean {
    return group.items.some((item) => isCurrentUrl(item.href));
}
</script>

<template>
    <SidebarGroup>
        <SidebarGroupLabel v-if="label">{{ label }}</SidebarGroupLabel>
        <SidebarMenu>
            <Collapsible
                v-for="group in items"
                :key="group.title"
                as-child
                :default-open="isGroupActive(group)"
                class="group/collapsible"
            >
                <SidebarMenuItem>
                    <CollapsibleTrigger as-child>
                        <SidebarMenuButton :tooltip="group.title">
                            <component :is="group.icon" v-if="group.icon" />
                            <span>{{ group.title }}</span>
                            <ChevronRight
                                class="ms-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                            />
                        </SidebarMenuButton>
                    </CollapsibleTrigger>
                    <CollapsibleContent>
                        <SidebarMenuSub>
                            <SidebarMenuSubItem
                                v-for="item in group.items"
                                :key="item.title"
                            >
                                <SidebarMenuSubButton
                                    as-child
                                    :is-active="isCurrentUrl(item.href)"
                                >
                                    <Link :href="item.href">
                                        <span>{{ item.title }}</span>
                                    </Link>
                                </SidebarMenuSubButton>
                            </SidebarMenuSubItem>
                        </SidebarMenuSub>
                    </CollapsibleContent>
                </SidebarMenuItem>
            </Collapsible>
        </SidebarMenu>
    </SidebarGroup>
</template>
