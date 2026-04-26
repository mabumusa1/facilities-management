<script setup lang="ts">
import { Head, router, setLayoutProps, useForm } from '@inertiajs/vue3';
import { ChevronDown, ChevronRight } from 'lucide-vue-next';
import { ref, watchEffect } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Sheet, SheetContent, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();

type Community = {
    id: number;
    name: string;
};

type Assignee = {
    id: number;
    name: string;
};

type ServiceSubcategory = {
    id: number;
    name_en: string;
    name_ar: string;
    response_sla_hours: number | null;
    resolution_sla_hours: number | null;
    status: 'active' | 'inactive';
};

type ServiceCategory = {
    id: number;
    name_en: string;
    name_ar: string;
    icon: string;
    response_sla_hours: number | null;
    resolution_sla_hours: number | null;
    require_completion_photo: boolean;
    status: 'active' | 'inactive';
    subcategories_count: number;
    default_assignee: Assignee | null;
    communities: Community[];
    subcategories: ServiceSubcategory[];
};

const props = defineProps<{
    categories: ServiceCategory[];
    communities: Community[];
    assignees: Assignee[];
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.serviceCategories.pageTitle'), href: '/services/categories' },
        ],
    });
});

// Accordion expanded state
const expandedCategories = ref<Set<number>>(new Set());

function toggleCategory(id: number) {
    if (expandedCategories.value.has(id)) {
        expandedCategories.value.delete(id);
    } else {
        expandedCategories.value.add(id);
    }
}

// Available icons
const ICONS = ['🔧', '💡', '❄️', '🧹', '🐜', '🔌', '🚿', '🏠', '🔨', '🛠️', '🪟', '🛁'];

// Category sheet state
const categorySheetOpen = ref(false);
const editingCategory = ref<ServiceCategory | null>(null);

const categoryForm = useForm({
    name_en: '',
    name_ar: '',
    icon: '🔧',
    response_sla_hours: null as number | null,
    resolution_sla_hours: null as number | null,
    default_assignee_id: null as number | null,
    require_completion_photo: false,
    status: 'active' as 'active' | 'inactive',
    community_ids: [] as number[],
});

function openCreateCategorySheet() {
    editingCategory.value = null;
    categoryForm.reset();
    categoryForm.clearErrors();
    categorySheetOpen.value = true;
}

function openEditCategorySheet(category: ServiceCategory) {
    editingCategory.value = category;
    categoryForm.name_en = category.name_en;
    categoryForm.name_ar = category.name_ar;
    categoryForm.icon = category.icon;
    categoryForm.response_sla_hours = category.response_sla_hours;
    categoryForm.resolution_sla_hours = category.resolution_sla_hours;
    categoryForm.default_assignee_id = category.default_assignee?.id ?? null;
    categoryForm.require_completion_photo = category.require_completion_photo;
    categoryForm.status = category.status;
    categoryForm.community_ids = category.communities.map((c) => c.id);
    categoryForm.clearErrors();
    categorySheetOpen.value = true;
}

function closeCategorySheet() {
    categorySheetOpen.value = false;
    editingCategory.value = null;
}

function toggleCommunity(communityId: number) {
    const idx = categoryForm.community_ids.indexOf(communityId);
    if (idx >= 0) {
        categoryForm.community_ids.splice(idx, 1);
    } else {
        categoryForm.community_ids.push(communityId);
    }
}

function submitCategoryForm() {
    if (editingCategory.value) {
        categoryForm.put(`/services/categories/${editingCategory.value.id}`, {
            preserveScroll: true,
            onSuccess: () => closeCategorySheet(),
        });
    } else {
        categoryForm.post('/services/categories', {
            preserveScroll: true,
            onSuccess: () => closeCategorySheet(),
        });
    }
}

// Subcategory sheet state
const subcategorySheetOpen = ref(false);
const editingSubcategory = ref<ServiceSubcategory | null>(null);
const subcategoryParentId = ref<number | null>(null);

const subcategoryForm = useForm({
    name_en: '',
    name_ar: '',
    response_sla_hours: null as number | null,
    resolution_sla_hours: null as number | null,
    status: 'active' as 'active' | 'inactive',
});

