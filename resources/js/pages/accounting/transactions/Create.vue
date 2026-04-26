<script setup lang="ts">
import { Head, Link, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, watchEffect } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import WarningBanner from '@/components/WarningBanner.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import type { Lease, Unit, Status, Resident, Owner } from '@/types';

const { t } = useI18n();

const props = defineProps<{
    leases: Pick<Lease, 'id' | 'contract_number'>[];
    units: Pick<Unit, 'id' | 'name'>[];
    statuses: Pick<Status, 'id' | 'name' | 'name_en'>[];
    tenants: Pick<Resident, 'id' | 'first_name' | 'last_name'>[];
    owners: Pick<Owner, 'id' | 'first_name' | 'last_name'>[];
    transactionCategories: { id: number; name: string; name_en: string | null }[];
    transactionTypes: { id: number; name: string; name_en: string | null }[];
    invoiceSettingComplete: boolean;
}>();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.navigation.transactions'), href: '/transactions' },
            { title: t('app.transactions.create.breadcrumb'), href: '/transactions/create' },
        ],
    });
});

const today = new Date().toISOString().split('T')[0];

const form = useForm({
    lease_id: '',
    unit_id: '',
    category_id: props.transactionCategories[0] ? String(props.transactionCategories[0].id) : '',
    type_id: '1',
    status_id: props.statuses[0] ? String(props.statuses[0].id) : '',
    assignee_id: '',
    assignee_type: 'App\\Models\\Resident' as string,
    amount: '',
    tax_amount: '0',
    due_date: today,
    notes: '',
    direction: 'money_in',
    payment_method: '',
    reference_number: '',
    generate_receipt: true,
});

const ctaLabel = computed(() =>
    props.invoiceSettingComplete
        ? t('app.transactions.create.saveWithReceipt')
        : t('app.transactions.create.saveWithoutReceipt'),
);

function resolveAssignee(value: string): { assignee_id: string; assignee_type: string } {
    if (value.startsWith('owner:')) {
        return { assignee_id: value.replace('owner:', ''), assignee_type: 'App\\Models\\Owner' };
    }

    return { assignee_id: value.replace('resident:', ''), assignee_type: 'App\\Models\\Resident' };
}

function submit() {
    const { assignee_id, assignee_type } = resolveAssignee(form.assignee_id);
    form.assignee_id = assignee_id;
    form.assignee_type = assignee_type;
    form.generate_receipt = props.invoiceSettingComplete;
    form.post('/transactions');
}
</script>

