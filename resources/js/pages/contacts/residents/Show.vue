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

        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>{{ t('app.contacts.shared.contactInfo') }}</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.email') }}</span><span>{{ resident.email ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.phone') }}</span><span>{{ resident.phone_number }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.countryCode') }}</span><span>{{ resident.phone_country_code }}</span></div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>{{ t('app.contacts.shared.personalInfo') }}</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.nationalId') }}</span><span>{{ resident.national_id ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.gender') }}</span><span>{{ resident.gender === 'male' ? t('app.contacts.shared.male') : resident.gender === 'female' ? t('app.contacts.shared.female') : '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.dateOfBirth') }}</span><span>{{ resident.georgian_birthdate ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.units') }}</span><span>{{ resident.units_count ?? 0 }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.leases') }}</span><span>{{ resident.leases_count ?? 0 }}</span></div>
                </CardContent>
            </Card>
        </div>

        <Card v-if="resident.units && resident.units.length > 0">
            <CardHeader><CardTitle>{{ t('app.contacts.shared.units') }}</CardTitle></CardHeader>
            <CardContent>
                <div class="space-y-2">
                    <Link v-for="unit in resident.units" :key="unit.id" :href="`/units/${unit.id}`" class="flex items-center justify-between rounded-md border p-3 hover:bg-muted/50">
                        <span class="font-medium">{{ unit.name }}</span>
                        <span class="text-muted-foreground text-sm">{{ unit.community?.name ?? '' }} {{ unit.building?.name ? `/ ${unit.building.name}` : '' }}</span>
                    </Link>
                </div>
            </CardContent>
        </Card>

        <Card v-if="resident.leases && resident.leases.length > 0">
            <CardHeader><CardTitle>{{ t('app.contacts.shared.leases') }}</CardTitle></CardHeader>
            <CardContent>
                <div class="space-y-2">
                    <Link v-for="lease in resident.leases" :key="lease.id" :href="`/leases/${lease.id}`" class="flex items-center justify-between rounded-md border p-3 hover:bg-muted/50">
                        <span class="font-medium">{{ lease.contract_number }}</span>
                        <Badge>{{ lease.status?.name ?? '—' }}</Badge>
                    </Link>
                </div>
            </CardContent>
        </Card>

        <Card v-if="resident.dependents && resident.dependents.length > 0">
            <CardHeader><CardTitle>{{ t('app.contacts.residents.dependents') }}</CardTitle></CardHeader>
            <CardContent>
                <div class="space-y-2">
                    <div v-for="dep in resident.dependents" :key="dep.id" class="flex items-center justify-between rounded-md border p-3">
                        <span class="font-medium">{{ dep.first_name }} {{ dep.last_name }}</span>
                        <span class="text-muted-foreground text-sm">{{ dep.relation ?? '—' }}</span>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
