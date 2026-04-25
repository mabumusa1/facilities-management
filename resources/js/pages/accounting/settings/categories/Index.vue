<script setup lang="ts">
import { Head, router, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, ref, watchEffect } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Sheet, SheetContent, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();

type TransactionCategory = {
    id: number;
    name_en: string;
    name_ar: string | null;
    category_type: 'income' | 'expense';
    is_active: boolean;
    is_default: boolean;
};

const props = defineProps<{
    categories: TransactionCategory[];
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.accounting'), href: '/transactions' },
            { title: t('app.accountingSettings.categories.pageTitle'), href: '/accounting/settings/categories' },
        ],
    });
});

// Tab state
const activeTab = ref<'income' | 'expense'>('income');

const filteredCategories = computed(() =>
    props.categories.filter((cat) => cat.category_type === activeTab.value),
);

// Sheet state
const sheetOpen = ref(false);
const editingCategory = ref<TransactionCategory | null>(null);

const categoryForm = useForm({
    name_en: '',
    name_ar: '',
    category_type: 'income' as 'income' | 'expense',
});

function openCreateSheet() {
    editingCategory.value = null;
    categoryForm.reset();
    categoryForm.category_type = activeTab.value;
    categoryForm.clearErrors();
    sheetOpen.value = true;
}

function openEditSheet(category: TransactionCategory) {
    editingCategory.value = category;
    categoryForm.name_en = category.name_en;
    categoryForm.name_ar = category.name_ar ?? '';
    categoryForm.category_type = category.category_type;
    categoryForm.clearErrors();
    sheetOpen.value = true;
}

function closeSheet() {
    sheetOpen.value = false;
    editingCategory.value = null;
}

function submitCategoryForm() {
    if (editingCategory.value) {
        categoryForm.put(`/accounting/settings/categories/${editingCategory.value.id}`, {
            preserveScroll: true,
            onSuccess: () => closeSheet(),
        });
    } else {
        categoryForm.post('/accounting/settings/categories', {
            preserveScroll: true,
            onSuccess: () => closeSheet(),
        });
    }
}

// Toggle active state
function toggleActive(category: TransactionCategory) {
    router.post(
        `/accounting/settings/categories/${category.id}/toggle`,
        {},
        { preserveScroll: true },
    );
}

// Deactivate confirm dialog
const deactivateDialogOpen = ref(false);
const categoryToDeactivate = ref<TransactionCategory | null>(null);
const deactivateForm = useForm({});

function openDeactivateDialog(category: TransactionCategory) {
    categoryToDeactivate.value = category;
    deactivateDialogOpen.value = true;
}

function closeDeactivateDialog() {
    deactivateDialogOpen.value = false;
    categoryToDeactivate.value = null;
}

function confirmDeactivate() {
    if (! categoryToDeactivate.value) {
        return;
    }
    deactivateForm.post(`/accounting/settings/categories/${categoryToDeactivate.value.id}/toggle`, {
        preserveScroll: true,
        onSuccess: () => closeDeactivateDialog(),
    });
}

// Delete
const deleteForm = useForm({});

