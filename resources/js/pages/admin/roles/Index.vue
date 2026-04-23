<script setup lang="ts">
import { Head, Link, router, setLayoutProps, useForm } from '@inertiajs/vue3';
import { ref, watchEffect } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Sheet, SheetContent, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { useI18n } from '@/composables/useI18n';
import type { PaginationLink } from '@/types';
import { destroy, index, store, update } from '@/routes/admin/roles';

const { t } = useI18n();

type Role = {
    id: number;
    name: string;
    name_en: string | null;
    name_ar: string | null;
    type: 'userRole' | 'adminRole' | null;
    users_count: number;
    is_system: boolean;
};

const props = defineProps<{
    roles: {
        data: Role[];
        links: PaginationLink[];
    };
    filters: {
        search: string;
        type: string;
    };
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.admin.roles.breadcrumb'), href: index.url() },
        ],
    });
});

// Search / filter
const searchQuery = ref(props.filters.search);
const typeFilter = ref(props.filters.type);

let searchTimeout: ReturnType<typeof setTimeout> | null = null;

function onSearch() {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 300);
}

function onTypeChange() {
    applyFilters();
}

function applyFilters() {
    router.get(
        index.url(),
        { search: searchQuery.value, type: typeFilter.value },
        { preserveState: true, replace: true },
    );
}

// Drawer state
const drawerOpen = ref(false);
const editingRole = ref<Role | null>(null);

const roleForm = useForm({
    name_en: '',
    name_ar: '',
    type: 'userRole' as 'userRole' | 'adminRole',
});

function openCreateDrawer() {
    editingRole.value = null;
    roleForm.reset();
    roleForm.clearErrors();
    drawerOpen.value = true;
}

function openEditDrawer(role: Role) {
    editingRole.value = role;
    roleForm.name_en = role.name_en ?? '';
    roleForm.name_ar = role.name_ar ?? '';
    roleForm.type = role.type ?? 'userRole';
    roleForm.clearErrors();
    drawerOpen.value = true;
}

function closeDrawer() {
    drawerOpen.value = false;
    editingRole.value = null;
}

function submitRoleForm() {
    if (editingRole.value) {
        roleForm.put(update.url(editingRole.value.id), {
            preserveScroll: true,
            onSuccess: () => closeDrawer(),
        });
    } else {
        roleForm.post(store.url(), {
            preserveScroll: true,
            onSuccess: () => closeDrawer(),
        });
    }
}

// Delete dialog state
const deleteDialogOpen = ref(false);
const roleToDelete = ref<Role | null>(null);
const deleteForm = useForm({});

function openDeleteDialog(role: Role) {
    roleToDelete.value = role;
    deleteDialogOpen.value = true;
}

function closeDeleteDialog() {
    deleteDialogOpen.value = false;
    roleToDelete.value = null;
}

function confirmDelete() {
    if (! roleToDelete.value) {
        return;
    }
    deleteForm.delete(destroy.url(roleToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => closeDeleteDialog(),
    });
}

function typeLabel(type: string | null): string {
    if (type === 'userRole') {
        return t('app.admin.roles.typeUserRole');
    }
    if (type === 'adminRole') {
        return t('app.admin.roles.typeAdminRole');
    }
    return '';
}
</script>

