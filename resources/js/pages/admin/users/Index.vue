<script setup lang="ts">
import { Head, Link, router, setLayoutProps, useForm } from '@inertiajs/vue3';
import { watch, watchEffect } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useI18n } from '@/composables/useI18n';
import type { PaginationLink } from '@/types';
import { destroy, index, store, update } from '@/routes/admin/users';

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
    role: string;
    created_at: string | null;
};

defineProps<{
    memberships: {
        data: Membership[];
        links: PaginationLink[];
    };
    roles: RoleOption[];
    currentTenant: {
        id: number;
        name: string;
    };
}>();

const roleDraft = useForm<{ role: string }>({ role: '' });

const createUserForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'admins',
});

watch(
    () => createUserForm.password,
    () => {
        if (!createUserForm.password_confirmation) {
            return;
        }

        createUserForm.clearErrors('password_confirmation');
    },
);

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.userManagement'), href: index.url() },
        ],
    });
});

function submitCreateUser() {
    createUserForm.post(store.url(), {
        preserveScroll: true,
        onSuccess: () => {
            createUserForm.reset('name', 'email', 'password', 'password_confirmation');
        },
    });
}

function saveRole(membershipId: number, role: string) {
    roleDraft.role = role;

    roleDraft.put(update.url({ membership: membershipId }), {
        preserveScroll: true,
    });
}

function removeMembership(membershipId: number) {
    router.delete(destroy.url({ membership: membershipId }), {
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
                <Badge variant="secondary">{{ currentTenant.name }}</Badge>
            </div>

            <form class="grid gap-4 md:grid-cols-2" @submit.prevent="submitCreateUser">
                <div class="grid gap-2">
                    <Label for="name">{{ t('app.admin.users.name') }}</Label>
                    <Input id="name" v-model="createUserForm.name" required />
                    <InputError :message="createUserForm.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">{{ t('app.admin.users.email') }}</Label>
                    <Input id="email" v-model="createUserForm.email" type="email" required />
                    <InputError :message="createUserForm.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">{{ t('app.admin.users.password') }}</Label>
                    <Input id="password" v-model="createUserForm.password" type="password" required />
                    <InputError :message="createUserForm.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">{{ t('app.admin.users.passwordConfirmation') }}</Label>
                    <Input id="password_confirmation" v-model="createUserForm.password_confirmation" type="password" required />
                    <InputError :message="createUserForm.errors.password_confirmation" />
                </div>

                <div class="grid gap-2 md:col-span-2">
                    <Label for="role">{{ t('app.admin.users.role') }}</Label>
                    <select
                        id="role"
                        v-model="createUserForm.role"
                        class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex h-9 w-full rounded-md border px-3 py-1 text-sm shadow-sm focus-visible:ring-1 focus-visible:outline-none"
                    >
                        <option v-for="role in roles" :key="role.value" :value="role.value">
                            {{ role.label }}
                        </option>
                    </select>
                    <InputError :message="createUserForm.errors.role" />
                </div>

                <div class="md:col-span-2">
                    <Button :disabled="createUserForm.processing">
                        {{ t('app.admin.users.addUser') }}
                    </Button>
                </div>
            </form>
        </div>

        <div class="rounded-lg border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>{{ t('app.admin.users.name') }}</TableHead>
                        <TableHead>{{ t('app.admin.users.email') }}</TableHead>
                        <TableHead>{{ t('app.admin.users.role') }}</TableHead>
                        <TableHead>{{ t('app.admin.users.actions') }}</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="membership in memberships.data" :key="membership.id">
                        <TableCell>{{ membership.name }}</TableCell>
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
                            <Button
                                variant="destructive"
                                size="sm"
                                @click="removeMembership(membership.id)"
                            >
                                {{ t('app.admin.users.remove') }}
                            </Button>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="memberships.data.length === 0">
                        <TableCell :colspan="4" class="text-center text-muted-foreground">
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
    </div>
</template>
