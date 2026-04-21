<script setup lang="ts">
import { computed, watch, watchEffect } from 'vue';
import { Head, setLayoutProps, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { useI18n } from '@/composables/useI18n';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import type { Community, RequestCategory, Status, Unit } from '@/types';

const props = defineProps<{
    categories: (Pick<RequestCategory, 'id' | 'name' | 'name_ar' | 'name_en'> & { subcategories: { id: number; name: string; name_ar: string | null; name_en: string | null; category_id: number }[] })[];
    communities: Pick<Community, 'id' | 'name'>[];
    units: Pick<Unit, 'id' | 'name'>[];
    statuses: Pick<Status, 'id' | 'name' | 'name_en'>[];
}>();

const { isArabic, t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.requests.pageTitle'), href: '/requests' },
            { title: t('app.requests.create.pageTitle'), href: '/requests/create' },
        ],
    });
});

const form = useForm({
    category_id: '',
    subcategory_id: '',
    unit_id: '',
    community_id: '',
    description: '',
    priority: 'medium',
});

const filteredSubcategories = computed(() => {
    if (!form.category_id) return [];
    const category = props.categories.find((c) => c.id === Number(form.category_id));
    return category?.subcategories ?? [];
});

watch(() => form.category_id, () => {
    form.subcategory_id = '';
});

function submit() {
    form.post('/requests');
}

function localizedOptionName(option: { name: string; name_ar?: string | null; name_en?: string | null }): string {
    if (isArabic.value) {
        return option.name_ar ?? option.name ?? option.name_en ?? '';
    }

    return option.name_en ?? option.name ?? option.name_ar ?? '';
}
</script>

<template>
    <Head :title="t('app.requests.create.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            variant="small"
            :title="t('app.requests.create.heading')"
            :description="t('app.requests.create.description')"
        />

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label>{{ t('app.requests.category') }}</Label>
                    <Select v-model="form.category_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.requests.create.selectCategory')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="cat in categories" :key="cat.id" :value="String(cat.id)">
                                {{ localizedOptionName(cat) }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.category_id" />
                </div>

                <div class="grid gap-2">
                    <Label>{{ t('app.requests.table.subcategory') }}</Label>
                    <Select v-model="form.subcategory_id" :disabled="!form.category_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.requests.create.selectSubcategory')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="sub in filteredSubcategories" :key="sub.id" :value="String(sub.id)">
                                {{ localizedOptionName(sub) }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.subcategory_id" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label>{{ t('app.requests.table.community') }}</Label>
                    <Select v-model="form.community_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.requests.create.selectCommunity')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="community in communities" :key="community.id" :value="String(community.id)">
                                {{ community.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.community_id" />
                </div>

                <div class="grid gap-2">
                    <Label>{{ t('app.requests.show.unit') }}</Label>
                    <Select v-model="form.unit_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.requests.create.selectUnit')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="unit in units" :key="unit.id" :value="String(unit.id)">
                                {{ unit.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.unit_id" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label>{{ t('app.requests.priority') }}</Label>
                <Select v-model="form.priority">
                    <SelectTrigger class="w-full">
                        <SelectValue :placeholder="t('app.requests.create.selectPriority')" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="low">{{ t('app.requests.create.low') }}</SelectItem>
                        <SelectItem value="medium">{{ t('app.requests.create.medium') }}</SelectItem>
                        <SelectItem value="high">{{ t('app.requests.create.high') }}</SelectItem>
                        <SelectItem value="urgent">{{ t('app.requests.create.urgent') }}</SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="form.errors.priority" />
            </div>

            <div class="grid gap-2">
                <Label for="description">{{ t('app.requests.show.description') }}</Label>
                <Textarea id="description" v-model="form.description" :placeholder="t('app.requests.create.descriptionPlaceholder')" />
                <InputError :message="form.errors.description" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">{{ t('app.requests.create.submitButton') }}</Button>
            </div>
        </form>
    </div>
</template>
