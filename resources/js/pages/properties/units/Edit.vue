<script setup lang="ts">
import { Head, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { useI18n } from '@/composables/useI18n';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import type { Community, Unit, UnitCategory } from '@/types';

const props = defineProps<{
    unit: Unit;
    communities: Pick<Community, 'id' | 'name'>[];
    categories: UnitCategory[];
}>();

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.properties.units.pageTitle'), href: '/units' },
            { title: t('app.properties.units.edit.breadcrumb'), href: '#' },
        ],
    });
});

const pageTitle = computed(() => t('app.properties.units.edit.pageTitleWithName', { name: props.unit.name }));

const form = useForm({
    name: props.unit.name,
    rf_community_id: String(props.unit.rf_community_id),
    rf_building_id: props.unit.rf_building_id ? String(props.unit.rf_building_id) : '',
    category_id: String(props.unit.category_id),
    type_id: String(props.unit.type_id),
    status_id: String(props.unit.status_id),
    net_area: props.unit.net_area ?? '',
    floor_no: props.unit.floor_no != null ? String(props.unit.floor_no) : '',
    year_build: props.unit.year_build ?? '',
    about: props.unit.about ?? '',
});

function submit() {
    form.put(`/units/${props.unit.id}`);
}
</script>

<template>
    <Head :title="pageTitle" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="pageTitle" :description="t('app.properties.units.edit.description')" />

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-2">
                <Label for="name">{{ t('app.properties.units.create.unitName') }}</Label>
                <Input id="name" v-model="form.name" required />
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="rf_community_id">{{ t('app.properties.units.create.community') }}</Label>
                <select id="rf_community_id" v-model="form.rf_community_id" required class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none">
                    <option value="" disabled>{{ t('app.properties.units.create.selectCommunity') }}</option>
                    <option v-for="c in communities" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
                <InputError :message="form.errors.rf_community_id" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="category_id">{{ t('app.properties.units.create.category') }}</Label>
                    <select id="category_id" v-model="form.category_id" required class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none">
                        <option value="" disabled>{{ t('app.properties.units.create.selectCategory') }}</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                    <InputError :message="form.errors.category_id" />
                </div>

                <div class="grid gap-2">
                    <Label for="type_id">{{ t('app.properties.units.create.type') }}</Label>
                    <select id="type_id" v-model="form.type_id" required class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none">
                        <option value="" disabled>{{ t('app.properties.units.create.selectType') }}</option>
                        <template v-for="cat in categories" :key="cat.id">
                            <option v-for="t in cat.types" :key="t.id" :value="t.id">{{ t.name }}</option>
                        </template>
                    </select>
                    <InputError :message="form.errors.type_id" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="grid gap-2">
                    <Label for="net_area">{{ t('app.properties.units.create.netArea') }}</Label>
                    <Input id="net_area" v-model="form.net_area" type="number" step="0.01" min="0" />
                    <InputError :message="form.errors.net_area" />
                </div>

                <div class="grid gap-2">
                    <Label for="floor_no">{{ t('app.properties.units.create.floorNumber') }}</Label>
                    <Input id="floor_no" v-model="form.floor_no" type="number" />
                    <InputError :message="form.errors.floor_no" />
                </div>

                <div class="grid gap-2">
                    <Label for="year_build">{{ t('app.properties.units.create.yearBuilt') }}</Label>
                    <Input id="year_build" v-model="form.year_build" type="number" min="1900" max="2099" />
                    <InputError :message="form.errors.year_build" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="about">{{ t('app.properties.units.create.descriptionLabel') }}</Label>
                <Textarea id="about" v-model="form.about" />
                <InputError :message="form.errors.about" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">{{ t('app.properties.units.edit.updateButton') }}</Button>
            </div>
        </form>
    </div>
</template>
