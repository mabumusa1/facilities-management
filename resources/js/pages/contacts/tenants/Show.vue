<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useI18n } from '@/composables/useI18n';
import type { Resident } from '@/types';

const props = defineProps<{ resident: Resident }>();
const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.contacts.tenants.pageTitle'), href: '/residents' },
            { title: `${props.resident.first_name} ${props.resident.last_name}`, href: '#' },
        ],
    });
});

function deleteResident() { if (confirm(t('app.contacts.shared.confirmDeletePrompt'))) { router.delete(`/residents/${props.resident.id}`); } }
</script>

<template>
    <Head :title="`${resident.first_name} ${resident.last_name}`" />
    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">{{ resident.first_name }} {{ resident.last_name }}</h2>
                <Badge :variant="resident.active ? 'default' : 'secondary'">{{ resident.active ? t('app.common.active') : t('app.common.inactive') }}</Badge>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child><Link :href="`/residents/${resident.id}/edit`">{{ t('app.common.edit') }}</Link></Button>
                <Button variant="destructive" @click="deleteResident">{{ t('app.common.delete') }}</Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.contacts.shared.units') }}</CardTitle></CardHeader><CardContent><div class="text-2xl font-bold">{{ resident.units_count ?? 0 }}</div></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.contacts.shared.leases') }}</CardTitle></CardHeader><CardContent><div class="text-2xl font-bold">{{ resident.leases_count ?? 0 }}</div></CardContent></Card>
            <Card><CardHeader class="pb-2"><CardTitle class="text-sm font-medium">{{ t('app.contacts.shared.status') }}</CardTitle></CardHeader><CardContent><Badge :variant="resident.active ? 'default' : 'secondary'">{{ resident.active ? t('app.common.active') : t('app.common.inactive') }}</Badge></CardContent></Card>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>{{ t('app.contacts.shared.contactInfo') }}</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.email') }}</span><span>{{ resident.email ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.phone') }}</span><span>{{ resident.phone_number }}</span></div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>{{ t('app.contacts.shared.personalInfo') }}</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.nationalId') }}</span><span>{{ resident.national_id ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.gender') }}</span><span>{{ resident.gender === 'male' ? t('app.contacts.shared.male') : resident.gender === 'female' ? t('app.contacts.shared.female') : '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.dateOfBirth') }}</span><span>{{ resident.georgian_birthdate ?? '—' }}</span></div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
