<script setup lang="ts">
import { computed, watch } from 'vue';
import { Head, setLayoutProps, useForm } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import type { Community, Building } from '@/types';

const { t } = useI18n();

const props = defineProps<{
    communities: Pick<Community, 'id' | 'name'>[];
    buildings: Pick<Building, 'id' | 'name' | 'rf_community_id'>[];
}>();

watch(() => t('app.announcements.newAnnouncement'), () => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.announcements'), href: '/announcements' },
            { title: t('app.announcements.newAnnouncement'), href: '/announcements/create' },
        ],
    });
}, { immediate: true });

const form = useForm({ title: '', body: '', community_id: '', building_id: '' });

const filteredBuildings = computed(() =>
    form.community_id ? props.buildings.filter((b) => b.rf_community_id === Number(form.community_id)) : props.buildings,
);

watch(() => form.community_id, () => { form.building_id = ''; });

function submit() { form.post('/announcements'); }
</script>

<template>
    <Head :title="t('app.announcements.newAnnouncement')" />
    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="t('app.announcements.createTitle')" :description="t('app.announcements.createDescription')" />
        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-2">
                <Label for="title">{{ t('app.announcements.title') }}</Label>
                <Input id="title" v-model="form.title" required :placeholder="t('app.announcements.titlePlaceholder')" />
                <InputError :message="form.errors.title" />
            </div>
            <div class="grid gap-2">
                <Label for="body">{{ t('app.announcements.body') }}</Label>
                <Textarea id="body" v-model="form.body" required :placeholder="t('app.announcements.bodyPlaceholder')" />
                <InputError :message="form.errors.body" />
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label>{{ t('app.announcements.communityOptional') }}</Label>
                    <Select v-model="form.community_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.announcements.selectCommunity')" />
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
                    <Label>{{ t('app.announcements.buildingOptional') }}</Label>
                    <Select v-model="form.building_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="t('app.announcements.selectBuilding')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="building in filteredBuildings" :key="building.id" :value="String(building.id)">
                                {{ building.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.building_id" />
                </div>
            </div>
            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">{{ t('app.announcements.createButton') }}</Button>
            </div>
        </form>
    </div>
</template>
