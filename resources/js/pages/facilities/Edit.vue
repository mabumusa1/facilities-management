<script setup lang="ts">
import { Head, setLayoutProps, useForm } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { Community, Facility, FacilityCategory } from '@/types';

const { isArabic, t } = useI18n();

const props = defineProps<{
    facility: Facility;
    categories: FacilityCategory[];
    communities: Pick<Community, 'id' | 'name'>[];
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.facilities'), href: '/facilities' },
            { title: t('app.actions.edit'), href: '#' },
        ],
    });
});

const form = useForm({
    name: props.facility.name,
    category_id: String(props.facility.category_id ?? ''),
    community_id: String(props.facility.community_id ?? ''),
    capacity: props.facility.capacity ?? '',
    is_active: props.facility.is_active ?? true,
});

function submit() { form.put(`/facilities/${props.facility.id}`); }

function localizedCategoryName(category: FacilityCategory): string {
    if (isArabic.value) {
        return category.name_ar ?? category.name ?? category.name_en ?? '';
    }

    return category.name_en ?? category.name ?? category.name_ar ?? '';
}
</script>

<template>
    <Head :title="t('app.facilities.editTitle')" />
    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="t('app.facilities.editTitle')" :description="t('app.facilities.editDescription')" />
        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-2"><Label for="name">{{ t('app.facilities.name') }}</Label><Input id="name" v-model="form.name" required /><InputError :message="form.errors.name" /></div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label>{{ t('app.facilities.category') }}</Label>
                    <Select v-model="form.category_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.facilities.selectCategory')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="cat in categories" :key="cat.id" :value="String(cat.id)">
                                {{ localizedCategoryName(cat) }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.category_id" />
                </div>
                <div class="grid gap-2">
                    <Label>{{ t('app.facilities.community') }}</Label>
                    <Select v-model="form.community_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.facilities.selectCommunity')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="community in communities" :key="community.id" :value="String(community.id)">
                                {{ community.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.community_id" />
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2"><Label for="capacity">{{ t('app.facilities.capacity') }}</Label><Input id="capacity" v-model="form.capacity" type="number" /><InputError :message="form.errors.capacity" /></div>
                <div class="flex items-end gap-2"><label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_active" class="rounded border-gray-300" /><span class="text-sm">{{ t('app.common.active') }}</span></label></div>
            </div>
            <div class="flex items-center gap-4"><Button :disabled="form.processing">{{ t('app.facilities.updateButton') }}</Button></div>
        </form>
    </div>
</template>
