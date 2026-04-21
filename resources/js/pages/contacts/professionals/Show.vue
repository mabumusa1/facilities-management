<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useI18n } from '@/composables/useI18n';
import type { Professional } from '@/types';

const props = defineProps<{ professional: Professional }>();
const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.contacts.professionals.pageTitle'), href: '/professionals' },
            { title: `${props.professional.first_name} ${props.professional.last_name}`, href: '#' },
        ],
    });
});

function deleteProfessional() { if (confirm(t('app.contacts.shared.confirmDeletePrompt'))) { router.delete(`/professionals/${props.professional.id}`); } }
</script>

<template>
    <Head :title="`${professional.first_name} ${professional.last_name}`" />
    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">{{ professional.first_name }} {{ professional.last_name }}</h2>
                <Badge :variant="professional.active ? 'default' : 'secondary'" class="mt-1">{{ professional.active ? t('app.common.active') : t('app.common.inactive') }}</Badge>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child><Link :href="`/professionals/${professional.id}/edit`">{{ t('app.common.edit') }}</Link></Button>
                <Button variant="destructive" @click="deleteProfessional">{{ t('app.common.delete') }}</Button>
            </div>
        </div>

        <Card>
            <CardHeader><CardTitle>{{ t('app.contacts.shared.contactInfo') }}</CardTitle></CardHeader>
            <CardContent class="space-y-2">
                <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.email') }}</span><span>{{ professional.email ?? '—' }}</span></div>
                <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.phone') }}</span><span>{{ professional.phone_number }}</span></div>
                <div class="flex justify-between"><span class="text-muted-foreground">{{ t('app.contacts.shared.nationalId') }}</span><span>{{ professional.national_id ?? '—' }}</span></div>
            </CardContent>
        </Card>
    </div>
</template>
