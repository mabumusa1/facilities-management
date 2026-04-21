<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

defineOptions({ layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Admins', href: '/admins' }, { title: 'New Admin', href: '/admins/create' }] } });

const form = useForm({ first_name: '', last_name: '', email: '', phone_number: '', phone_country_code: 'SA', role: 'Admins', national_id: '', gender: '' });

function submit() { form.post('/admins'); }
</script>

<template>
    <Head title="New Admin" />
    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" title="Create Admin" description="Add a new administrator." />
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
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="role">Role</Label>
                    <Select v-model="form.role">
                        <SelectTrigger id="role" class="w-full">
                            <SelectValue placeholder="Select role" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="Admins">Admin</SelectItem>
                            <SelectItem value="accountingManagers">Accounting Manager</SelectItem>
                            <SelectItem value="serviceManagers">Service Manager</SelectItem>
                            <SelectItem value="marketingManagers">Marketing Manager</SelectItem>
                            <SelectItem value="salesAndLeasingManagers">Sales & Leasing Manager</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.role" />
                </div>
                <div class="grid gap-2">
                    <Label for="gender">Gender</Label>
                    <Select v-model="form.gender">
                        <SelectTrigger id="gender" class="w-full">
                            <SelectValue placeholder="Select gender" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="male">Male</SelectItem>
                            <SelectItem value="female">Female</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.gender" />
                </div>
            </div>
            <div class="grid gap-2"><Label for="national_id">National ID</Label><Input id="national_id" v-model="form.national_id" /><InputError :message="form.errors.national_id" /></div>
            <div class="flex items-center gap-4"><Button :disabled="form.processing">Create Admin</Button></div>
        </form>
    </div>
</template>
