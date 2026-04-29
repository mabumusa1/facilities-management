<script setup lang="ts">
import { Head, Link, useForm, setLayoutProps } from '@inertiajs/vue3';
import { ref, watchEffect } from 'vue';
import {
    index as leasesIndex,
    show as leasesShow,
} from '@/actions/App/Http/Controllers/Leasing/LeaseController';
import {
    index as noticesIndex,
    store as noticesStore,
    show as noticesShow,
} from '@/actions/App/Http/Controllers/Leasing/LeaseNoticeController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();

type NoticeType = {
    value: string;
    label: string;
};

type NoticeRecord = {
    id: number;
    type: string;
    subject_en: string;
    subject_ar: string;
    body_en: string | null;
    body_ar: string | null;
    sent_at: string | null;
    sent_by: { id: number; name: string } | null;
};

type Pagination<T> = {
    data: T[];
    total: number;
    per_page: number;
    current_page: number;
    last_page: number;
};

type Tenant = {
    id: number;
    first_name: string | null;
    last_name: string | null;
    email: string | null;
};

type LeaseDetail = {
    id: number;
    contract_number: string;
    tenant_id: number | null;
};

const props = defineProps<{
    lease: LeaseDetail;
    tenant: Tenant | null;
    notices: Pagination<NoticeRecord>;
    noticeTypes: NoticeType[];
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.leases.pageTitle'), href: leasesIndex.url() },
            { title: props.lease.contract_number, href: leasesShow.url(props.lease.id) },
            { title: t('app.leases.notices.breadcrumb'), href: '#' },
        ],
    });
});

// ── Notice type labels ─────────────────────────────────────────────────────────
const noticeTypeLabel: Record<string, string> = {
    rent_increase: t('app.leases.notices.rentIncrease'),
    renewal_offer: t('app.leases.notices.renewalOffer'),
    move_out_reminder: t('app.leases.notices.moveOutReminder'),
    free_form: t('app.leases.notices.freeForm'),
};

// ── Send form ──────────────────────────────────────────────────────────────────
const form = useForm({
    type: '',
    subject_en: '',
    body_en: '',
    subject_ar: '',
    body_ar: '',
});

const previewOpen = ref(false);

function submitNotice() {
    form.post(noticesStore.url(props.lease.id), {
        onSuccess: () => form.reset(),
    });
}

// ── History expand ─────────────────────────────────────────────────────────────
const expandedNotices = ref<Set<number>>(new Set());
const loadedBodies = ref<Record<number, { body_en: string; body_ar: string }>>({});

async function toggleNotice(notice: NoticeRecord) {
    if (expandedNotices.value.has(notice.id)) {
        expandedNotices.value.delete(notice.id);
        return;
    }

    if (! loadedBodies.value[notice.id]) {
        const response = await fetch(noticesShow.url({ lease: props.lease.id, notice: notice.id }), {
            headers: { Accept: 'application/json' },
        });
        if (response.ok) {
            const data = await response.json();
            loadedBodies.value[notice.id] = { body_en: data.body_en, body_ar: data.body_ar };
        }
    }

    expandedNotices.value.add(notice.id);
}

const tenantName = props.tenant
    ? `${props.tenant.first_name ?? ''} ${props.tenant.last_name ?? ''}`.trim()
    : '—';
</script>

