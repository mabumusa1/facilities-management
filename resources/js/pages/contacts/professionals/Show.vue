<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Professional } from '@/types';

const props = defineProps<{ professional: Professional }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Professionals', href: '/professionals' }, { title: 'Details', href: '#' }] } });

function deleteProfessional() { if (confirm('Are you sure?')) { router.delete(`/professionals/${props.professional.id}`); } }
</script>

<template>
    <Head :title="`${professional.first_name} ${professional.last_name}`" />
    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">{{ professional.first_name }} {{ professional.last_name }}</h2>
                <Badge :variant="professional.active ? 'default' : 'secondary'" class="mt-1">{{ professional.active ? 'Active' : 'Inactive' }}</Badge>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child><a :href="`/professionals/${professional.id}/edit`">Edit</a></Button>
                <Button variant="destructive" @click="deleteProfessional">Delete</Button>
            </div>
        </div>

        <Card>
            <CardHeader><CardTitle>Contact Info</CardTitle></CardHeader>
            <CardContent class="space-y-2">
                <div class="flex justify-between"><span class="text-muted-foreground">Email</span><span>{{ professional.email ?? '—' }}</span></div>
                <div class="flex justify-between"><span class="text-muted-foreground">Phone</span><span>{{ professional.phone_number }}</span></div>
                <div class="flex justify-between"><span class="text-muted-foreground">National ID</span><span>{{ professional.national_id ?? '—' }}</span></div>
            </CardContent>
        </Card>
    </div>
</template>