function openCreateSubcategorySheet(categoryId: number) {
    editingSubcategory.value = null;
    subcategoryParentId.value = categoryId;
    subcategoryForm.reset();
    subcategoryForm.clearErrors();
    subcategorySheetOpen.value = true;
}

function openEditSubcategorySheet(categoryId: number, subcategory: ServiceSubcategory) {
    editingSubcategory.value = subcategory;
    subcategoryParentId.value = categoryId;
    subcategoryForm.name_en = subcategory.name_en;
    subcategoryForm.name_ar = subcategory.name_ar;
    subcategoryForm.response_sla_hours = subcategory.response_sla_hours;
    subcategoryForm.resolution_sla_hours = subcategory.resolution_sla_hours;
    subcategoryForm.status = subcategory.status;
    subcategoryForm.clearErrors();
    subcategorySheetOpen.value = true;
}

function closeSubcategorySheet() {
    subcategorySheetOpen.value = false;
    editingSubcategory.value = null;
    subcategoryParentId.value = null;
}

function submitSubcategoryForm() {
    const parentId = subcategoryParentId.value;
    if (! parentId) {
        return;
    }

    if (editingSubcategory.value) {
        subcategoryForm.put(`/services/categories/${parentId}/subcategories/${editingSubcategory.value.id}`, {
            preserveScroll: true,
            onSuccess: () => closeSubcategorySheet(),
        });
    } else {
        subcategoryForm.post(`/services/categories/${parentId}/subcategories`, {
            preserveScroll: true,
            onSuccess: () => closeSubcategorySheet(),
        });
    }
}

// Toggle category status
function toggleCategoryStatus(category: ServiceCategory) {
    router.post(`/services/categories/${category.id}/toggle-status`, {}, { preserveScroll: true });
}

// Toggle subcategory status (via update)
function toggleSubcategoryStatus(categoryId: number, subcategory: ServiceSubcategory) {
    router.put(
        `/services/categories/${categoryId}/subcategories/${subcategory.id}`,
        {
            name_en: subcategory.name_en,
            name_ar: subcategory.name_ar,
            response_sla_hours: subcategory.response_sla_hours,
            resolution_sla_hours: subcategory.resolution_sla_hours,
            status: subcategory.status === 'active' ? 'inactive' : 'active',
        },
        { preserveScroll: true },
    );
}

// Delete subcategory
function deleteSubcategory(categoryId: number, subcategoryId: number) {
    router.delete(`/services/categories/${categoryId}/subcategories/${subcategoryId}`, { preserveScroll: true });
}
</script>

