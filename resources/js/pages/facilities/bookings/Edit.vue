<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import type { Facility, FacilityBooking, Resident, Status } from '@/types';

const props = defineProps<{
    facilityBooking: FacilityBooking;
    facilities: Pick<Facility, 'id' | 'name'>[];
    residents: Pick<Resident, 'id' | 'first_name' | 'last_name'>[];
    statuses: Pick<Status, 'id' | 'name'>[];
}>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Bookings', href: '/facility-bookings' }, { title: 'Edit', href: '#' }] } });

const form = useForm({
    status_id: String(props.facilityBooking.status_id ?? ''),
    booking_date: props.facilityBooking.booking_date ?? '',
    start_time: props.facilityBooking.start_time ?? '',
    end_time: props.facilityBooking.end_time ?? '',
    number_of_guests: (props.facilityBooking as any).number_of_guests ?? '',
    notes: props.facilityBooking.notes ?? '',
});

function submit() { form.put(`/facility-bookings/${props.facilityBooking.id}`); }
</script>

<template>
    <Head title="Edit Booking" />
    <div class="flex flex-col gap-6 p-4">
        <Heading variant="small" title="Edit Booking" :description="`Update booking #${facilityBooking.id}`" />
        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label>Facility</Label>
                    <Input :model-value="facilityBooking.facility?.name ?? '—'" disabled />
                </div>
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
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="grid gap-2">
                    <Label for="booking_date">Booking Date</Label>
                    <Input id="booking_date" v-model="form.booking_date" type="date" />
                    <InputError :message="form.errors.booking_date" />
                </div>
                <div class="grid gap-2">
                    <Label for="start_time">Start Time</Label>
                    <Input id="start_time" v-model="form.start_time" type="time" />
                    <InputError :message="form.errors.start_time" />
                </div>
                <div class="grid gap-2">
                    <Label for="end_time">End Time</Label>
                    <Input id="end_time" v-model="form.end_time" type="time" />
                    <InputError :message="form.errors.end_time" />
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="guests">Guests</Label>
                    <Input id="guests" v-model="form.number_of_guests" type="number" min="1" />
                    <InputError :message="form.errors.number_of_guests" />
                </div>
            </div>
            <div class="grid gap-2">
                <Label for="notes">Notes</Label>
                <Textarea id="notes" v-model="form.notes" placeholder="Optional notes..." />
                <InputError :message="form.errors.notes" />
            </div>
            <div class="flex items-center gap-4"><Button :disabled="form.processing">Update Booking</Button></div>
        </form>
    </div>
</template>
