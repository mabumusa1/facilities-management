<script setup lang="ts">
import { ref, watchEffect } from 'vue';
import { Head, Link, setLayoutProps } from '@inertiajs/vue3';
import { create, index } from '@/actions/App/Http/Controllers/Services/ResidentServiceRequestController';
import { Check, ClipboardCopy } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { useI18n } from '@/composables/useI18n';

type Category = {
    id: number;
    name_en: string;
    name_ar: string;
};

type ServiceRequestConfirmation = {
    id: number;
    request_code: string;
    urgency: string;
    room_location: string | null;
    description: string;
    sla_response_due_at: string | null;
    sla_resolution_due_at: string | null;
    category: Category | null;
    subcategory: Category | null;
};

const props = defineProps<{
    serviceRequest: ServiceRequestConfirmation;
}>();

const { isArabic, t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.serviceRequests.pageTitle'), href: index.url() },
            { title: props.serviceRequest.request_code ?? String(props.serviceRequest.id), href: '#' },
        ],
    });
});

const copied = ref(false);

function copyReferenceNumber(): void {
    if (!props.serviceRequest.request_code) {
        return;
    }
    navigator.clipboard.writeText(props.serviceRequest.request_code).then(() => {
        copied.value = true;
        setTimeout(() => {
            copied.value = false;
        }, 2000);
    });
}

function localizedName(cat: Category): string {
    return isArabic.value ? cat.name_ar : cat.name_en;
}

function formatDateTime(isoString: string | null): string {
    if (!isoString) {
        return '';
    }
    return new Date(isoString).toLocaleString(isArabic.value ? 'ar' : 'en', {
        dateStyle: 'medium',
        timeStyle: 'short',
    });
}
</script>

<template>
    <Head :title="t('app.serviceRequests.confirmedHeading')" />

    <div class="flex min-h-[60vh] items-center justify-center p-4">
        <div
            class="w-full max-w-md space-y-6 rounded-xl border bg-card p-8 shadow-sm"
            role="status"
            aria-live="polite"
        >
            <!-- Success icon -->
            <div class="flex flex-col items-center gap-3 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                    <Check class="h-8 w-8 text-green-600 dark:text-green-400" />
                </div>

                <h1 class="text-xl font-semibold">
                    {{ t('app.serviceRequests.confirmedHeading') }}
                </h1>
            </div>

            <!-- Reference number -->
            <div class="rounded-lg border bg-muted/50 p-4">
                <p class="mb-2 text-sm text-muted-foreground">
                    {{ t('app.serviceRequests.referenceLabel') }}
                </p>
                <div class="flex items-center gap-2">
                    <bdi
                        dir="ltr"
                        class="flex-1 font-mono text-lg font-semibold tracking-wider"
                    >
                        {{ serviceRequest.request_code }}
                    </bdi>
                    <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        :aria-label="t('app.serviceRequests.copyButton')"
                        @click="copyReferenceNumber"
                    >
                        <ClipboardCopy
                            v-if="!copied"
                            class="h-4 w-4"
                        />
                        <Check
                            v-else
                            class="h-4 w-4 text-green-600"
                        />
                        <span class="sr-only">{{ t('app.serviceRequests.copyButton') }}</span>
                    </Button>
                </div>
            </div>

            <!-- Category info -->
            <div
                v-if="serviceRequest.category"
                class="space-y-1 text-sm"
            >
                <span class="font-medium">
                    {{ localizedName(serviceRequest.category) }}
                    <template v-if="serviceRequest.subcategory">
                        — {{ localizedName(serviceRequest.subcategory) }}
                    </template>
                </span>
            </div>

            <!-- SLA times -->
            <div
                v-if="serviceRequest.sla_response_due_at || serviceRequest.sla_resolution_due_at"
                class="space-y-2 text-sm"
            >
                <div
                    v-if="serviceRequest.sla_response_due_at"
                    class="flex justify-between"
                >
                    <span class="text-muted-foreground">{{ t('app.serviceRequests.expectedResponseLabel') }}</span>
                    <span class="font-medium">{{ formatDateTime(serviceRequest.sla_response_due_at) }}</span>
                </div>
                <div
                    v-if="serviceRequest.sla_resolution_due_at"
                    class="flex justify-between"
                >
                    <span class="text-muted-foreground">{{ t('app.serviceRequests.expectedResolutionLabel') }}</span>
                    <span class="font-medium">{{ formatDateTime(serviceRequest.sla_resolution_due_at) }}</span>
                </div>
            </div>

            <p class="text-center text-sm text-muted-foreground">
                {{ t('app.serviceRequests.confirmedDescription') }}
            </p>

            <!-- CTAs -->
            <div class="flex flex-col gap-3 sm:flex-row">
                <Link
                    :href="index.url()"
                    class="flex-1"
                >
                    <Button
                        class="w-full"
                        variant="default"
                    >
                        {{ t('app.serviceRequests.viewMyRequests') }}
                    </Button>
                </Link>

                <Link
                    :href="create.url()"
                    class="flex-1"
                >
                    <Button
                        class="w-full"
                        variant="outline"
                    >
                        {{ t('app.serviceRequests.submitAnother') }}
                    </Button>
                </Link>
            </div>
        </div>
    </div>
</template>
