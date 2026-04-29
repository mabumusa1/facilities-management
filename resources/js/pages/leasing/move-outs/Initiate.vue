<script setup lang="ts">
import { Head, useForm, setLayoutProps } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { store as storeMoveOut } from '@/actions/App/Http/Controllers/Leasing/MoveOutController';
import { show as leasesShow } from '@/actions/App/Http/Controllers/Leasing/LeaseController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useI18n } from '@/composables/useI18n';

type ReasonOption = { value: string; label: string };

type LeaseDetail = {
    id: number;
    contract_number: string;
    end_date: string | null;
    security_deposit_amount: string | null;
    status: { id: number; name: string; name_en: string | null } | null;
    tenant: { id: number; name: string } | null;
    units: { id: number; name: string }[];
};

const props = defineProps<{
    lease: LeaseDetail;
    reasons: ReasonOption[];
}>();

const { t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.leases.pageTitle'), href: '/leases' },
            { title: props.lease.contract_number, href: leasesShow.url(props.lease.id) },
            { title: t('app.moveout.initiate.title'), href: '#' },
        ],
    });
});

const form = useForm({
    move_out_date: props.lease.end_date ?? '',
    reason: '',
    notes: '',
});

function submit() {
    form.post(storeMoveOut.url(props.lease.id));
}
</script>

<template>
    <Head :title="t('app.moveout.initiate.title')" />

    <div class="space-y-6">
        <Heading
            :title="t('app.moveout.initiate.title')"
            :description="lease.contract_number"
        />

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <!-- Lease summary sidebar -->
            <Card class="md:col-span-1">
                <CardHeader>
                    <CardTitle>{{ t('app.leases.show.breadcrumb') }}</CardTitle>
                </CardHeader>
                <CardContent class="space-y-3 text-sm">
                    <div v-if="lease.tenant">
                        <span class="font-medium">{{ t('app.leases.show.tenantLabel') }}:</span>
                        {{ lease.tenant.name }}
                    </div>
                    <div v-if="lease.units.length > 0">
                        <span class="font-medium">{{ t('app.leases.show.unitLabel') }}:</span>
                        {{ lease.units.map((u) => u.name).join(', ') }}
                    </div>
                    <div v-if="lease.end_date">
                        <span class="font-medium">{{ t('app.leases.create.endDate') }}:</span>
                        {{ lease.end_date }}
                    </div>
                    <div v-if="lease.security_deposit_amount">
                        <span class="font-medium">{{ t('app.moveout.deductions.securityDepositLabel') }}:</span>
                        {{ Number(lease.security_deposit_amount).toLocaleString() }}
                    </div>
                </CardContent>
            </Card>

            <!-- Form -->
            <Card class="md:col-span-2">
                <CardHeader>
                    <CardTitle>{{ t('app.moveout.initiate.title') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <form class="space-y-6" @submit.prevent="submit">
                        <!-- Move-out date -->
                        <div class="space-y-2">
                            <Label for="move_out_date">{{ t('app.moveout.initiate.date') }} *</Label>
                            <Input
                                id="move_out_date"
                                v-model="form.move_out_date"
                                type="date"
                            />
                            <InputError :message="form.errors.move_out_date" />
                        </div>

                        <!-- Reason -->
                        <div class="space-y-3">
                            <Label>{{ t('app.moveout.initiate.reason') }} *</Label>
                            <div
                                v-for="option in reasons"
                                :key="option.value"
                                class="flex items-center gap-2"
                            >
                                <input
                                    :id="`reason_${option.value}`"
                                    v-model="form.reason"
                                    type="radio"
                                    :value="option.value"
                                    class="size-4"
                                />
                                <Label :for="`reason_${option.value}`" class="cursor-pointer font-normal">
                                    {{ option.label }}
                                </Label>
                            </div>
                            <InputError :message="form.errors.reason" />
                        </div>

                        <!-- Notes -->
                        <div class="space-y-2">
                            <Label for="notes">{{ t('app.common.notes') }}</Label>
                            <Textarea
                                id="notes"
                                v-model="form.notes"
                                rows="3"
                            />
                            <InputError :message="form.errors.notes" />
                        </div>

                        <!-- Warning banner -->
                        <div
                            class="rounded-md border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-700 dark:bg-amber-950 dark:text-amber-200"
                            role="note"
                        >
                            {{ t('app.moveout.initiate.warning') }}
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-3">
                            <Button
                                as="a"
                                variant="outline"
                                :href="leasesShow.url(lease.id)"
                            >
                                {{ t('app.common.cancel') }}
                            </Button>
                            <Button
                                type="submit"
                                :disabled="form.processing"
                            >
                                {{ t('app.moveout.initiate.button') }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
