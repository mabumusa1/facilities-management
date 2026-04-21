<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

type FacilityBooking = {
    id: number;
    booking_date: string;
    start_time: string;
    end_time: string;
    number_of_guests: number | null;
    notes: string | null;
};

type Facility = {
    id: number;
    name: string;
    name_en: string | null;
    name_ar: string | null;
    description: string | null;
    capacity: number | null;
    open_time: string | null;
    close_time: string | null;
    booking_fee: string;
    is_active: boolean;
    requires_approval: boolean;
    category?: { name?: string | null; name_en?: string | null } | null;
    community?: { name?: string | null } | null;
    bookings?: FacilityBooking[];
};

const props = defineProps<{ facility: Facility }>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Settings', href: '/settings/invoice' },
            { title: 'Facilities', href: '/settings/facilities' },
        ],
    },
});
</script>

<template>
    <Head :title="`Facility - ${props.facility.name}`" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading
                variant="small"
                :title="props.facility.name_en ?? props.facility.name"
                description="Facility details from the settings module."
            />
            <div class="flex gap-2">
                <Button variant="outline" as-child>
                    <Link href="/settings/facilities">Back</Link>
                </Button>
                <Button as-child>
                    <Link :href="`/settings/addNewFacility/${props.facility.id}`">Edit</Link>
                </Button>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Details</CardTitle>
            </CardHeader>
            <CardContent class="grid gap-3 text-sm md:grid-cols-2">
                <p><span class="font-medium">Category:</span> {{ props.facility.category?.name_en ?? props.facility.category?.name ?? 'N/A' }}</p>
                <p><span class="font-medium">Community:</span> {{ props.facility.community?.name ?? 'N/A' }}</p>
                <p><span class="font-medium">Capacity:</span> {{ props.facility.capacity ?? 'N/A' }}</p>
                <p><span class="font-medium">Open:</span> {{ props.facility.open_time ?? 'N/A' }}</p>
                <p><span class="font-medium">Close:</span> {{ props.facility.close_time ?? 'N/A' }}</p>
                <p><span class="font-medium">Booking Fee:</span> {{ props.facility.booking_fee }}</p>
                <p>
                    <span class="font-medium">Status:</span>
                    <Badge :variant="props.facility.is_active ? 'default' : 'secondary'">
                        {{ props.facility.is_active ? 'Active' : 'Inactive' }}
                    </Badge>
                </p>
                <p>
                    <span class="font-medium">Approval:</span>
                    <Badge :variant="props.facility.requires_approval ? 'default' : 'secondary'">
                        {{ props.facility.requires_approval ? 'Required' : 'Not Required' }}
                    </Badge>
                </p>
                <p class="md:col-span-2"><span class="font-medium">Description:</span> {{ props.facility.description ?? 'N/A' }}</p>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Recent Bookings</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3">
                <div v-for="booking in props.facility.bookings ?? []" :key="booking.id" class="rounded-md border p-3 text-sm">
                    <p class="font-medium">Booking #{{ booking.id }}</p>
                    <p>{{ booking.booking_date }} - {{ booking.start_time }} to {{ booking.end_time }}</p>
                    <p>Guests: {{ booking.number_of_guests ?? 'N/A' }}</p>
                    <p>Notes: {{ booking.notes ?? 'N/A' }}</p>
                </div>
                <p v-if="(props.facility.bookings ?? []).length === 0" class="text-muted-foreground text-sm">No bookings yet.</p>
            </CardContent>
        </Card>
    </div>
</template>
