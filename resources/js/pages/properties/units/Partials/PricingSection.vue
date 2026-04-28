<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useI18n } from '@/composables/useI18n';
import type { Currency } from '@/types';

const { t } = useI18n();

defineProps<{
    currencies: Pick<Currency, 'id' | 'name' | 'code' | 'symbol'>[];
    currencyId: number | string;
    amount: number | string;
    period: string;
    errors: Record<string, string | undefined>;
}>();

const emit = defineEmits<{
    'update:currencyId': [value: string];
    'update:amount': [value: string];
    'update:period': [value: string];
}>();
</script>

<template>
    <fieldset class="space-y-4 rounded-lg border p-4">
        <legend class="px-1 text-sm font-semibold">
            {{ t('app.properties.units.edit.pricing.sectionTitle') }}
        </legend>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="grid gap-2">
                <Label for="pricing_currency">{{ t('app.properties.units.edit.pricing.selectCurrency') }}</Label>
                <select
                    id="pricing_currency"
                    :value="currencyId"
                    class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                    @change="emit('update:currencyId', ($event.target as HTMLSelectElement).value)"
                >
                    <option value="">—</option>
                    <option v-for="currency in currencies" :key="currency.id" :value="currency.id">
                        {{ currency.code }} {{ currency.symbol ? `(${currency.symbol})` : '' }}
                    </option>
                </select>
                <InputError :message="errors.currency_id" />
            </div>

            <div class="grid gap-2">
                <Label for="pricing_amount">{{ t('app.properties.units.edit.pricing.askingRent') }}</Label>
                <Input
                    id="pricing_amount"
                    type="number"
                    step="0.01"
                    min="0"
                    :value="amount"
                    @input="emit('update:amount', ($event.target as HTMLInputElement).value)"
                />
                <InputError :message="errors.asking_rent_amount" />
            </div>

            <div class="grid gap-2">
                <Label for="pricing_period">{{ t('app.properties.units.edit.pricing.selectPeriod') }}</Label>
                <select
                    id="pricing_period"
                    :value="period"
                    class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                    @change="emit('update:period', ($event.target as HTMLSelectElement).value)"
                >
                    <option value="">—</option>
                    <option value="year">{{ t('app.properties.units.edit.pricing.periodYear') }}</option>
                    <option value="month">{{ t('app.properties.units.edit.pricing.periodMonth') }}</option>
                </select>
                <InputError :message="errors.rent_period" />
            </div>
        </div>
    </fieldset>
</template>
