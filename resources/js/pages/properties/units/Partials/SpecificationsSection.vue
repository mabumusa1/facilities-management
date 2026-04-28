<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Label } from '@/components/ui/label';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();

const props = defineProps<{
    bedrooms: number;
    bathrooms: number;
    livingRooms: number;
    furnished: boolean;
    parkingBays: number;
    view: string;
    errors: Record<string, string | undefined>;
}>();

const emit = defineEmits<{
    'update:bedrooms': [value: number];
    'update:bathrooms': [value: number];
    'update:livingRooms': [value: number];
    'update:furnished': [value: boolean];
    'update:parkingBays': [value: number];
    'update:view': [value: string];
}>();

const roomCounts = Array.from({ length: 11 }, (_, i) => i);
</script>

<template>
    <fieldset class="space-y-4 rounded-lg border p-4">
        <legend class="px-1 text-sm font-semibold">
            {{ t('app.properties.units.edit.specifications.sectionTitle') }}
        </legend>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="grid gap-2">
                <Label for="spec_bedrooms">{{ t('app.properties.units.edit.specifications.bedrooms') }}</Label>
                <select
                    id="spec_bedrooms"
                    :value="bedrooms"
                    class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                    @change="emit('update:bedrooms', Number(($event.target as HTMLSelectElement).value))"
                >
                    <option v-for="n in roomCounts" :key="n" :value="n">{{ n }}</option>
                </select>
                <InputError :message="errors.bedrooms" />
            </div>

            <div class="grid gap-2">
                <Label for="spec_bathrooms">{{ t('app.properties.units.edit.specifications.bathrooms') }}</Label>
                <select
                    id="spec_bathrooms"
                    :value="bathrooms"
                    class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                    @change="emit('update:bathrooms', Number(($event.target as HTMLSelectElement).value))"
                >
                    <option v-for="n in roomCounts" :key="n" :value="n">{{ n }}</option>
                </select>
                <InputError :message="errors.bathrooms" />
            </div>

            <div class="grid gap-2">
                <Label for="spec_living_rooms">{{ t('app.properties.units.edit.specifications.livingRooms') }}</Label>
                <select
                    id="spec_living_rooms"
                    :value="livingRooms"
                    class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                    @change="emit('update:livingRooms', Number(($event.target as HTMLSelectElement).value))"
                >
                    <option v-for="n in roomCounts" :key="n" :value="n">{{ n }}</option>
                </select>
                <InputError :message="errors.livingRooms" />
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="grid gap-2">
                <Label>{{ t('app.properties.units.edit.specifications.furnished') }}</Label>
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        role="switch"
                        :aria-checked="furnished"
                        :class="[
                            'relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors focus:ring-2 focus:ring-offset-2 focus:outline-none',
                            furnished ? 'bg-primary' : 'bg-input',
                        ]"
                        @click="emit('update:furnished', !furnished)"
                    >
                        <span
                            :class="[
                                'pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transition-transform',
                                furnished ? 'translate-x-5' : 'translate-x-0',
                            ]"
                        />
                    </button>
                    <span class="text-sm">
                        {{ furnished
                            ? t('app.properties.units.edit.specifications.furnishedYes')
                            : t('app.properties.units.edit.specifications.furnishedNo') }}
                    </span>
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="spec_parking">{{ t('app.properties.units.edit.specifications.parking') }}</Label>
                <select
                    id="spec_parking"
                    :value="parkingBays"
                    class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                    @change="emit('update:parkingBays', Number(($event.target as HTMLSelectElement).value))"
                >
                    <option value="0">{{ t('app.properties.units.edit.specifications.parkingNone') }}</option>
                    <option value="1">{{ t('app.properties.units.edit.specifications.parkingOneBay') }}</option>
                    <option value="2">{{ t('app.properties.units.edit.specifications.parkingTwoBays') }}</option>
                </select>
                <InputError :message="errors.parkingBays" />
            </div>

            <div class="grid gap-2">
                <Label for="spec_view">{{ t('app.properties.units.edit.specifications.view') }}</Label>
                <select
                    id="spec_view"
                    :value="view"
                    class="border-input bg-background ring-offset-background flex h-10 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                    @change="emit('update:view', ($event.target as HTMLSelectElement).value)"
                >
                    <option value="none">{{ t('app.properties.units.edit.specifications.viewNone') }}</option>
                    <option value="sea_view">{{ t('app.properties.units.edit.specifications.viewSea') }}</option>
                    <option value="city_view">{{ t('app.properties.units.edit.specifications.viewCity') }}</option>
                    <option value="garden_view">{{ t('app.properties.units.edit.specifications.viewGarden') }}</option>
                </select>
                <InputError :message="errors.view" />
            </div>
        </div>
    </fieldset>
</template>
