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
import type { Announcement, Community, Building } from '@/types';

const { t } = useI18n();

const props = defineProps<{
    announcement: Announcement;
    communities: Pick<Community, 'id' | 'name'>[];
    buildings: Pick<Building, 'id' | 'name' | 'rf_community_id'>[];
}>();

watch(() => t('app.actions.edit'), () => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.announcements'), href: '/announcements' },
            { title: t('app.actions.edit'), href: '#' },
        ],
    });
}, { immediate: true });

const form = useForm({
    title: props.announcement.title,
    body: props.announcement.body,
    community_id: props.announcement.community_id ? String(props.announcement.community_id) : '',
    building_id: props.announcement.building_id ? String(props.announcement.building_id) : '',
    is_published: !!props.announcement.published_at,
});

const filteredBuildings = computed(() =>
    form.community_id ? props.buildings.filter((b) => b.rf_community_id === Number(form.community_id)) : props.buildings,
);

watch(() => form.community_id, () => { form.building_id = ''; });

function submit() { form.put(`/announcements/${props.announcement.id}`); }
</script>

<template>
    <Head :title="t('app.announcements.editTitle')" />
    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="t('app.announcements.editTitle')" :description="t('app.announcements.editDescription')" />
        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-2">
                <Label for="title">{{ t('app.announcements.title') }}</Label>
                <Input id="title" v-model="form.title" required />
                <InputError :message="form.errors.title" />
            </div>
            <div class="grid gap-2">
                <Label for="body">{{ t('app.announcements.body') }}</Label>
                <Textarea id="body" v-model="form.body" rows="6" required />
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
                <label class="flex items-center gap-2">
                    <input type="checkbox" v-model="form.is_published" class="rounded border-gray-300" />
                    <span class="text-sm">{{ t('app.announcements.published') }}</span>
                </label>
            </div>
            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">{{ t('app.announcements.updateButton') }}</Button>
            </div>
        </form>
    </div>
</template>