<template>
    <Head :title="t('app.serviceCategories.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading
                variant="small"
                :title="t('app.serviceCategories.heading')"
                :description="t('app.serviceCategories.description')"
            />
            <Button @click="openCreateCategorySheet">
                {{ t('app.serviceCategories.newCategory') }}
            </Button>
        </div>

        <!-- Empty state -->
        <div
            v-if="categories.length === 0"
            class="text-muted-foreground flex flex-col items-center gap-4 py-16 text-center"
        >
            <p>{{ t('app.serviceCategories.noCategories') }}</p>
            <Button @click="openCreateCategorySheet">
                {{ t('app.serviceCategories.newCategory') }}
            </Button>
        </div>

        <!-- Category accordion list -->
        <div v-else class="flex flex-col gap-2">
            <Collapsible
                v-for="category in categories"
                :key="category.id"
                :open="expandedCategories.has(category.id)"
                class="rounded-lg border"
                @update:open="() => toggleCategory(category.id)"
            >
                <!-- Category header row -->
                <CollapsibleTrigger
                    :id="`category-${category.id}-heading`"
                    class="flex w-full items-center gap-3 px-4 py-3 text-start"
                    :aria-expanded="expandedCategories.has(category.id)"
                    :aria-controls="`subcategory-list-${category.id}`"
                >
                    <ChevronRight
                        v-if="!expandedCategories.has(category.id)"
                        class="text-muted-foreground size-4 shrink-0 ltr:rotate-0 rtl:rotate-180"
                        aria-hidden="true"
                    />
                    <ChevronDown
                        v-else
                        class="text-muted-foreground size-4 shrink-0"
                        aria-hidden="true"
                    />

                    <span class="text-lg" aria-hidden="true">{{ category.icon }}</span>

                    <div class="flex flex-1 flex-col gap-0.5">
                        <span class="font-medium">{{ category.name_en }}</span>
                        <span class="text-muted-foreground text-sm" dir="rtl" lang="ar">{{ category.name_ar }}</span>
                    </div>

                    <div class="flex items-center gap-4 text-sm">
                        <span v-if="category.response_sla_hours" class="text-muted-foreground">
                            {{ t('app.serviceCategories.responseSlaLabel', { hours: category.response_sla_hours }) }}
                        </span>
                        <span v-if="category.resolution_sla_hours" class="text-muted-foreground">
                            {{ t('app.serviceCategories.resolutionSlaLabel', { hours: category.resolution_sla_hours }) }}
                        </span>

                        <Badge :variant="category.status === 'active' ? 'default' : 'secondary'">
                            {{ category.status === 'active'
                                ? t('app.serviceCategories.statusActive')
                                : t('app.serviceCategories.statusInactive') }}
                        </Badge>

                        <span class="text-muted-foreground text-xs">
                            ({{ category.subcategories_count }})
                        </span>
                    </div>

                    <div class="flex items-center gap-1" @click.stop>
                        <Button
                            variant="ghost"
                            size="sm"
                            :aria-label="`${t('app.serviceCategories.editAction')} ${category.name_en}`"
                            @click="openEditCategorySheet(category)"
                        >
                            {{ t('app.serviceCategories.editAction') }}
                        </Button>
                        <Button
                            variant="ghost"
                            size="sm"
                            :aria-label="`${category.status === 'active' ? t('app.serviceCategories.disableAction') : t('app.serviceCategories.enableAction')} ${category.name_en}`"
                            @click="toggleCategoryStatus(category)"
                        >
                            {{ category.status === 'active'
                                ? t('app.serviceCategories.disableAction')
                                : t('app.serviceCategories.enableAction') }}
                        </Button>
                    </div>
                </CollapsibleTrigger>

                <!-- Subcategory list -->
                <CollapsibleContent
                    :id="`subcategory-list-${category.id}`"
                    role="region"
                    :aria-labelledby="`category-${category.id}-heading`"
                >
                    <div class="border-t px-4 pb-4">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-muted-foreground border-b text-xs">
                                    <th class="py-2 text-start font-medium">{{ t('app.serviceCategories.colSubName') }}</th>
                                    <th class="py-2 text-start font-medium">{{ t('app.serviceCategories.colSubSla') }}</th>
                                    <th class="py-2 text-end font-medium">{{ t('app.serviceCategories.colSubActions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="sub in category.subcategories"
                                    :key="sub.id"
                                    class="hover:bg-muted/50 border-b last:border-0"
                                >
                                    <td class="py-2">
                                        <div class="flex flex-col gap-0.5">
                                            <span>{{ sub.name_en }}</span>
                                            <span class="text-muted-foreground text-xs" dir="rtl" lang="ar">{{ sub.name_ar }}</span>
                                        </div>
                                    </td>
                                    <td class="py-2">
                                        <div class="flex flex-col gap-0.5 text-xs">
                                            <span v-if="sub.response_sla_hours">
                                                {{ t('app.serviceCategories.responseSlaLabel', { hours: sub.response_sla_hours }) }}
                                            </span>
                                            <span v-else class="text-muted-foreground italic">
                                                {{ t('app.serviceCategories.inheritedSla') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-2 text-end">
                                        <div class="flex items-center justify-end gap-1">
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                :aria-label="`${t('app.serviceCategories.editAction')} ${sub.name_en}`"
                                                @click="openEditSubcategorySheet(category.id, sub)"
                                            >
                                                {{ t('app.serviceCategories.editAction') }}
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                :aria-label="`${sub.status === 'active' ? t('app.serviceCategories.disableAction') : t('app.serviceCategories.enableAction')} ${sub.name_en}`"
                                                @click="toggleSubcategoryStatus(category.id, sub)"
                                            >
                                                {{ sub.status === 'active'
                                                    ? t('app.serviceCategories.disableAction')
                                                    : t('app.serviceCategories.enableAction') }}
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                class="text-destructive hover:text-destructive"
                                                :aria-label="`${t('app.serviceCategories.deleteAction')} ${sub.name_en}`"
                                                @click="deleteSubcategory(category.id, sub.id)"
                                            >
                                                {{ t('app.serviceCategories.deleteAction') }}
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="category.subcategories.length === 0">
                                    <td colspan="3" class="text-muted-foreground py-3 text-center text-xs">—</td>
                                </tr>
                            </tbody>
                        </table>

                        <Button
                            variant="outline"
                            size="sm"
                            class="mt-3"
                            @click="openCreateSubcategorySheet(category.id)"
                        >
                            {{ t('app.serviceCategories.newSubcategory') }}
                        </Button>
                    </div>
                </CollapsibleContent>
            </Collapsible>
        </div>
    </div>

    <!-- Category Create / Edit Sheet -->
    <Sheet :open="categorySheetOpen" @update:open="(val) => { if (!val) closeCategorySheet(); }">
        <SheetContent side="right" class="flex flex-col gap-0 overflow-y-auto p-0" role="dialog" aria-modal="true">
            <SheetHeader class="border-b p-6">
                <SheetTitle>
                    {{ editingCategory
                        ? t('app.serviceCategories.sheetEditTitle')
                        : t('app.serviceCategories.sheetNewTitle') }}
                </SheetTitle>
            </SheetHeader>

            <form class="flex flex-1 flex-col gap-5 p-6" @submit.prevent="submitCategoryForm">
                <!-- Name EN -->
                <div class="grid gap-2">
                    <Label for="cat-name-en">{{ t('app.serviceCategories.labelNameEn') }} *</Label>
                    <Input
                        id="cat-name-en"
                        v-model="categoryForm.name_en"
                        dir="ltr"
                        lang="en"
                        required
                        autofocus
                        :placeholder="t('app.serviceCategories.labelNameEn')"
                    />
                    <InputError :message="categoryForm.errors.name_en" />
                </div>

                <!-- Name AR -->
                <div class="grid gap-2">
                    <Label for="cat-name-ar">{{ t('app.serviceCategories.labelNameAr') }} *</Label>
                    <Input
                        id="cat-name-ar"
                        v-model="categoryForm.name_ar"
                        dir="rtl"
                        lang="ar"
                        class="leading-relaxed"
                        required
                        :placeholder="t('app.serviceCategories.labelNameAr')"
                    />
                    <InputError :message="categoryForm.errors.name_ar" />
                </div>

                <!-- Icon picker -->
                <div class="grid gap-2">
                    <Label>{{ t('app.serviceCategories.labelIcon') }} *</Label>
                    <div
                        role="listbox"
                        :aria-label="t('app.serviceCategories.labelIcon')"
                        class="grid grid-cols-6 gap-2"
                    >
                        <button
                            v-for="icon in ICONS"
                            :key="icon"
                            type="button"
                            role="option"
                            :aria-selected="categoryForm.icon === icon"
                            class="flex size-10 items-center justify-center rounded-lg border text-xl transition-colors"
                            :class="categoryForm.icon === icon ? 'bg-primary/10 border-primary ring-2 ring-primary' : 'hover:bg-muted'"
                            @click="categoryForm.icon = icon"
                        >
                            {{ icon }}
                        </button>
                    </div>
                    <InputError :message="categoryForm.errors.icon" />
                </div>

                <!-- SLA Configuration -->
                <div class="grid gap-4 rounded-lg border p-4">
                    <p class="text-sm font-medium">{{ t('app.serviceCategories.slaConfigHeading') }}</p>

                    <div class="grid gap-2">
                        <Label for="cat-response-sla">{{ t('app.serviceCategories.labelResponseSla') }} *</Label>
                        <Input
                            id="cat-response-sla"
                            v-model.number="categoryForm.response_sla_hours"
                            type="number"
                            min="1"
                            max="720"
                            required
                            :placeholder="t('app.serviceCategories.placeholderResponseHours')"
                        />
                        <InputError :message="categoryForm.errors.response_sla_hours" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="cat-resolution-sla">{{ t('app.serviceCategories.labelResolutionSla') }} *</Label>
                        <Input
                            id="cat-resolution-sla"
                            v-model.number="categoryForm.resolution_sla_hours"
                            type="number"
                            min="1"
                            max="720"
                            required
                            :placeholder="t('app.serviceCategories.placeholderResolutionHours')"
                        />
                        <InputError :message="categoryForm.errors.resolution_sla_hours" />
                    </div>
                </div>

                <!-- Default Assignee -->
                <div class="grid gap-2">
                    <Label for="cat-assignee">{{ t('app.serviceCategories.labelDefaultAssignee') }}</Label>
                    <select
                        id="cat-assignee"
                        v-model="categoryForm.default_assignee_id"
                        class="border-input bg-background ring-offset-background focus-visible:ring-ring flex h-9 w-full rounded-md border px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1"
                    >
                        <option :value="null">{{ t('app.serviceCategories.placeholderAssignee') }}</option>
                        <option v-for="assignee in assignees" :key="assignee.id" :value="assignee.id">
                            {{ assignee.name }}
                        </option>
                    </select>
                    <InputError :message="categoryForm.errors.default_assignee_id" />
                </div>

                <!-- Community Visibility -->
                <div class="grid gap-2">
                    <Label>{{ t('app.serviceCategories.labelCommunities') }}</Label>
                    <div
                        role="group"
                        :aria-label="t('app.serviceCategories.labelCommunities')"
                        class="grid grid-cols-2 gap-2"
                    >
                        <label
                            v-for="community in communities"
                            :key="community.id"
                            class="flex cursor-pointer items-center gap-2 text-sm"
                        >
                            <Checkbox
                                :checked="categoryForm.community_ids.includes(community.id)"
                                @update:checked="() => toggleCommunity(community.id)"
                            />
                            {{ community.name }}
                        </label>
                    </div>
                    <InputError :message="categoryForm.errors.community_ids" />
                </div>

                <!-- Require completion photo -->
                <label class="flex cursor-pointer items-center gap-2 text-sm">
                    <Checkbox
                        :checked="categoryForm.require_completion_photo"
                        role="switch"
                        :aria-checked="categoryForm.require_completion_photo"
                        @update:checked="(val) => (categoryForm.require_completion_photo = val as boolean)"
                    />
                    {{ t('app.serviceCategories.labelRequirePhoto') }}
                </label>

                <!-- Status -->
                <div class="grid gap-2">
                    <Label>{{ t('app.serviceCategories.labelStatus') }}</Label>
                    <div class="flex gap-4">
                        <label class="flex cursor-pointer items-center gap-2 text-sm">
                            <input
                                v-model="categoryForm.status"
                                type="radio"
                                value="active"
                                class="accent-primary"
                            />
                            {{ t('app.serviceCategories.statusActive') }}
                        </label>
                        <label class="flex cursor-pointer items-center gap-2 text-sm">
                            <input
                                v-model="categoryForm.status"
                                type="radio"
                                value="inactive"
                                class="accent-primary"
                            />
                            {{ t('app.serviceCategories.statusInactive') }}
                        </label>
                    </div>
                    <InputError :message="categoryForm.errors.status" />
                </div>

                <SheetFooter class="mt-auto flex justify-end gap-2 border-t pt-4">
                    <Button
                        type="button"
                        variant="outline"
                        :disabled="categoryForm.processing"
                        @click="closeCategorySheet"
                    >
                        {{ t('app.serviceCategories.cancelButton') }}
                    </Button>
                    <Button type="submit" :disabled="categoryForm.processing">
                        {{ categoryForm.processing
                            ? t('app.serviceCategories.saving')
                            : t('app.serviceCategories.saveButton') }}
                    </Button>
                </SheetFooter>
            </form>
        </SheetContent>
    </Sheet>

    <!-- Subcategory Create / Edit Sheet -->
    <Sheet :open="subcategorySheetOpen" @update:open="(val) => { if (!val) closeSubcategorySheet(); }">
        <SheetContent side="right" class="flex flex-col gap-0 overflow-y-auto p-0" role="dialog" aria-modal="true">
            <SheetHeader class="border-b p-6">
                <SheetTitle>
                    {{ editingSubcategory
                        ? t('app.serviceCategories.sheetSubEditTitle')
                        : t('app.serviceCategories.sheetSubNewTitle') }}
                </SheetTitle>
            </SheetHeader>

            <form class="flex flex-1 flex-col gap-5 p-6" @submit.prevent="submitSubcategoryForm">
                <!-- Name EN -->
                <div class="grid gap-2">
                    <Label for="sub-name-en">{{ t('app.serviceCategories.labelNameEn') }} *</Label>
                    <Input
                        id="sub-name-en"
                        v-model="subcategoryForm.name_en"
                        dir="ltr"
                        lang="en"
                        required
                        autofocus
                        :placeholder="t('app.serviceCategories.labelNameEn')"
                    />
                    <InputError :message="subcategoryForm.errors.name_en" />
                </div>

                <!-- Name AR -->
                <div class="grid gap-2">
                    <Label for="sub-name-ar">{{ t('app.serviceCategories.labelNameAr') }} *</Label>
                    <Input
                        id="sub-name-ar"
                        v-model="subcategoryForm.name_ar"
                        dir="rtl"
                        lang="ar"
                        class="leading-relaxed"
                        required
                        :placeholder="t('app.serviceCategories.labelNameAr')"
                    />
                    <InputError :message="subcategoryForm.errors.name_ar" />
                </div>

                <!-- SLA Configuration (optional — null = inherit) -->
                <div class="grid gap-4 rounded-lg border p-4">
                    <p class="text-sm font-medium">{{ t('app.serviceCategories.slaConfigHeading') }}</p>
                    <p class="text-muted-foreground text-xs">{{ t('app.serviceCategories.inheritedSla') }}</p>

                    <div class="grid gap-2">
                        <Label for="sub-response-sla">{{ t('app.serviceCategories.labelResponseSla') }}</Label>
                        <Input
                            id="sub-response-sla"
                            v-model.number="subcategoryForm.response_sla_hours"
                            type="number"
                            min="1"
                            max="720"
                            :placeholder="t('app.serviceCategories.placeholderResponseHours')"
                        />
                        <InputError :message="subcategoryForm.errors.response_sla_hours" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="sub-resolution-sla">{{ t('app.serviceCategories.labelResolutionSla') }}</Label>
                        <Input
                            id="sub-resolution-sla"
                            v-model.number="subcategoryForm.resolution_sla_hours"
                            type="number"
                            min="1"
                            max="720"
                            :placeholder="t('app.serviceCategories.placeholderResolutionHours')"
                        />
                        <InputError :message="subcategoryForm.errors.resolution_sla_hours" />
                    </div>
                </div>

                <!-- Status -->
                <div class="grid gap-2">
                    <Label>{{ t('app.serviceCategories.labelStatus') }}</Label>
                    <div class="flex gap-4">
                        <label class="flex cursor-pointer items-center gap-2 text-sm">
                            <input
                                v-model="subcategoryForm.status"
                                type="radio"
                                value="active"
                                class="accent-primary"
                            />
                            {{ t('app.serviceCategories.statusActive') }}
                        </label>
                        <label class="flex cursor-pointer items-center gap-2 text-sm">
                            <input
                                v-model="subcategoryForm.status"
                                type="radio"
                                value="inactive"
                                class="accent-primary"
                            />
                            {{ t('app.serviceCategories.statusInactive') }}
                        </label>
                    </div>
                    <InputError :message="subcategoryForm.errors.status" />
                </div>

                <SheetFooter class="mt-auto flex justify-end gap-2 border-t pt-4">
                    <Button
                        type="button"
                        variant="outline"
                        :disabled="subcategoryForm.processing"
                        @click="closeSubcategorySheet"
                    >
                        {{ t('app.serviceCategories.cancelButton') }}
                    </Button>
                    <Button type="submit" :disabled="subcategoryForm.processing">
                        {{ subcategoryForm.processing
                            ? t('app.serviceCategories.saving')
                            : t('app.serviceCategories.saveSubButton') }}
                    </Button>
                </SheetFooter>
            </form>
        </SheetContent>
    </Sheet>
</template>
