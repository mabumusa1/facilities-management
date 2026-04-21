<script setup lang="ts">
import { computed, watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import type { Community, RequestCategory, ServiceRequest, Status, Unit } from '@/types';

const props = defineProps<{
    serviceRequest: ServiceRequest;
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
            { title: 'Edit', href: '#' },
        ],
    },
});

const form = useForm({
    category_id: props.serviceRequest.category_id ? String(props.serviceRequest.category_id) : '',
    subcategory_id: props.serviceRequest.subcategory_id ? String(props.serviceRequest.subcategory_id) : '',
    unit_id: props.serviceRequest.unit_id ? String(props.serviceRequest.unit_id) : '',
    community_id: props.serviceRequest.community_id ? String(props.serviceRequest.community_id) : '',
    description: props.serviceRequest.description ?? '',
    priority: props.serviceRequest.priority ?? 'medium',
    status_id: props.serviceRequest.status_id ? String(props.serviceRequest.status_id) : '',
});

const filteredSubcategories = computed(() => {
    if (!form.category_id) return [];
    const category = props.categories.find((c) => c.id === Number(form.category_id));
    return category?.subcategories ?? [];
});

watch(() => form.category_id, (newVal, oldVal) => {
    if (oldVal !== undefined && newVal !== oldVal) {
        form.subcategory_id = '';
    }
});

function submit() {
    form.put(`/requests/${props.serviceRequest.id}`);
}
</script>

<template>
    <Head title="Edit Request" />

    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" title="Edit Request" description="Update the service request details." />

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

            <div class="grid gap-4 sm:grid-cols-2">
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
                    <Label>Status</Label>
                    <Select v-model="form.status_id">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="status in statuses" :key="status.id" :value="String(status.id)">
                                {{ status.name_en ?? status.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.status_id" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="description">Description</Label>
                <Textarea id="description" v-model="form.description" placeholder="Describe the request..." />
                <InputError :message="form.errors.description" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">Update Request</Button>
            </div>
        </form>
    </div>
</template>
