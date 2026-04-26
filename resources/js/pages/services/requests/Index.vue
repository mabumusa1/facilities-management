<script setup lang="ts">
import { watchEffect } from 'vue';
import { Head, Link, setLayoutProps } from '@inertiajs/vue3';
import { create, index } from '@/actions/App/Http/Controllers/Services/ResidentServiceRequestController';
import PageHeader from '@/components/PageHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { useI18n } from '@/composables/useI18n';

type Category = {
    id: number;
    name_en: string;
    name_ar: string;
};

type ServiceRequestStatus = {
    id: number;
    name: string;
    name_en: string | null;
    name_ar: string | null;
};

type ResidentServiceRequest = {
    id: number;
    request_code: string | null;
    urgency: string;
    room_location: string | null;
    description: string;
    sla_response_due_at: string | null;
    sla_resolution_due_at: string | null;
    created_at: string | null;
    category: Category | null;
    subcategory: Category | null;
    status: ServiceRequestStatus | null;
};

type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: { url: string | null; label: string; active: boolean }[];
};

const props = defineProps<{
    serviceRequests: Paginated<ResidentServiceRequest>;
}>();

const { isArabic, t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.serviceRequests.pageTitle'), href: index.url() },
        ],
    });
});

function localizedName(cat: Category): string {
    return isArabic.value ? cat.name_ar : cat.name_en;
}

function localizedStatus(status: ServiceRequestStatus): string {
    if (isArabic.value) {
        return status.name_ar ?? status.name_en ?? status.name;
    }
    return status.name_en ?? status.name;
}

function formatDate(isoString: string | null): string {
    if (!isoString) {
        return '';
    }
    return new Date(isoString).toLocaleDateString(isArabic.value ? 'ar' : 'en', {
        dateStyle: 'medium',
    });
}
</script>

<template>
    <Head :title="t('app.serviceRequests.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader
            :title="t('app.serviceRequests.heading')"
            :description="t('app.serviceRequests.description')"
        >
            <template #actions>
                <Link :href="create.url()">
                    <Button>{{ t('app.serviceRequests.newRequest') }}</Button>
                </Link>
            </template>
        </PageHeader>

        <!-- Empty state -->
        <div
            v-if="serviceRequests.data.length === 0"
            class="flex flex-col items-center justify-center gap-4 rounded-lg border border-dashed p-12 text-center"
        >
            <p class="text-muted-foreground">{{ t('app.serviceRequests.noRequestsYet') }}</p>
            <Link :href="create.url()">
                <Button variant="outline">{{ t('app.serviceRequests.newRequest') }}</Button>
            </Link>
        </div>

        <!-- Request cards -->
        <div
            v-else
            class="space-y-4"
        >
            <div
                v-for="sr in serviceRequests.data"
                :key="sr.id"
                class="rounded-lg border bg-card p-5 shadow-sm"
            >
                <div class="flex items-start justify-between gap-2">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2">
                            <bdi
                                dir="ltr"
                                class="font-mono text-sm font-semibold text-primary"
                            >
                                {{ sr.request_code }}
                            </bdi>
                            <Badge
                                v-if="sr.urgency === 'urgent'"
                                variant="destructive"
                                class="text-xs"
                            >
                                {{ t('app.serviceRequests.urgencyUrgent') }}
                            </Badge>
                        </div>
                        <div class="text-sm text-muted-foreground">
                            <template v-if="sr.category">
                                {{ localizedName(sr.category) }}
                                <template v-if="sr.subcategory">
                                    — {{ localizedName(sr.subcategory) }}
                                </template>
                            </template>
                            <template v-if="sr.room_location">
                                · {{ sr.room_location }}
                            </template>
                            · {{ formatDate(sr.created_at) }}
                        </div>
                    </div>

                    <Badge
                        v-if="sr.status"
                        variant="outline"
                        class="shrink-0"
                    >
                        {{ localizedStatus(sr.status) }}
                    </Badge>
                </div>

                <p class="mt-3 line-clamp-2 text-sm text-foreground">
                    {{ sr.description }}
                </p>
            </div>

            <!-- Pagination -->
            <div
                v-if="serviceRequests.last_page > 1"
                class="flex justify-center gap-1"
            >
                <template
                    v-for="link in serviceRequests.links"
                    :key="link.label"
                >
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="rounded px-3 py-1 text-sm transition-colors"
                        :class="link.active
                            ? 'bg-primary text-primary-foreground'
                            : 'hover:bg-muted'"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>
    </div>
</template>
