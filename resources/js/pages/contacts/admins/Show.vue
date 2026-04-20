<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Admin } from '@/types';

const props = defineProps<{ admin: Admin }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Admins', href: '/admins' }, { title: 'Details', href: '#' }] } });
</script>

<template>
    <Head :title="`${admin.first_name} ${admin.last_name}`" />
    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">{{ admin.first_name }} {{ admin.last_name }}</h2>
                <div class="mt-1 flex items-center gap-2">
                    <Badge>{{ admin.role }}</Badge>
                    <Badge :variant="admin.active ? 'default' : 'secondary'">{{ admin.active ? 'Active' : 'Inactive' }}</Badge>
                </div>
            </div>
        </div>

        <Card>
            <CardHeader><CardTitle>Info</CardTitle></CardHeader>
            <CardContent class="space-y-2">
                <div class="flex justify-between"><span class="text-muted-foreground">Email</span><span>{{ admin.email ?? '—' }}</span></div>
                <div class="flex justify-between"><span class="text-muted-foreground">Phone</span><span>{{ admin.phone_number }}</span></div>
                <div class="flex justify-between"><span class="text-muted-foreground">National ID</span><span>{{ admin.national_id ?? '—' }}</span></div>
                <div class="flex justify-between"><span class="text-muted-foreground">Last Login</span><span>{{ admin.last_login_at ?? '—' }}</span></div>
            </CardContent>
        </Card>
    </div>
</template>
