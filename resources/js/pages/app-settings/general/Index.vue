<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { Setting } from '@/types';

defineProps<{
    settingGroups: Record<string, Setting[]>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'App Settings', href: '/app-settings/general' },
            { title: 'General Settings', href: '/app-settings/general' },
        ],
    },
});

const groupLabels: Record<string, string> = {
    rental_contract_type: 'Rental Contract Types',
    payment_schedule: 'Payment Schedules',
    transaction_category: 'Transaction Categories',
    transaction_type: 'Transaction Types',
};

const showAddForm = ref(false);
const addForm = useForm({
    name_ar: '',
    name_en: '',
    type: '',
    parent_id: null as number | null,
});

function addSetting() {
    addForm.post('/app-settings/general', {
        preserveScroll: true,
        onSuccess: () => {
            addForm.reset();
            showAddForm.value = false;
        },
    });
}

function deleteSetting(id: number) {
    if (confirm('Delete this setting?')) {
        router.delete(`/app-settings/general/${id}`, { preserveScroll: true });
    }
}

const editingId = ref<number | null>(null);
const editForm = useForm({ name_ar: '', name_en: '' });

function startEdit(setting: Setting) {
    editingId.value = setting.id;
    editForm.name_ar = setting.name_ar ?? '';
    editForm.name_en = setting.name_en ?? '';
}

function saveEdit(id: number) {
    editForm.put(`/app-settings/general/${id}`, {
        preserveScroll: true,
        onSuccess: () => { editingId.value = null; },
    });
}

function cancelEdit() {
    editingId.value = null;
}
</script>

<template>
    <Head title="General Settings" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <Heading variant="small" title="General Settings" description="Manage lookup tables for contract types, payment schedules, and transaction categories." />
            <Button @click="showAddForm = !showAddForm">{{ showAddForm ? 'Cancel' : 'Add Setting' }}</Button>
        </div>

        <!-- Add form -->
        <Card v-if="showAddForm">
            <CardContent class="pt-6">
                <form @submit.prevent="addSetting" class="space-y-4">
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div class="grid gap-2">
                            <Label>Name (English)</Label>
                            <Input v-model="addForm.name_en" required placeholder="Setting name" />
                            <InputError :message="addForm.errors.name_en" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Name (Arabic)</Label>
                            <Input v-model="addForm.name_ar" required placeholder="اسم الإعداد" dir="rtl" />
                            <InputError :message="addForm.errors.name_ar" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Type</Label>
                            <Select v-model="addForm.type">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select type" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="(label, key) in groupLabels" :key="key" :value="key">{{ label }}</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="addForm.errors.type" />
                        </div>
                    </div>
                    <Button size="sm" :disabled="addForm.processing">Add Setting</Button>
                </form>
            </CardContent>
        </Card>

        <!-- Settings grouped by type -->
        <Card v-for="(settings, groupKey) in settingGroups" :key="groupKey">
            <CardHeader>
                <CardTitle>{{ groupLabels[groupKey as string] ?? groupKey }}</CardTitle>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name (EN)</TableHead>
                            <TableHead>Name (AR)</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="setting in settings" :key="setting.id">
                            <template v-if="editingId === setting.id">
                                <TableCell><Input v-model="editForm.name_en" class="h-8" /></TableCell>
                                <TableCell><Input v-model="editForm.name_ar" class="h-8" dir="rtl" /></TableCell>
                                <TableCell class="text-right space-x-2">
                                    <Button variant="default" size="sm" @click="saveEdit(setting.id)">Save</Button>
                                    <Button variant="outline" size="sm" @click="cancelEdit">Cancel</Button>
                                </TableCell>
                            </template>
                            <template v-else>
                                <TableCell>{{ setting.name_en ?? setting.name }}</TableCell>
                                <TableCell>{{ setting.name_ar ?? '—' }}</TableCell>
                                <TableCell class="text-right space-x-2">
                                    <Button variant="outline" size="sm" @click="startEdit(setting)">Edit</Button>
                                    <Button variant="destructive" size="sm" @click="deleteSetting(setting.id)">Delete</Button>
                                </TableCell>
                            </template>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>

        <p v-if="Object.keys(settingGroups).length === 0" class="text-muted-foreground text-center">No settings configured yet. Add your first setting above.</p>
    </div>
</template>