<template>
    <Head :title="t('app.transactions.create.pageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            variant="small"
            :title="t('app.transactions.create.heading')"
            :description="t('app.transactions.create.description')"
        />

        <div class="max-w-2xl space-y-6">
            <!-- InvoiceSetting incomplete banner -->
            <WarningBanner
                v-if="!invoiceSettingComplete"
                :title="t('app.transactions.create.invoiceIncompleteTitle')"
                :message="t('app.transactions.create.invoiceIncompleteMessage')"
                :link-href="'/app-settings/invoice'"
                :link-label="t('app.transactions.create.configureSettings')"
            />

            <!-- Direction badge (read-only) -->
            <div class="flex items-center gap-2">
                <Badge variant="default">{{ t('app.transactions.create.directionLabel') }}</Badge>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Payer + Unit -->
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="assignee_id">{{ t('app.transactions.create.payer') }} *</Label>
                        <Select v-model="form.assignee_id">
                            <SelectTrigger id="assignee_id" class="w-full">
                                <SelectValue :placeholder="t('app.transactions.create.selectPayer')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem disabled value="__residents__">
                                    {{ t('app.transactions.create.residentsGroup') }}
                                </SelectItem>
                                <SelectItem
                                    v-for="tenant in tenants"
                                    :key="`r-${tenant.id}`"
                                    :value="`resident:${tenant.id}`"
                                >
                                    {{ tenant.first_name }} {{ tenant.last_name }}
                                </SelectItem>
                                <SelectItem disabled value="__owners__">
                                    {{ t('app.transactions.create.ownersGroup') }}
                                </SelectItem>
                                <SelectItem
                                    v-for="owner in owners"
                                    :key="`o-${owner.id}`"
                                    :value="`owner:${owner.id}`"
                                >
                                    {{ owner.first_name }} {{ owner.last_name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.assignee_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="unit_id">{{ t('app.transactions.table.unit') }} *</Label>
                        <Select v-model="form.unit_id">
                            <SelectTrigger id="unit_id" class="w-full">
                                <SelectValue :placeholder="t('app.transactions.create.selectUnit')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="unit in units" :key="unit.id" :value="String(unit.id)">
                                    {{ unit.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.unit_id" />
                    </div>
                </div>

                <!-- Category + Payment Method -->
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="category_id">{{ t('app.transactions.table.category') }} *</Label>
                        <Select v-model="form.category_id">
                            <SelectTrigger id="category_id" class="w-full">
                                <SelectValue :placeholder="t('app.transactions.create.selectCategory')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="cat in transactionCategories" :key="cat.id" :value="String(cat.id)">
                                    {{ cat.name_en ?? cat.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.category_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="payment_method">{{ t('app.transactions.create.paymentMethod') }} *</Label>
                        <Select v-model="form.payment_method">
                            <SelectTrigger id="payment_method" class="w-full">
                                <SelectValue :placeholder="t('app.transactions.create.selectPaymentMethod')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="cash">{{ t('app.transactions.create.cash') }}</SelectItem>
                                <SelectItem value="bank_transfer">{{ t('app.transactions.create.bankTransfer') }}</SelectItem>
                                <SelectItem value="cheque">{{ t('app.transactions.create.cheque') }}</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.payment_method" />
                    </div>
                </div>

                <!-- Amount + Tax Amount -->
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="amount">{{ t('app.transactions.table.amount') }} *</Label>
                        <Input
                            id="amount"
                            v-model="form.amount"
                            type="number"
                            step="0.01"
                            min="0"
                            dir="ltr"
                            inputmode="decimal"
                            required
                        />
                        <InputError :message="form.errors.amount" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="tax_amount">{{ t('app.transactions.create.taxAmount', {}, 'Tax Amount') }}</Label>
                        <Input
                            id="tax_amount"
                            v-model="form.tax_amount"
                            type="number"
                            step="0.01"
                            min="0"
                            dir="ltr"
                            inputmode="decimal"
                        />
                        <InputError :message="form.errors.tax_amount" />
                    </div>
                </div>

                <!-- Payment Date + Reference Number -->
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="due_date">{{ t('app.transactions.create.paymentDate') }} *</Label>
                        <Input
                            id="due_date"
                            v-model="form.due_date"
                            type="date"
                            dir="ltr"
                            required
                        />
                        <InputError :message="form.errors.due_date" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="reference_number">{{ t('app.transactions.create.referenceNumber') }}</Label>
                        <Input
                            id="reference_number"
                            v-model="form.reference_number"
                            type="text"
                            dir="ltr"
                        />
                        <InputError :message="form.errors.reference_number" />
                    </div>
                </div>

                <!-- Lease (optional) -->
                <div class="grid gap-2">
                    <Label for="lease_id">{{ t('app.transactions.create.leaseOptional') }}</Label>
                    <Select v-model="form.lease_id">
                        <SelectTrigger id="lease_id" class="w-full">
                            <SelectValue :placeholder="t('app.transactions.create.selectLease')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">{{ '—' }}</SelectItem>
                            <SelectItem v-for="lease in leases" :key="lease.id" :value="String(lease.id)">
                                {{ lease.contract_number }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.lease_id" />
                </div>

                <!-- Notes -->
                <div class="grid gap-2">
                    <Label for="notes">{{ t('app.transactions.edit.notes') }}</Label>
                    <Textarea
                        id="notes"
                        v-model="form.notes"
                        :placeholder="t('app.transactions.edit.notesPlaceholder')"
                    />
                    <InputError :message="form.errors.notes" />
                </div>

                <!-- CTAs -->
                <div class="flex items-center gap-4">
                    <Button type="submit" :disabled="form.processing">
                        {{ ctaLabel }}
                    </Button>
                    <Link href="/transactions" class="text-muted-foreground text-sm hover:underline">
                        {{ t('app.transactions.create.cancel') }}
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>
