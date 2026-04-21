<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

type FacilityItem = {
    id: number;
    name: string;
    category?: { name?: string | null; name_en?: string | null } | null;
    community?: { name?: string | null } | null;
    is_active: boolean;
    bookings_count: number;
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Settings', href: '/settings/invoice' },
            { title: 'Facilities', href: '/settings/facilities' },
        ],
    },
});

const props = defineProps<{
    facilities: {
        data: FacilityItem[];
    };
}>();
</script>

<template>
    <Head title="Settings - Facilities" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading
                variant="small"
                title="Settings Facilities"
                description="Manage facilities that appear in the settings module."
            />
            <Button as-child>
                <Link href="/settings/addNewFacility">Add Facility</Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Facilities</CardTitle>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Category</TableHead>
                            <TableHead>Community</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Bookings</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="facility in props.facilities.data" :key="facility.id">
                            <TableCell>{{ facility.name }}</TableCell>
                            <TableCell>{{ facility.category?.name_en ?? facility.category?.name ?? 'N/A' }}</TableCell>
                            <TableCell>{{ facility.community?.name ?? 'N/A' }}</TableCell>
                            <TableCell>
                                <Badge :variant="facility.is_active ? 'default' : 'secondary'">
                                    {{ facility.is_active ? 'Active' : 'Inactive' }}
                                </Badge>
                            </TableCell>
                            <TableCell>{{ facility.bookings_count }}</TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Button variant="outline" size="sm" as-child>
                                        <Link :href="`/settings/facility/${facility.id}`">View</Link>
                                    </Button>
                                    <Button variant="outline" size="sm" as-child>
                                        <Link :href="`/settings/addNewFacility/${facility.id}`">Edit</Link>
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.facilities.data.length === 0">
                            <TableCell :colspan="6" class="text-muted-foreground text-center">No facilities found.</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    </div>
</template>
