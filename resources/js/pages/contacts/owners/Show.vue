<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useI18n } from '@/composables/useI18n';
import type { Owner } from '@/types';

const props = defineProps<{ owner: Owner }>();
const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.contacts.owners.pageTitle'), href: '/owners' },
            { title: `${props.owner.first_name} ${props.owner.last_name}`, href: '#' },
        ],
    });
});

function deleteOwner() { if (confirm(t('app.contacts.shared.confirmDeletePrompt'))) { router.delete(`/owners/${props.owner.id}`); } }
</script>

<template>
    <Head :title="`${owner.first_name} ${owner.last_name}`" />
    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">{{ owner.first_name }} {{ owner.last_name }}</h2>
                <Badge :variant="owner.active ? 'default' : 'secondary'">{{ owner.active ? t('app.common.active') : t('app.common.inactive') }}</Badge>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child><Link :href="`/owners/${owner.id}/edit`">{{ t('app.common.edit') }}</Link></Button>
                <Button variant="destructive" @click="deleteOwner">{{ t('app.common.delete') }}</Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>{{ t('app.contacts.shared.contactInfo') }}</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.email') }}</span><span>{{ owner.email ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.phone') }}</span><span>{{ owner.phone_number }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.countryCode') }}</span><span>{{ owner.phone_country_code }}</span></div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>{{ t('app.contacts.shared.personalInfo') }}</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.nationalId') }}</span><span>{{ owner.national_id ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.gender') }}</span><span>{{ owner.gender === 'male' ? t('app.contacts.shared.male') : owner.gender === 'female' ? t('app.contacts.shared.female') : '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.dateOfBirth') }}</span><span>{{ owner.georgian_birthdate ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.units') }}</span><span>{{ owner.units_count ?? 0 }}</span></div>
                </CardContent>
            </Card>
        </div>

        <Card v-if="owner.units && owner.units.length > 0">
            <CardHeader><CardTitle>{{ t('app.contacts.shared.units') }}</CardTitle></CardHeader>
            <CardContent>
                <div class="space-y-2">
                    <Link v-for="unit in owner.units" :key="unit.id" :href="`/units/${unit.id}`" class="flex items-center justify-between rounded-md border p-3 hover:bg-muted/50">
                        <span class="font-medium">{{ unit.name }}</span>
                        <span class="text-muted-foreground text-sm">{{ unit.community?.name ?? '' }} {{ unit.building?.name ? `/ ${unit.building.name}` : '' }}</span>
                    </Link>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
