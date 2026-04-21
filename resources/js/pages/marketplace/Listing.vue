<script setup lang="ts">
import { Head, Link, router, setLayoutProps, useForm } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.marketplace'), href: '/marketplace' },
            { title: t('app.navigation.listing'), href: '/marketplace/listing' },
        ],
    });
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
    <Head :title="t('app.marketplace.listing.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <Heading variant="small" :title="t('app.marketplace.listing.heading')" :description="t('app.marketplace.listing.description')" />
            <Button variant="outline" as-child>
                <Link href="/marketplace">{{ t('app.marketplace.common.backToOverview') }}</Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.marketplace.listing.addListing') }}</CardTitle>
            </CardHeader>
            <CardContent>
                <form class="grid gap-4 md:grid-cols-4" @submit.prevent="submitListing">
                    <div class="grid gap-2 md:col-span-2">
                        <Label for="unit_id">{{ t('app.marketplace.common.unit') }}</Label>
                        <select id="unit_id" v-model.number="listingForm.unit_id" class="rounded-md border border-input bg-background px-3 py-2">
                            <option :value="0">{{ t('app.marketplace.listing.selectUnit') }}</option>
                            <option v-for="unit in props.units" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
                        </select>
                        <InputError :message="listingForm.errors.unit_id" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="listing_type">{{ t('app.marketplace.common.type') }}</Label>
                        <select id="listing_type" v-model="listingForm.listing_type" class="rounded-md border border-input bg-background px-3 py-2">
                            <option value="rent">{{ t('app.marketplace.listing.rent') }}</option>
                            <option value="sale">{{ t('app.marketplace.listing.sale') }}</option>
                            <option value="both">{{ t('app.marketplace.listing.both') }}</option>
                        </select>
                        <InputError :message="listingForm.errors.listing_type" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="price">{{ t('app.marketplace.common.price') }}</Label>
                        <Input id="price" v-model="listingForm.price" type="number" min="0" step="0.01" />
                        <InputError :message="listingForm.errors.price" />
                    </div>
                    <div class="md:col-span-4">
                        <Button :disabled="listingForm.processing">{{ t('app.marketplace.listing.createButton') }}</Button>
                    </div>
                </form>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('app.marketplace.listing.listings') }}</CardTitle>
            </CardHeader>
            <CardContent class="space-y-2">
                <div v-for="listing in props.listings.data" :key="listing.id" class="flex items-center justify-between gap-3 rounded-md border p-3 text-sm">
                    <div>
                        <p class="font-medium">{{ listing.unit?.name ?? t('app.marketplace.common.listingFallback', { id: listing.id }) }}</p>
                        <p>{{ t('app.marketplace.common.type') }}: {{ listing.listing_type }} | {{ t('app.marketplace.common.price') }}: {{ listing.price }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <Badge :variant="listing.is_active ? 'default' : 'secondary'">{{ listing.is_active ? t('app.common.active') : t('app.common.inactive') }}</Badge>
                        <Button variant="outline" size="sm" @click="toggleListing(listing)">
                            {{ listing.is_active ? t('app.marketplace.listing.deactivate') : t('app.marketplace.listing.activate') }}
                        </Button>
                        <Button variant="destructive" size="sm" @click="removeListing(listing.id)">{{ t('app.actions.delete') }}</Button>
                    </div>
                </div>
                <p v-if="props.listings.data.length === 0" class="text-muted-foreground text-sm">{{ t('app.marketplace.listing.noListings') }}</p>
            </CardContent>
        </Card>
    </div>
</template>
