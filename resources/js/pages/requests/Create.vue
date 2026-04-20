<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

defineOptions({ layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Requests', href: '/requests' }, { title: 'New Request', href: '/requests/create' }] } });

const form = useForm({ category_id: '', subcategory_id: '', unit_id: '', community_id: '', description: '', priority: 'medium' });

function submit() { form.post('/requests'); }
</script>

<template>
    <Head title="New Request" />
    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" title="Create Request" description="Submit a new service request." />
        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2"><Label for="category_id">Category</Label><Input id="category_id" v-model="form.category_id" type="number" required placeholder="Category ID" /><InputError :message="form.errors.category_id" /></div>
                <div class="grid gap-2"><Label for="subcategory_id">Subcategory</Label><Input id="subcategory_id" v-model="form.subcategory_id" type="number" placeholder="Subcategory ID" /><InputError :message="form.errors.subcategory_id" /></div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2"><Label for="community_id">Community</Label><Input id="community_id" v-model="form.community_id" type="number" placeholder="Community ID" /><InputError :message="form.errors.community_id" /></div>
                <div class="grid gap-2"><Label for="unit_id">Unit</Label><Input id="unit_id" v-model="form.unit_id" type="number" placeholder="Unit ID" /><InputError :message="form.errors.unit_id" /></div>
            </div>
            <div class="grid gap-2">
                <Label for="priority">Priority</Label>
                <select id="priority" v-model="form.priority" class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none">
                    <option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option><option value="urgent">Urgent</option>
                </select>
                <InputError :message="form.errors.priority" />
            </div>
            <div class="grid gap-2"><Label for="description">Description</Label><Textarea id="description" v-model="form.description" placeholder="Describe the request..." /><InputError :message="form.errors.description" /></div>
            <div class="flex items-center gap-4"><Button :disabled="form.processing">Submit Request</Button></div>
        </form>
    </div>
</template>
