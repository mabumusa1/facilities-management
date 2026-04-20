<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

defineOptions({ layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Announcements', href: '/announcements' }, { title: 'New Announcement', href: '/announcements/create' }] } });

const form = useForm({ title: '', body: '', community_id: '', building_id: '' });

function submit() { form.post('/announcements'); }
</script>

<template>
    <Head title="New Announcement" />
    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" title="Create Announcement" description="Create a new announcement for your community." />
        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-2"><Label for="title">Title</Label><Input id="title" v-model="form.title" required placeholder="Announcement title" /><InputError :message="form.errors.title" /></div>
            <div class="grid gap-2"><Label for="body">Body</Label><Textarea id="body" v-model="form.body" required placeholder="Announcement content..." /><InputError :message="form.errors.body" /></div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2"><Label for="community_id">Community (optional)</Label><Input id="community_id" v-model="form.community_id" type="number" placeholder="Community ID" /><InputError :message="form.errors.community_id" /></div>
                <div class="grid gap-2"><Label for="building_id">Building (optional)</Label><Input id="building_id" v-model="form.building_id" type="number" placeholder="Building ID" /><InputError :message="form.errors.building_id" /></div>
            </div>
            <div class="flex items-center gap-4"><Button :disabled="form.processing">Create Announcement</Button></div>
        </form>
    </div>
</template>