function deleteCategory(category: TransactionCategory) {
    deleteForm.delete(`/accounting/settings/categories/${category.id}`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head :title="t('app.accountingSettings.categories.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading
                variant="small"
                :title="t('app.accountingSettings.categories.heading')"
                :description="t('app.accountingSettings.categories.description')"
            />
            <Button @click="openCreateSheet">
                {{ t('app.accountingSettings.categories.addCategory') }}
            </Button>
        </div>

        <!-- Tabs -->
        <div role="tablist" aria-label="Category type" class="flex gap-1 border-b">
            <button
                id="tab-income"
                role="tab"
                :aria-selected="activeTab === 'income'"
                aria-controls="panel-income"
                class="relative px-4 py-2 text-sm font-medium transition-colors"
                :class="activeTab === 'income' ? 'text-foreground after:bg-primary after:absolute after:bottom-0 after:start-0 after:end-0 after:h-0.5' : 'text-muted-foreground hover:text-foreground'"
                @click="activeTab = 'income'"
            >
                {{ t('app.accountingSettings.categories.tabIncome') }}
            </button>
            <button
                id="tab-expense"
                role="tab"
                :aria-selected="activeTab === 'expense'"
                aria-controls="panel-expense"
                class="relative px-4 py-2 text-sm font-medium transition-colors"
                :class="activeTab === 'expense' ? 'text-foreground after:bg-primary after:absolute after:bottom-0 after:start-0 after:end-0 after:h-0.5' : 'text-muted-foreground hover:text-foreground'"
                @click="activeTab = 'expense'"
            >
                {{ t('app.accountingSettings.categories.tabExpense') }}
            </button>
        </div>

        <!-- Category table panel -->
        <div
            id="panel-income"
            role="tabpanel"
            aria-labelledby="tab-income"
            :hidden="activeTab !== 'income'"
        >
            <div v-if="activeTab === 'income'" class="rounded-lg border">
                <Table>
                    <caption class="sr-only">{{ t('app.accountingSettings.categories.tabIncome') }}</caption>
                    <TableHeader>
                        <TableRow>
                            <TableHead scope="col">{{ t('app.accountingSettings.categories.colNameEn') }}</TableHead>
                            <TableHead scope="col">{{ t('app.accountingSettings.categories.colNameAr') }}</TableHead>
                            <TableHead scope="col">{{ t('app.accountingSettings.categories.colStatus') }}</TableHead>
                            <TableHead scope="col">{{ t('app.accountingSettings.categories.colActions') }}</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="cat in filteredCategories" :key="cat.id">
                            <TableCell>
                                <span class="flex flex-wrap items-center gap-2">
                                    {{ cat.name_en }}
                                    <Badge v-if="cat.is_default" variant="outline" class="text-xs">
                                        {{ t('app.accountingSettings.categories.defaultBadge') }}
                                    </Badge>
                                </span>
                            </TableCell>
                            <TableCell>
                                <span dir="rtl" lang="ar">{{ cat.name_ar ?? '—' }}</span>
                            </TableCell>
                            <TableCell>
                                <Badge :variant="cat.is_active ? 'default' : 'secondary'">
                                    {{ cat.is_active
                                        ? t('app.accountingSettings.categories.statusActive')
                                        : t('app.accountingSettings.categories.statusInactive') }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        :aria-label="`${t('app.accountingSettings.categories.editAction')} ${cat.name_en}`"
                                        @click="openEditSheet(cat)"
                                    >
                                        {{ t('app.accountingSettings.categories.editAction') }}
                                    </Button>
                                    <Button
                                        v-if="cat.is_active"
                                        variant="ghost"
                                        size="sm"
                                        :aria-label="`${t('app.accountingSettings.categories.deactivateAction')} ${cat.name_en}`"
                                        @click="openDeactivateDialog(cat)"
                                    >
                                        {{ t('app.accountingSettings.categories.deactivateAction') }}
                                    </Button>
                                    <Button
                                        v-else
                                        variant="ghost"
                                        size="sm"
                                        :aria-label="`${t('app.accountingSettings.categories.reactivateAction')} ${cat.name_en}`"
                                        @click="toggleActive(cat)"
                                    >
                                        {{ t('app.accountingSettings.categories.reactivateAction') }}
                                    </Button>
                                    <Button
                                        v-if="!cat.is_default"
                                        variant="ghost"
                                        size="sm"
                                        class="text-destructive hover:text-destructive"
                                        :disabled="deleteForm.processing"
                                        :aria-label="`${t('app.accountingSettings.categories.deleteAction')} ${cat.name_en}`"
                                        @click="deleteCategory(cat)"
                                    >
                                        {{ t('app.accountingSettings.categories.deleteAction') }}
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="filteredCategories.length === 0">
                            <TableCell :colspan="4" class="text-muted-foreground py-12 text-center">
                                {{ t('app.accountingSettings.categories.noCategories') }}
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>

        <div
            id="panel-expense"
            role="tabpanel"
            aria-labelledby="tab-expense"
            :hidden="activeTab !== 'expense'"
        >
            <div v-if="activeTab === 'expense'" class="rounded-lg border">
                <Table>
                    <caption class="sr-only">{{ t('app.accountingSettings.categories.tabExpense') }}</caption>
                    <TableHeader>
                        <TableRow>
                            <TableHead scope="col">{{ t('app.accountingSettings.categories.colNameEn') }}</TableHead>
                            <TableHead scope="col">{{ t('app.accountingSettings.categories.colNameAr') }}</TableHead>
                            <TableHead scope="col">{{ t('app.accountingSettings.categories.colStatus') }}</TableHead>
                            <TableHead scope="col">{{ t('app.accountingSettings.categories.colActions') }}</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="cat in filteredCategories" :key="cat.id">
                            <TableCell>
                                <span class="flex flex-wrap items-center gap-2">
                                    {{ cat.name_en }}
                                    <Badge v-if="cat.is_default" variant="outline" class="text-xs">
                                        {{ t('app.accountingSettings.categories.defaultBadge') }}
                                    </Badge>
                                </span>
                            </TableCell>
                            <TableCell>
                                <span dir="rtl" lang="ar">{{ cat.name_ar ?? '—' }}</span>
                            </TableCell>
                            <TableCell>
                                <Badge :variant="cat.is_active ? 'default' : 'secondary'">
                                    {{ cat.is_active
                                        ? t('app.accountingSettings.categories.statusActive')
                                        : t('app.accountingSettings.categories.statusInactive') }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        :aria-label="`${t('app.accountingSettings.categories.editAction')} ${cat.name_en}`"
                                        @click="openEditSheet(cat)"
                                    >
                                        {{ t('app.accountingSettings.categories.editAction') }}
                                    </Button>
                                    <Button
                                        v-if="cat.is_active"
                                        variant="ghost"
                                        size="sm"
                                        :aria-label="`${t('app.accountingSettings.categories.deactivateAction')} ${cat.name_en}`"
                                        @click="openDeactivateDialog(cat)"
                                    >
                                        {{ t('app.accountingSettings.categories.deactivateAction') }}
                                    </Button>
                                    <Button
                                        v-else
                                        variant="ghost"
                                        size="sm"
                                        :aria-label="`${t('app.accountingSettings.categories.reactivateAction')} ${cat.name_en}`"
                                        @click="toggleActive(cat)"
                                    >
                                        {{ t('app.accountingSettings.categories.reactivateAction') }}
                                    </Button>
                                    <Button
                                        v-if="!cat.is_default"
                                        variant="ghost"
                                        size="sm"
                                        class="text-destructive hover:text-destructive"
                                        :disabled="deleteForm.processing"
                                        :aria-label="`${t('app.accountingSettings.categories.deleteAction')} ${cat.name_en}`"
                                        @click="deleteCategory(cat)"
                                    >
                                        {{ t('app.accountingSettings.categories.deleteAction') }}
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="filteredCategories.length === 0">
                            <TableCell :colspan="4" class="text-muted-foreground py-12 text-center">
                                {{ t('app.accountingSettings.categories.noCategories') }}
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </div>

    <!-- Create / Edit Sheet -->
    <Sheet :open="sheetOpen" @update:open="(val) => { if (!val) closeSheet(); }">
        <SheetContent side="right" class="flex flex-col gap-0 p-0" role="dialog" aria-modal="true">
            <SheetHeader class="border-b p-6">
                <SheetTitle>
                    {{ editingCategory
                        ? t('app.accountingSettings.categories.sheetEditTitle')
                        : t('app.accountingSettings.categories.sheetAddTitle') }}
                </SheetTitle>
            </SheetHeader>

            <form class="flex flex-1 flex-col gap-5 overflow-y-auto p-6" @submit.prevent="submitCategoryForm">
                <!-- Category type — locked on edit -->
                <div class="grid gap-2">
                    <Label>{{ t('app.accountingSettings.categories.labelCategoryType') }} *</Label>
                    <div v-if="editingCategory" class="text-muted-foreground text-sm">
                        {{ categoryForm.category_type === 'income'
                            ? t('app.accountingSettings.categories.typeIncome')
                            : t('app.accountingSettings.categories.typeExpense') }}
                    </div>
                    <div v-else class="flex gap-4">
                        <label class="flex cursor-pointer items-center gap-2 text-sm">
                            <input
                                v-model="categoryForm.category_type"
                                type="radio"
                                value="income"
                                class="accent-primary"
                            />
                            {{ t('app.accountingSettings.categories.typeIncome') }}
                        </label>
                        <label class="flex cursor-pointer items-center gap-2 text-sm">
                            <input
                                v-model="categoryForm.category_type"
                                type="radio"
                                value="expense"
                                class="accent-primary"
                            />
                            {{ t('app.accountingSettings.categories.typeExpense') }}
                        </label>
                    </div>
                    <InputError :message="categoryForm.errors.category_type" />
                </div>

                <div class="grid gap-2">
                    <Label for="name_en">{{ t('app.accountingSettings.categories.labelNameEn') }} *</Label>
                    <Input
                        id="name_en"
                        v-model="categoryForm.name_en"
                        dir="ltr"
                        required
                        :placeholder="t('app.accountingSettings.categories.labelNameEn')"
                    />
                    <InputError :message="categoryForm.errors.name_en" />
                </div>

                <div class="grid gap-2">
                    <Label for="name_ar">{{ t('app.accountingSettings.categories.labelNameAr') }} *</Label>
                    <Input
                        id="name_ar"
                        v-model="categoryForm.name_ar"
                        dir="rtl"
                        required
                        :placeholder="t('app.accountingSettings.categories.labelNameAr')"
                    />
                    <InputError :message="categoryForm.errors.name_ar" />
                </div>

                <SheetFooter class="mt-auto flex justify-end gap-2 border-t pt-4">
                    <Button
                        type="button"
                        variant="outline"
                        :disabled="categoryForm.processing"
                        @click="closeSheet"
                    >
                        {{ t('app.accountingSettings.categories.cancelButton') }}
                    </Button>
                    <Button type="submit" :disabled="categoryForm.processing">
                        {{ categoryForm.processing
                            ? t('app.accountingSettings.categories.saving')
                            : t('app.accountingSettings.categories.saveButton') }}
                    </Button>
                </SheetFooter>
            </form>
        </SheetContent>
    </Sheet>

    <!-- Deactivate Confirm Dialog -->
    <Dialog :open="deactivateDialogOpen" @update:open="(val) => { if (!val) closeDeactivateDialog(); }">
        <DialogContent role="alertdialog" aria-modal="true">
            <DialogHeader>
                <DialogTitle>
                    {{ t('app.accountingSettings.categories.deactivateConfirmTitle') }}
                </DialogTitle>
                <DialogDescription>
                    {{ t('app.accountingSettings.categories.deactivateConfirmBody') }}
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button
                    variant="outline"
                    :disabled="deactivateForm.processing"
                    @click="closeDeactivateDialog"
                >
                    {{ t('app.accountingSettings.categories.keepActiveButton') }}
                </Button>
                <Button
                    variant="destructive"
                    :disabled="deactivateForm.processing"
                    @click="confirmDeactivate"
                >
                    {{ t('app.accountingSettings.categories.deactivateConfirmButton') }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
