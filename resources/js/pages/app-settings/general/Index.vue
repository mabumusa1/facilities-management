<script setup lang="ts">
import { Head, router, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, ref, watchEffect } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useI18n } from '@/composables/useI18n';
import type { Setting } from '@/types';

const { t } = useI18n();

defineProps<{
    settingGroups: Record<string, Setting[]>;
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.appSettings.common.appSettings'), href: '/app-settings/general' },
            { title: t('app.appSettings.general.pageTitle'), href: '/app-settings/general' },
        ],
    });
});

const groupLabels = computed<Record<string, string>>(() => ({
    rental_contract_type: t('app.appSettings.general.rentalContractTypes'),
    payment_schedule: t('app.appSettings.general.paymentSchedules'),
    transaction_category: t('app.appSettings.general.transactionCategories'),
    transaction_type: t('app.appSettings.general.transactionTypes'),
    calculation_basis: t('app.appSettings.general.calculationBasis'),
    fit_out_status: t('app.appSettings.general.fitOutStatus'),
    payment_frequency: t('app.appSettings.general.paymentFrequency'),
}));

function resolveGroupLabel(groupKey: string): string {
    return groupLabels.value[groupKey]
        ?? t('app.appSettings.general.unknownType', { type: groupKey.replaceAll('_', ' ') });
}

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
    if (confirm(t('app.appSettings.general.deleteSettingConfirm'))) {
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
    <Head :title="t('app.appSettings.general.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <Heading variant="small" :title="t('app.appSettings.general.heading')" :description="t('app.appSettings.general.description')" />
            <Button @click="showAddForm = !showAddForm">{{ showAddForm ? t('app.appSettings.general.cancelAdd') : t('app.appSettings.general.addSetting') }}</Button>
        </div>

        <!-- Add form -->
        <Card v-if="showAddForm">
            <CardContent class="pt-6">
                <form @submit.prevent="addSetting" class="space-y-4">
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div class="grid gap-2">
                            <Label>{{ t('app.appSettings.general.nameEn') }}</Label>
                            <Input v-model="addForm.name_en" required :placeholder="t('app.appSettings.general.settingNamePlaceholder')" />
                            <InputError :message="addForm.errors.name_en" />
                        </div>
                        <div class="grid gap-2">
                            <Label>{{ t('app.appSettings.general.nameAr') }}</Label>
                            <Input v-model="addForm.name_ar" required :placeholder="t('app.appSettings.general.settingNameArPlaceholder')" dir="rtl" />
                            <InputError :message="addForm.errors.name_ar" />
                        </div>
                        <div class="grid gap-2">
                            <Label>{{ t('app.appSettings.general.type') }}</Label>
                            <Select v-model="addForm.type">
                                <SelectTrigger class="w-full">
                                    <SelectValue :placeholder="t('app.appSettings.general.selectType')" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="(label, key) in groupLabels" :key="key" :value="key">{{ label }}</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="addForm.errors.type" />
                        </div>
                    </div>
                    <Button size="sm" :disabled="addForm.processing">{{ t('app.appSettings.general.addSettingSubmit') }}</Button>
                </form>
            </CardContent>
        </Card>

        <!-- Settings grouped by type -->
        <Card v-for="(settings, groupKey) in settingGroups" :key="groupKey">
            <CardHeader>
                <CardTitle>{{ resolveGroupLabel(String(groupKey)) }}</CardTitle>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>{{ t('app.appSettings.general.nameEn') }}</TableHead>
                            <TableHead>{{ t('app.appSettings.general.nameAr') }}</TableHead>
                            <TableHead class="text-right">{{ t('app.appSettings.common.actions') }}</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="setting in settings" :key="setting.id">
                            <template v-if="editingId === setting.id">
                                <TableCell><Input v-model="editForm.name_en" class="h-8" /></TableCell>
                                <TableCell><Input v-model="editForm.name_ar" class="h-8" dir="rtl" /></TableCell>
                                <TableCell class="text-right space-x-2">
                                    <Button variant="default" size="sm" @click="saveEdit(setting.id)">{{ t('app.appSettings.common.save') }}</Button>
                                    <Button variant="outline" size="sm" @click="cancelEdit">{{ t('app.appSettings.common.cancel') }}</Button>
                                </TableCell>
                            </template>
                            <template v-else>
                                <TableCell>{{ setting.name_en ?? setting.name }}</TableCell>
                                <TableCell>{{ setting.name_ar ?? '—' }}</TableCell>
                                <TableCell class="text-right space-x-2">
                                    <Button variant="outline" size="sm" @click="startEdit(setting)">{{ t('app.appSettings.common.edit') }}</Button>
                                    <Button variant="destructive" size="sm" @click="deleteSetting(setting.id)">{{ t('app.appSettings.common.delete') }}</Button>
                                </TableCell>
                            </template>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>

        <p v-if="Object.keys(settingGroups).length === 0" class="text-muted-foreground text-center">{{ t('app.appSettings.general.noSettings') }}</p>
    </div>
</template>
