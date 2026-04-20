<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Admin } from '@/types';

const props = defineProps<{ admin: Admin }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Admins', href: '/admins' }, { title: 'Edit', href: '#' }] } });

const form = useForm({ first_name: props.admin.first_name, last_name: props.admin.last_name, email: props.admin.email ?? '', phone_number: props.admin.phone_number, phone_country_code: props.admin.phone_country_code, role: props.admin.role, national_id: props.admin.national_id ?? '', gender: props.admin.gender ?? '' });

function submit() { form.put(`/admins/${props.admin.id}`); }
</script>

<template>
    <Head :title="`Edit ${admin.first_name} ${admin.last_name}`" />
    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="`Edit ${admin.first_name} ${admin.last_name}`" description="Update admin details." />
        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2"><Label for="first_name">First Name</Label><Input id="first_name" v-model="form.first_name" required /><InputError :message="form.errors.first_name" /></div>
                <div class="grid gap-2"><Label for="last_name">Last Name</Label><Input id="last_name" v-model="form.last_name" required /><InputError :message="form.errors.last_name" /></div>
            </div>
            <div class="grid gap-2"><Label for="email">Email</Label><Input id="email" v-model="form.email" type="email" /><InputError :message="form.errors.email" /></div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2"><Label for="phone_country_code">Country Code</Label><Input id="phone_country_code" v-model="form.phone_country_code" required maxlength="5" /><InputError :message="form.errors.phone_country_code" /></div>
                <div class="grid gap-2"><Label for="phone_number">Phone Number</Label><Input id="phone_number" v-model="form.phone_number" required /><InputError :message="form.errors.phone_number" /></div>
            </div>
            <div class="grid gap-2">
                <Label for="role">Role</Label>
                <select id="role" v-model="form.role" required class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none">
                    <option value="Admins">Admin</option>
                    <option value="accountingManagers">Accounting Manager</option>
                    <option value="serviceManagers">Service Manager</option>
                    <option value="marketingManagers">Marketing Manager</option>
                    <option value="salesAndLeasingManagers">Sales & Leasing Manager</option>
                </select>
                <InputError :message="form.errors.role" />
            </div>
            <div class="flex items-center gap-4"><Button :disabled="form.processing">Update Admin</Button></div>
        </form>
    </div>
</template>
