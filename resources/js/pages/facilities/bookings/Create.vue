<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import type { Facility, Resident, Status } from '@/types';

defineOptions({ layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Bookings', href: '/facility-bookings' }, { title: 'New Booking', href: '/facility-bookings/create' }] } });

defineProps<{
    facilities: Pick<Facility, 'id' | 'name'>[];
    residents: Pick<Resident, 'id' | 'first_name' | 'last_name'>[];
    statuses: Pick<Status, 'id' | 'name'>[];
}>();

const form = useForm({
    facility_id: '',
    booker_id: '',
    booker_type: 'App\\Models\\Resident',
    status_id: '',
    booking_date: '',
    start_time: '',
    end_time: '',
    number_of_guests: '',
    notes: '',
});

function submit() { form.post('/facility-bookings'); }
</script>

<template>
    <Head title="New Booking" />
    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" title="Create Booking" description="Reserve a facility for a resident." />
        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label>Facility</Label>
                    <Select v-model="form.facility_id">
                        <SelectTrigger class="w-full"><SelectValue placeholder="Select facility" /></SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="f in facilities" :key="f.id" :value="String(f.id)">{{ f.name }}</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.facility_id" />
                </div>
                <div class="grid gap-2">
                    <Label>Resident</Label>
                    <Select v-model="form.booker_id">
                        <SelectTrigger class="w-full"><SelectValue placeholder="Select resident" /></SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="r in residents" :key="r.id" :value="String(r.id)">{{ r.first_name }} {{ r.last_name }}</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.booker_id" />
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label>Status</Label>
                    <Select v-model="form.status_id">
                        <SelectTrigger class="w-full"><SelectValue placeholder="Select status" /></SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="s in statuses" :key="s.id" :value="String(s.id)">{{ s.name }}</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.status_id" />
                </div>
                <div class="grid gap-2">
                    <Label for="booking_date">Booking Date</Label>
                    <Input id="booking_date" v-model="form.booking_date" type="date" required />
                    <InputError :message="form.errors.booking_date" />
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="grid gap-2">
                    <Label for="start_time">Start Time</Label>
                    <Input id="start_time" v-model="form.start_time" type="time" required />
                    <InputError :message="form.errors.start_time" />
                </div>
                <div class="grid gap-2">
                    <Label for="end_time">End Time</Label>
                    <Input id="end_time" v-model="form.end_time" type="time" required />
                    <InputError :message="form.errors.end_time" />
                </div>
                <div class="grid gap-2">
                    <Label for="guests">Guests</Label>
                    <Input id="guests" v-model="form.number_of_guests" type="number" min="1" placeholder="Number of guests" />
                    <InputError :message="form.errors.number_of_guests" />
                </div>
            </div>
            <div class="grid gap-2">
                <Label for="notes">Notes</Label>
                <Textarea id="notes" v-model="form.notes" placeholder="Optional notes..." />
                <InputError :message="form.errors.notes" />
            </div>
            <div class="flex items-center gap-4"><Button :disabled="form.processing">Create Booking</Button></div>
        </form>
    </div>
</template>
