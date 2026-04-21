<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Marketplace', href: '/marketplace' },
            { title: 'Listing', href: '/marketplace/listing' },
        ],
    },
});

const props = defineProps<{
    listings: {
        data: Array<{
            id: number;
            unit_id: number;
            listing_type: string;
            price: string;
            is_active: boolean;
            unit?: { id: number; name: string } | null;
        }>;
    };
    units: Array<{ id: number; name: string }>;
}>();

const listingForm = useForm({
    unit_id: 0,
    listing_type: 'rent',
    price: '',
    is_active: true,
});

function submitListing() {
    listingForm.post('/marketplace/listing', {
        preserveScroll: true,
        onSuccess: () => listingForm.reset('price'),
    });
}

function toggleListing(listing: { id: number; unit_id: number; listing_type: string; price: string; is_active: boolean }) {
    router.put(`/marketplace/listing/${listing.id}`, {
        unit_id: listing.unit_id,
        listing_type: listing.listing_type,
        price: listing.price,
        is_active: !listing.is_active,
    });
}

function removeListing(id: number) {
    router.delete(`/marketplace/listing/${id}`);
}
</script>

<template>
    <Head title="Marketplace Listing" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading variant="small" title="Marketplace Listing" description="Create and manage unit listings." />
            <Button variant="outline" as-child>
                <Link href="/marketplace">Back to Overview</Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Add Listing</CardTitle>
            </CardHeader>
            <CardContent>
                <form class="grid gap-4 md:grid-cols-4" @submit.prevent="submitListing">
                    <div class="grid gap-2 md:col-span-2">
                        <Label for="unit_id">Unit</Label>
                        <select id="unit_id" v-model.number="listingForm.unit_id" class="rounded-md border border-input bg-background px-3 py-2">
                            <option :value="0">Select unit</option>
                            <option v-for="unit in props.units" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
                        </select>
                        <InputError :message="listingForm.errors.unit_id" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="listing_type">Type</Label>
                        <select id="listing_type" v-model="listingForm.listing_type" class="rounded-md border border-input bg-background px-3 py-2">
                            <option value="rent">Rent</option>
                            <option value="sale">Sale</option>
                            <option value="both">Both</option>
                        </select>
                        <InputError :message="listingForm.errors.listing_type" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="price">Price</Label>
                        <Input id="price" v-model="listingForm.price" type="number" min="0" step="0.01" />
                        <InputError :message="listingForm.errors.price" />
                    </div>
                    <div class="md:col-span-4">
                        <Button :disabled="listingForm.processing">Create Listing</Button>
                    </div>
                </form>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Listings</CardTitle>
            </CardHeader>
            <CardContent class="space-y-2">
                <div v-for="listing in props.listings.data" :key="listing.id" class="flex items-center justify-between gap-3 rounded-md border p-3 text-sm">
                    <div>
                        <p class="font-medium">{{ listing.unit?.name ?? `Listing #${listing.id}` }}</p>
                        <p>Type: {{ listing.listing_type }} | Price: {{ listing.price }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <Badge :variant="listing.is_active ? 'default' : 'secondary'">{{ listing.is_active ? 'Active' : 'Inactive' }}</Badge>
                        <Button variant="outline" size="sm" @click="toggleListing(listing)">
                            {{ listing.is_active ? 'Deactivate' : 'Activate' }}
                        </Button>
                        <Button variant="destructive" size="sm" @click="removeListing(listing.id)">Delete</Button>
                    </div>
                </div>
                <p v-if="props.listings.data.length === 0" class="text-muted-foreground text-sm">No listings yet.</p>
            </CardContent>
        </Card>
    </div>
</template>
