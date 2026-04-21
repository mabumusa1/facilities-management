<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { FacilityBooking } from '@/types';

const props = defineProps<{ facilityBooking: FacilityBooking }>();
defineOptions({ layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Bookings', href: '/facility-bookings' }, { title: 'Details', href: '#' }] } });

function deleteBooking() { if (confirm('Are you sure?')) { router.delete(`/facility-bookings/${props.facilityBooking.id}`); } }
</script>

<template>
    <Head :title="`Booking #${facilityBooking.id}`" />
    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Booking #{{ facilityBooking.id }}</h2>
                <p class="text-muted-foreground text-sm">{{ facilityBooking.facility?.name }}</p>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child><Link :href="`/facility-bookings/${facilityBooking.id}/edit`">Edit</Link></Button>
                <Button variant="destructive" @click="deleteBooking">Delete</Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>Booking Details</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">Facility</span><span>{{ facilityBooking.facility?.name ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Booked By</span><span>{{ facilityBooking.booker ? `${facilityBooking.booker.first_name ?? ''} ${facilityBooking.booker.last_name ?? ''}`.trim() || facilityBooking.booker.name : '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Status</span><Badge>{{ facilityBooking.status?.name ?? '—' }}</Badge></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Guests</span><span>{{ (facilityBooking as any).number_of_guests ?? '—' }}</span></div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>Schedule</CardTitle></CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex justify-between"><span class="text-muted-foreground">Date</span><span>{{ facilityBooking.booking_date }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Start</span><span>{{ facilityBooking.start_time }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">End</span><span>{{ facilityBooking.end_time }}</span></div>
                </CardContent>
            </Card>
        </div>

        <Card v-if="(facilityBooking as any).notes">
            <CardHeader><CardTitle>Notes</CardTitle></CardHeader>
            <CardContent><p>{{ (facilityBooking as any).notes }}</p></CardContent>
        </Card>
    </div>
</template>