<template>
    <Head :title="t('app.admin.roles.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading
                variant="small"
                :title="t('app.admin.roles.heading')"
                :description="t('app.admin.roles.description')"
            />
            <Button @click="openCreateDrawer">
                {{ t('app.admin.roles.newRole') }}
            </Button>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1">
                <Input
                    id="search-roles"
                    v-model="searchQuery"
                    :placeholder="t('app.admin.roles.search')"
                    class="max-w-xs"
                    aria-label="Search roles"
                    @input="onSearch"
                />
            </div>
            <div class="flex items-center gap-2">
                <Label for="type-filter">{{ t('app.admin.roles.typeLabel') }}</Label>
                <select
                    id="type-filter"
                    v-model="typeFilter"
                    class="border-input bg-background ring-offset-background focus-visible:ring-ring flex h-9 rounded-md border px-3 py-1 text-sm shadow-sm focus-visible:ring-1 focus-visible:outline-none"
                    @change="onTypeChange"
                >
                    <option value="">{{ t('app.admin.roles.typeAll') }}</option>
                    <option value="userRole">{{ t('app.admin.roles.typeUserRole') }}</option>
                    <option value="adminRole">{{ t('app.admin.roles.typeAdminRole') }}</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="rounded-lg border">
            <Table aria-busy="false">
                <caption class="sr-only">Roles list</caption>
                <TableHeader>
                    <TableRow>
                        <TableHead scope="col">{{ t('app.admin.roles.colNameEn') }}</TableHead>
                        <TableHead scope="col">{{ t('app.admin.roles.colNameAr') }}</TableHead>
                        <TableHead scope="col">{{ t('app.admin.roles.colType') }}</TableHead>
                        <TableHead scope="col">{{ t('app.admin.roles.colUsers') }}</TableHead>
                        <TableHead scope="col">{{ t('app.admin.roles.colActions') }}</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="role in roles.data" :key="role.id">
                        <TableCell>
                            <span class="flex flex-wrap items-center gap-2">
                                {{ role.name_en }}
                                <Badge v-if="role.is_system" variant="secondary" class="text-xs">
                                    {{ t('app.admin.roles.systemBadge') }}
                                </Badge>
                            </span>
                        </TableCell>
                        <TableCell>
                            <span dir="rtl" lang="ar">{{ role.name_ar }}</span>
                        </TableCell>
                        <TableCell>
                            <Badge variant="outline">{{ typeLabel(role.type) }}</Badge>
                        </TableCell>
                        <TableCell>{{ role.users_count }}</TableCell>
                        <TableCell>
                            <div class="flex items-center gap-2">
                                <TooltipProvider v-if="role.is_system">
                                    <Tooltip>
                                        <TooltipTrigger as-child>
                                            <span>
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    class="opacity-40 cursor-not-allowed"
                                                    :aria-label="`${t('app.admin.roles.ariaEditRole')} ${role.name_en}`"
                                                    aria-disabled="true"
                                                    :tabindex="-1"
                                                    disabled
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                </Button>
                                            </span>
                                        </TooltipTrigger>
                                        <TooltipContent>{{ t('app.admin.roles.editTooltip') }}</TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                                <Button
                                    v-else
                                    variant="ghost"
                                    size="icon"
                                    :aria-label="`${t('app.admin.roles.ariaEditRole')} ${role.name_en}`"
                                    @click="openEditDrawer(role)"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </Button>

                                <TooltipProvider v-if="role.is_system">
                                    <Tooltip>
                                        <TooltipTrigger as-child>
                                            <span>
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    class="opacity-40 cursor-not-allowed"
                                                    :aria-label="`${t('app.admin.roles.ariaDeleteRole')} ${role.name_en}`"
                                                    aria-disabled="true"
                                                    :tabindex="-1"
                                                    disabled
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </Button>
                                            </span>
                                        </TooltipTrigger>
                                        <TooltipContent>{{ t('app.admin.roles.deleteTooltip') }}</TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                                <Button
                                    v-else
                                    variant="ghost"
                                    size="icon"
                                    :aria-label="`${t('app.admin.roles.ariaDeleteRole')} ${role.name_en}`"
                                    @click="openDeleteDialog(role)"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>

                    <!-- Empty state -->
                    <TableRow v-if="roles.data.length === 0">
                        <TableCell :colspan="5" class="py-12 text-center">
                            <div class="flex flex-col items-center gap-3 text-muted-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-10 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                <p class="font-medium">{{ t('app.admin.roles.emptyHeading') }}</p>
                                <p class="text-sm">{{ t('app.admin.roles.emptyBody') }}</p>
                                <Button variant="outline" size="sm" @click="openCreateDrawer">
                                    {{ t('app.admin.roles.emptyCta') }}
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <!-- Pagination -->
        <div v-if="roles.links.length > 3" class="flex items-center justify-end gap-1">
            <template v-for="link in roles.links" :key="link.label">
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
    </div>

    <!-- Create / Edit Drawer -->
    <Sheet :open="drawerOpen" @update:open="(val) => { if (!val) closeDrawer(); }">
        <SheetContent side="right" class="flex flex-col gap-0 p-0" role="dialog" aria-modal="true">
            <SheetHeader class="border-b p-6">
                <SheetTitle>
                    {{ editingRole ? t('app.admin.roles.drawerEditTitle') : t('app.admin.roles.drawerCreateTitle') }}
                </SheetTitle>
            </SheetHeader>

            <form class="flex flex-1 flex-col gap-5 overflow-y-auto p-6" @submit.prevent="submitRoleForm">
                <div class="grid gap-2">
                    <Label for="name_en">{{ t('app.admin.roles.nameEnLabel') }} *</Label>
                    <Input
                        id="name_en"
                        v-model="roleForm.name_en"
                        dir="ltr"
                        required
                        :placeholder="t('app.admin.roles.nameEnLabel')"
                    />
                    <InputError :message="roleForm.errors.name_en" />
                </div>

                <div class="grid gap-2">
                    <Label for="name_ar">{{ t('app.admin.roles.nameArLabel') }} *</Label>
                    <Input
                        id="name_ar"
                        v-model="roleForm.name_ar"
                        dir="rtl"
                        required
                        :placeholder="t('app.admin.roles.nameArLabel')"
                    />
                    <InputError :message="roleForm.errors.name_ar" />
                </div>

                <div class="grid gap-2">
                    <Label>{{ t('app.admin.roles.typeLabel') }} *</Label>
                    <div v-if="editingRole" class="text-muted-foreground text-sm">
                        {{ typeLabel(roleForm.type) }}
                        <p class="mt-1 text-xs">{{ t('app.admin.roles.typeImmutableHint') }}</p>
                    </div>
                    <div v-else class="flex gap-4">
                        <label class="flex cursor-pointer items-center gap-2 text-sm">
                            <input v-model="roleForm.type" type="radio" value="userRole" class="accent-primary" />
                            {{ t('app.admin.roles.typeUserRole') }}
                        </label>
                        <label class="flex cursor-pointer items-center gap-2 text-sm">
                            <input v-model="roleForm.type" type="radio" value="adminRole" class="accent-primary" />
                            {{ t('app.admin.roles.typeAdminRole') }}
                        </label>
                    </div>
                    <InputError :message="roleForm.errors.type" />
                </div>

                <SheetFooter class="mt-auto flex justify-end gap-2 border-t pt-4">
                    <Button type="button" variant="outline" :disabled="roleForm.processing" @click="closeDrawer">
                        {{ t('app.admin.roles.cancel') }}
                    </Button>
                    <Button type="submit" :disabled="roleForm.processing">
                        {{ roleForm.processing
                            ? t('app.admin.roles.saving')
                            : editingRole
                                ? t('app.admin.roles.saveChanges')
                                : t('app.admin.roles.createRole') }}
                    </Button>
                </SheetFooter>
            </form>
        </SheetContent>
    </Sheet>

    <!-- Delete Confirmation Dialog -->
    <Dialog :open="deleteDialogOpen" @update:open="(val) => { if (!val) closeDeleteDialog(); }">
        <DialogContent role="alertdialog" aria-modal="true">
            <DialogHeader>
                <DialogTitle>{{ t('app.admin.roles.deleteDialogTitle') }}</DialogTitle>
                <DialogDescription>
                    {{ t('app.admin.roles.deleteDialogBody').replace('{name}', roleToDelete?.name_en ?? '') }}
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" :disabled="deleteForm.processing" @click="closeDeleteDialog">
                    {{ t('app.admin.roles.deleteDialogCancel') }}
                </Button>
                <Button variant="destructive" :disabled="deleteForm.processing" @click="confirmDelete">
                    {{ t('app.admin.roles.deleteDialogConfirm') }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
