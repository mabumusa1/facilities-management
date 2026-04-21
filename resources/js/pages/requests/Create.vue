<script setup lang="ts">
import { computed, watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import type { Community, RequestCategory, Status, Unit } from '@/types';

const props = defineProps<{
    categories: (Pick<RequestCategory, 'id' | 'name' | 'name_en'> & { subcategories: { id: number; name: string; name_en: string | null; category_id: number }[] })[];
    communities: Pick<Community, 'id' | 'name'>[];
    units: Pick<Unit, 'id' | 'name'>[];
    statuses: Pick<Status, 'id' | 'name' | 'name_en'>[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Requests', href: '/requests' },
            { title: 'New Request', href: '/requests/create' },
        ],
    },
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
</script>

<template>
    <Head title="New Request" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" title="Create Request" description="Submit a new service request." />

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label>Category</Label>
                    <Select v-model="form.category_id">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select category" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="cat in categories" :key="cat.id" :value="String(cat.id)">
                                {{ cat.name_en ?? cat.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.category_id" />
                </div>

                <div class="grid gap-2">
                    <Label>Subcategory</Label>
                    <Select v-model="form.subcategory_id" :disabled="!form.category_id">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select subcategory" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="sub in filteredSubcategories" :key="sub.id" :value="String(sub.id)">
                                {{ sub.name_en ?? sub.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.subcategory_id" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label>Community</Label>
                    <Select v-model="form.community_id">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select community" />
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
                    <Label>Unit</Label>
                    <Select v-model="form.unit_id">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select unit" />
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
                <Label>Priority</Label>
                <Select v-model="form.priority">
                    <SelectTrigger class="w-full">
                        <SelectValue placeholder="Select priority" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="low">Low</SelectItem>
                        <SelectItem value="medium">Medium</SelectItem>
                        <SelectItem value="high">High</SelectItem>
                        <SelectItem value="urgent">Urgent</SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="form.errors.priority" />
            </div>

            <div class="grid gap-2">
                <Label for="description">Description</Label>
                <Textarea id="description" v-model="form.description" placeholder="Describe the request..." />
                <InputError :message="form.errors.description" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">Submit Request</Button>
            </div>
        </form>
    </div>
</template>
