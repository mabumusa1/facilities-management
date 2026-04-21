<script setup lang="ts">
import { Head, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useI18n } from '@/composables/useI18n';
import type { Admin } from '@/types';

const props = defineProps<{ admin: Admin }>();
const { t } = useI18n();

function roleLabel(role: string | null | undefined): string {
    if (role === 'Admins' || role === 'admin') {
        return t('app.contacts.admins.roles.admin');
    }

    if (role === 'accountingManagers') {
        return t('app.contacts.admins.roles.accountingManager');
    }

    if (role === 'serviceManagers') {
        return t('app.contacts.admins.roles.serviceManager');
    }

    if (role === 'marketingManagers') {
        return t('app.contacts.admins.roles.marketingManager');
    }

    if (role === 'salesAndLeasingManagers') {
        return t('app.contacts.admins.roles.salesLeasingManager');
    }

    return role ?? '—';
}

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.contacts.admins.pageTitle'), href: '/admins' },
            { title: `${props.admin.first_name} ${props.admin.last_name}`, href: '#' },
        ],
    });
});
</script>

<template>
    <Head :title="`${admin.first_name} ${admin.last_name}`" />
    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">{{ admin.first_name }} {{ admin.last_name }}</h2>
                <div class="mt-1 flex items-center gap-2">
                    <Badge>{{ roleLabel(admin.role) }}</Badge>
                    <Badge :variant="admin.active ? 'default' : 'secondary'">{{ admin.active ? t('app.common.active') : t('app.common.inactive') }}</Badge>
                </div>
            </div>
        </div>

        <Card>
            <CardHeader><CardTitle>{{ t('app.contacts.shared.info') }}</CardTitle></CardHeader>
            <CardContent class="space-y-2">
                <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.email') }}</span><span>{{ admin.email ?? '—' }}</span></div>
                <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.phone') }}</span><span>{{ admin.phone_number }}</span></div>
                <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.nationalId') }}</span><span>{{ admin.national_id ?? '—' }}</span></div>
                <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.lastLogin') }}</span><span>{{ admin.last_login_at ?? '—' }}</span></div>
            </CardContent>
        </Card>
    </div>
</template>
