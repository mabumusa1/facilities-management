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
import type { Community, FacilityCategory } from '@/types';

const { isArabic, t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.facilities'), href: '/facilities' },
            { title: t('app.facilities.newFacility'), href: '/facilities/create' },
        ],
    });
});

defineProps<{
    categories: FacilityCategory[];
    communities: Pick<Community, 'id' | 'name'>[];
}>();

const form = useForm({ name: '', category_id: '', community_id: '', capacity: '' });

function submit() { form.post('/facilities'); }

function localizedCategoryName(category: FacilityCategory): string {
    if (isArabic.value) {
        return category.name_ar ?? category.name ?? category.name_en ?? '';
    }

    return category.name_en ?? category.name ?? category.name_ar ?? '';
}
</script>

<template>
    <Head :title="t('app.facilities.newFacility')" />
    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="t('app.facilities.createTitle')" :description="t('app.facilities.createDescription')" />
        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-2"><Label for="name">{{ t('app.facilities.facilityName') }}</Label><Input id="name" v-model="form.name" required :placeholder="t('app.facilities.namePlaceholder')" /><InputError :message="form.errors.name" /></div>
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
            <div class="grid gap-2"><Label for="capacity">{{ t('app.facilities.capacity') }}</Label><Input id="capacity" v-model="form.capacity" type="number" min="1" :placeholder="t('app.facilities.capacityPlaceholder')" /><InputError :message="form.errors.capacity" /></div>
            <div class="flex items-center gap-4"><Button :disabled="form.processing">{{ t('app.facilities.createButton') }}</Button></div>
        </form>
    </div>
</template>