<template>
    <div>
        <Head :title="t('app.leases.notices.title')" />

        <div class="flex flex-col gap-6 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight">{{ t('app.leases.notices.title') }}</h2>
                    <p class="text-muted-foreground text-sm">{{ lease.contract_number }}</p>
                </div>
                <Button variant="outline" as-child>
                    <Link :href="leasesShow.url(lease.id)">← {{ lease.contract_number }}</Link>
                </Button>
            </div>

            <!-- Missing email warning -->
            <div
                v-if="! tenant?.email"
                role="alert"
                class="bg-destructive/10 text-destructive rounded-lg border border-destructive/20 p-4"
            >
                <p class="font-medium">{{ t('app.leases.notices.errorMissingEmail') }}</p>
                <Link
                    :href="`/residents/${tenant?.id ?? ''}/edit`"
                    class="text-destructive underline text-sm mt-1 inline-block"
                >
                    {{ t('app.leases.notices.errorEditContact') }} →
                </Link>
            </div>

            <!-- Send Notice Form -->
            <Card v-if="tenant?.email">
                <CardHeader>
                    <CardTitle>{{ t('app.leases.notices.title') }}</CardTitle>
                    <p v-if="tenant" class="text-muted-foreground text-sm">
                        {{ t('app.leases.notices.to', { name: tenantName, email: tenant.email }) }}
                    </p>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submitNotice" class="space-y-6">

                        <!-- Notice Type -->
                        <fieldset>
                            <legend class="text-sm font-medium mb-2" aria-required="true">
                                {{ t('app.leases.notices.type') }} *
                            </legend>
                            <div class="space-y-2" role="radiogroup">
                                <label
                                    v-for="nt in noticeTypes"
                                    :key="nt.value"
                                    class="flex items-center gap-2 cursor-pointer"
                                >
                                    <input
                                        type="radio"
                                        name="type"
                                        :value="nt.value"
                                        v-model="form.type"
                                        aria-required="true"
                                        class="text-primary"
                                    />
                                    <span class="text-sm">{{ noticeTypeLabel[nt.value] ?? nt.label }}</span>
                                </label>
                            </div>
                            <p v-if="form.errors.type" class="text-destructive text-sm mt-1">{{ form.errors.type }}</p>
                        </fieldset>

                        <!-- EN Section -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-muted-foreground border-b pb-1">EN</h3>

                            <div class="space-y-1">
                                <Label for="subject_en" aria-required="true">{{ t('app.leases.notices.subjectEn') }} *</Label>
                                <Input
                                    id="subject_en"
                                    v-model="form.subject_en"
                                    aria-required="true"
                                    :aria-invalid="!! form.errors.subject_en"
                                />
                                <p v-if="form.errors.subject_en" class="text-destructive text-sm">{{ form.errors.subject_en }}</p>
                            </div>

                            <div class="space-y-1">
                                <Label for="body_en" aria-required="true">{{ t('app.leases.notices.bodyEn') }} *</Label>
                                <Textarea
                                    id="body_en"
                                    v-model="form.body_en"
                                    rows="5"
                                    aria-required="true"
                                    :aria-invalid="!! form.errors.body_en"
                                />
                                <p v-if="form.errors.body_en" class="text-destructive text-sm">{{ form.errors.body_en }}</p>
                            </div>
                        </div>

                        <!-- AR Section -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-muted-foreground border-b pb-1" dir="rtl">AR</h3>

                            <div class="space-y-1" dir="rtl">
                                <Label for="subject_ar" aria-required="true">{{ t('app.leases.notices.subjectAr') }} *</Label>
                                <Input
                                    id="subject_ar"
                                    v-model="form.subject_ar"
                                    dir="rtl"
                                    aria-required="true"
                                    :aria-invalid="!! form.errors.subject_ar"
                                />
                                <p v-if="form.errors.subject_ar" class="text-destructive text-sm">{{ form.errors.subject_ar }}</p>
                            </div>

                            <div class="space-y-1" dir="rtl">
                                <Label for="body_ar" aria-required="true">{{ t('app.leases.notices.bodyAr') }} *</Label>
                                <Textarea
                                    id="body_ar"
                                    v-model="form.body_ar"
                                    rows="5"
                                    dir="rtl"
                                    aria-required="true"
                                    :aria-invalid="!! form.errors.body_ar"
                                />
                                <p v-if="form.errors.body_ar" class="text-destructive text-sm">{{ form.errors.body_ar }}</p>
                            </div>
                        </div>

                        <!-- Preview dialog trigger -->
                        <div v-if="form.subject_en && form.body_en" class="rounded-md border p-4 bg-muted/40">
                            <button
                                type="button"
                                class="text-sm text-primary underline"
                                @click="previewOpen = ! previewOpen"
                                :aria-expanded="previewOpen"
                            >
                                {{ t('app.leases.notices.preview') }}
                            </button>
                            <div v-if="previewOpen" class="mt-3 space-y-2 text-sm">
                                <p class="font-medium">{{ form.subject_en }}</p>
                                <p class="whitespace-pre-wrap text-muted-foreground">{{ form.body_en }}</p>
                                <hr class="my-2" />
                                <p class="font-medium" dir="rtl">{{ form.subject_ar }}</p>
                                <p class="whitespace-pre-wrap text-muted-foreground" dir="rtl">{{ form.body_ar }}</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end gap-2">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                            >
                                {{ form.processing ? '…' : t('app.leases.notices.send') }}
                            </Button>
                        </div>

                        <div
                            v-if="form.errors.tenant_email"
                            role="alert"
                            class="text-destructive text-sm"
                        >
                            {{ form.errors.tenant_email }}
                        </div>

                        <div
                            v-if="form.wasSuccessful"
                            role="status"
                            aria-live="polite"
                            class="text-green-600 text-sm"
                        >
                            {{ t('app.leases.notices.sent', { email: tenant?.email ?? '' }) }}
                        </div>
                    </form>
                </CardContent>
            </Card>

            <!-- Notice History -->
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('app.leases.notices.historyTitle') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <div v-if="notices.data.length === 0" class="text-muted-foreground text-sm py-4 text-center">
                        —
                    </div>

                    <div v-else class="space-y-2">
                        <Collapsible
                            v-for="notice in notices.data"
                            :key="notice.id"
                            :open="expandedNotices.has(notice.id)"
                        >
                            <div class="rounded-md border p-3">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <Badge variant="secondary">{{ noticeTypeLabel[notice.type] ?? notice.type }}</Badge>
                                            <span class="text-muted-foreground text-xs">{{ notice.sent_at }}</span>
                                        </div>
                                        <p class="text-sm font-medium mt-1 truncate">{{ notice.subject_en }}</p>
                                    </div>
                                    <CollapsibleTrigger as-child>
                                        <button
                                            type="button"
                                            class="text-sm text-primary underline shrink-0"
                                            :aria-expanded="expandedNotices.has(notice.id)"
                                            @click="toggleNotice(notice)"
                                        >
                                            {{ t('app.leases.notices.historyViewBody') }}
                                        </button>
                                    </CollapsibleTrigger>
                                </div>

                                <CollapsibleContent>
                                    <div v-if="loadedBodies[notice.id]" class="mt-3 space-y-3 text-sm border-t pt-3">
                                        <div>
                                            <p class="font-medium text-muted-foreground text-xs mb-1">EN</p>
                                            <p class="whitespace-pre-wrap">{{ loadedBodies[notice.id].body_en }}</p>
                                        </div>
                                        <div dir="rtl">
                                            <p class="font-medium text-muted-foreground text-xs mb-1">AR</p>
                                            <p class="whitespace-pre-wrap">{{ loadedBodies[notice.id].body_ar }}</p>
                                        </div>
                                    </div>
                                    <div v-else class="mt-3 text-sm text-muted-foreground animate-pulse">
                                        {{ t('app.leases.notices.historyViewBody') }}…
                                    </div>
                                </CollapsibleContent>
                            </div>
                        </Collapsible>
                    </div>

                    <p v-if="notices.total > 0" class="text-muted-foreground text-xs mt-3">
                        {{ t('app.leases.notices.historyCount', { n: notices.total }) }}
                    </p>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
