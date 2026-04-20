<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Resident } from '@/types';

const props = defineProps<{ resident: Resident }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Tenants', href: '/residents' }, { title: 'Edit', href: '#' }] } });

const form = useForm({ first_name: props.resident.first_name, last_name: props.resident.last_name, email: props.resident.email ?? '', phone_number: props.resident.phone_number, phone_country_code: props.resident.phone_country_code, national_id: props.resident.national_id ?? '', gender: props.resident.gender ?? '', georgian_birthdate: props.resident.georgian_birthdate ?? '' });

function submit() { form.put(`/residents/${props.resident.id}`); }
</script>

<template>
    <Head :title="`Edit ${resident.first_name} ${resident.last_name}`" />
    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" :title="`Edit ${resident.first_name} ${resident.last_name}`" description="Update tenant details." />
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
                <div class="grid gap-2"><Label for="national_id">National ID</Label><Input id="national_id" v-model="form.national_id" /><InputError :message="form.errors.national_id" /></div>
                <div class="grid gap-2">
                    <Label for="gender">Gender</Label>
                    <select id="gender" v-model="form.gender" class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"><option value="">Select</option><option value="male">Male</option><option value="female">Female</option></select>
                    <InputError :message="form.errors.gender" />
                </div>
            </div>
            <div class="grid gap-2"><Label for="georgian_birthdate">Date of Birth</Label><Input id="georgian_birthdate" v-model="form.georgian_birthdate" type="date" /><InputError :message="form.errors.georgian_birthdate" /></div>
            <div class="flex items-center gap-4"><Button :disabled="form.processing">Update Tenant</Button></div>
        </form>
    </div>
</template>
