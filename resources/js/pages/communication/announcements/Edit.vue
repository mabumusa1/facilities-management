<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import type { Announcement } from '@/types';

const props = defineProps<{ announcement: Announcement }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Announcements', href: '/announcements' }, { title: 'Edit', href: '#' }] } });

const form = useForm({
    title: props.announcement.title,
    body: props.announcement.body,
    community_id: props.announcement.community_id ?? '',
    building_id: props.announcement.building_id ?? '',
    is_published: props.announcement.is_published,
});

function submit() { form.put(`/announcements/${props.announcement.id}`); }
</script>

<template>
    <Head title="Edit Announcement" />
    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" title="Edit Announcement" description="Update announcement details." />
        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-2"><Label for="title">Title</Label><Input id="title" v-model="form.title" required /><InputError :message="form.errors.title" /></div>
            <div class="grid gap-2"><Label for="body">Body</Label><Textarea id="body" v-model="form.body" rows="6" required /><InputError :message="form.errors.body" /></div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2"><Label for="community_id">Community ID</Label><Input id="community_id" v-model="form.community_id" type="number" placeholder="Optional" /><InputError :message="form.errors.community_id" /></div>
                <div class="grid gap-2"><Label for="building_id">Building ID</Label><Input id="building_id" v-model="form.building_id" type="number" placeholder="Optional" /><InputError :message="form.errors.building_id" /></div>
            </div>
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_published" class="rounded border-gray-300" /><span class="text-sm">Published</span></label>
            </div>
            <div class="flex items-center gap-4"><Button :disabled="form.processing">Update Announcement</Button></div>
        </form>
    </div>
</template>
