<script setup lang="ts">
import { computed, ref, watch, watchEffect } from 'vue';
import { Head, Link, setLayoutProps, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { useI18n } from '@/composables/useI18n';

type ServiceSubcategory = {
    id: number;
    service_category_id: number;
    name_en: string;
    name_ar: string;
    response_sla_hours: number | null;
    resolution_sla_hours: number | null;
};

type ServiceCategory = {
    id: number;
    name_en: string;
    name_ar: string;
    icon: string;
    response_sla_hours: number | null;
    resolution_sla_hours: number | null;
    subcategories: ServiceSubcategory[];
};

type Community = {
    id: number;
    name: string;
};

type Unit = {
    id: number;
    name: string;
    rf_community_id: number;
};

const props = defineProps<{
    categories: ServiceCategory[];
    communities: Community[];
    units: Unit[];
    roomOptions: string[];
}>();

const { isArabic, t } = useI18n();

watchEffect(() => {
    setLayoutProps({
        breadcrumbs: [
            { title: t('app.navigation.dashboard'), href: '/dashboard' },
            { title: t('app.serviceRequests.pageTitle'), href: '/service-requests' },
            { title: t('app.serviceRequests.createPageTitle'), href: '/service-requests/create' },
        ],
    });
});

const form = useForm({
    service_category_id: '',
    service_subcategory_id: '',
    community_id: '',
    unit_id: '',
    room_location: '',
    urgency: 'normal',
    description: '',
});

const descriptionLength = computed(() => form.description.length);

const filteredSubcategories = computed((): ServiceSubcategory[] => {
    if (!form.service_category_id) {
        return [];
    }
    const cat = props.categories.find((c) => c.id === Number(form.service_category_id));
    return cat?.subcategories ?? [];
});

const filteredUnits = computed((): Unit[] => {
    if (!form.community_id) {
        return props.units;
    }
    return props.units.filter((u) => u.rf_community_id === Number(form.community_id));
});

watch(
    () => form.service_category_id,
    () => {
        form.service_subcategory_id = '';
    },
);

watch(
    () => form.community_id,
    () => {
        form.unit_id = '';
    },
);

function localizedName(option: { name_en: string; name_ar: string }): string {
    return isArabic.value ? option.name_ar : option.name_en;
}

function roomLabel(key: string): string {
    const map: Record<string, string> = {
        kitchen: t('app.serviceRequests.roomKitchen'),
        bathroom: t('app.serviceRequests.roomBathroom'),
        living_room: t('app.serviceRequests.roomLivingRoom'),
        bedroom: t('app.serviceRequests.roomBedroom'),
        balcony: t('app.serviceRequests.roomBalcony'),
        other: t('app.serviceRequests.roomOther'),
    };
    return map[key] ?? key;
}

const isSubmitting = ref(false);

function submit(): void {
    isSubmitting.value = true;
    form.post('/service-requests', {
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
}
</script>

<template>
    <Head :title="t('app.serviceRequests.createPageTitle')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            variant="small"
            :title="t('app.serviceRequests.createHeading')"
            :description="t('app.serviceRequests.createDescription')"
        />

        <form
            class="max-w-2xl space-y-8"
            @submit.prevent="submit"
        >
            <!-- What needs fixing -->
            <fieldset class="space-y-4">
                <legend class="text-sm font-semibold text-foreground">
                    {{ t('app.serviceRequests.whatNeedsFixing') }}
                </legend>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="service_category_id">
                            {{ t('app.serviceRequests.categoryLabel') }}
                            <span aria-hidden="true">*</span>
                        </Label>
                        <Select
                            v-model="form.service_category_id"
                            required
                        >
                            <SelectTrigger
                                id="service_category_id"
                                class="w-full"
                                :aria-label="t('app.serviceRequests.categoryLabel')"
                            >
                                <SelectValue :placeholder="t('app.serviceRequests.selectCategory')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="cat in categories"
                                    :key="cat.id"
                                    :value="String(cat.id)"
                                >
                                    {{ cat.icon }} {{ localizedName(cat) }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.service_category_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="service_subcategory_id">
                            {{ t('app.serviceRequests.subcategoryLabel') }}
                        </Label>
                        <Select
                            v-model="form.service_subcategory_id"
                            :disabled="!form.service_category_id"
                        >
                            <SelectTrigger
                                id="service_subcategory_id"
                                class="w-full"
                                :aria-label="t('app.serviceRequests.subcategoryLabel')"
                                :aria-disabled="!form.service_category_id"
                            >
                                <SelectValue
                                    :placeholder="form.service_category_id
                                        ? t('app.serviceRequests.selectSubcategory')
                                        : t('app.serviceRequests.subcategoryDisabled')"
                                />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="sub in filteredSubcategories"
                                    :key="sub.id"
                                    :value="String(sub.id)"
                                >
                                    {{ localizedName(sub) }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.service_subcategory_id" />
                    </div>
                </div>
            </fieldset>

            <!-- Where is the problem -->
            <fieldset class="space-y-4">
                <legend class="text-sm font-semibold text-foreground">
                    {{ t('app.serviceRequests.whereIsTheProblem') }}
                </legend>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="community_id">
                            {{ t('app.serviceRequests.communityLabel') }}
                            <span aria-hidden="true">*</span>
                        </Label>
                        <Select
                            v-model="form.community_id"
                            required
                        >
                            <SelectTrigger
                                id="community_id"
                                class="w-full"
                                :aria-label="t('app.serviceRequests.communityLabel')"
                            >
                                <SelectValue :placeholder="t('app.serviceRequests.selectCommunity')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="community in communities"
                                    :key="community.id"
                                    :value="String(community.id)"
                                >
                                    {{ community.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.community_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="unit_id">
                            {{ t('app.serviceRequests.unitLabel') }}
                            <span aria-hidden="true">*</span>
                        </Label>
                        <Select
                            v-model="form.unit_id"
                            required
                        >
                            <SelectTrigger
                                id="unit_id"
                                class="w-full"
                                :aria-label="t('app.serviceRequests.unitLabel')"
                            >
                                <SelectValue :placeholder="t('app.serviceRequests.selectUnit')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="unit in filteredUnits"
                                    :key="unit.id"
                                    :value="String(unit.id)"
                                >
                                    {{ unit.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.unit_id" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="room_location">
                        {{ t('app.serviceRequests.roomLabel') }}
                    </Label>
                    <Select v-model="form.room_location">
                        <SelectTrigger
                            id="room_location"
                            class="w-full"
                            :aria-label="t('app.serviceRequests.roomLabel')"
                        >
                            <SelectValue :placeholder="t('app.serviceRequests.selectRoom')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="room in roomOptions"
                                :key="room"
                                :value="room"
                            >
                                {{ roomLabel(room) }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.room_location" />
                </div>
            </fieldset>

            <!-- How urgent -->
            <fieldset class="space-y-4">
                <legend class="text-sm font-semibold text-foreground">
                    {{ t('app.serviceRequests.howUrgent') }}
                </legend>

                <RadioGroup
                    v-model="form.urgency"
                    class="flex flex-col gap-3 sm:flex-row"
                    :aria-label="t('app.serviceRequests.urgencyLabel')"
                >
                    <label
                        class="flex cursor-pointer items-center gap-3 rounded-md border p-4 transition-colors"
                        :class="form.urgency === 'normal' ? 'border-primary bg-primary/5' : 'border-border'"
                    >
                        <RadioGroupItem
                            value="normal"
                            :aria-label="t('app.serviceRequests.urgencyNormal')"
                        />
                        <div>
                            <div class="font-medium">{{ t('app.serviceRequests.urgencyNormal') }}</div>
                            <div class="text-xs text-muted-foreground">{{ t('app.serviceRequests.urgencyNormalHint') }}</div>
                        </div>
                    </label>

                    <label
                        class="flex cursor-pointer items-center gap-3 rounded-md border p-4 transition-colors"
                        :class="form.urgency === 'urgent' ? 'border-destructive bg-destructive/5' : 'border-border'"
                    >
                        <RadioGroupItem
                            value="urgent"
                            :aria-label="t('app.serviceRequests.urgencyUrgent')"
                        />
                        <div>
                            <div class="font-medium">{{ t('app.serviceRequests.urgencyUrgent') }}</div>
                            <div class="text-xs text-muted-foreground">{{ t('app.serviceRequests.urgencyUrgentHint') }}</div>
                        </div>
                    </label>
                </RadioGroup>

                <InputError :message="form.errors.urgency" />
            </fieldset>

            <!-- Describe the issue -->
            <fieldset class="space-y-4">
                <legend class="text-sm font-semibold text-foreground">
                    {{ t('app.serviceRequests.describeTheIssue') }}
                </legend>

                <div class="grid gap-2">
                    <Label for="description">
                        {{ t('app.serviceRequests.descriptionLabel') }}
                        <span aria-hidden="true">*</span>
                    </Label>
                    <Textarea
                        id="description"
                        v-model="form.description"
                        dir="auto"
                        rows="5"
                        maxlength="500"
                        :placeholder="t('app.serviceRequests.descriptionPlaceholder')"
                        aria-required="true"
                        :aria-describedby="'description-count'"
                        required
                    />
                    <div
                        id="description-count"
                        class="text-end text-xs text-muted-foreground"
                        dir="ltr"
                        aria-live="polite"
                    >
                        {{ descriptionLength }}/500
                    </div>
                    <InputError :message="form.errors.description" />
                </div>
            </fieldset>

            <!-- Actions -->
            <div class="flex items-center gap-4 border-t pt-4">
                <Button
                    type="submit"
                    :disabled="form.processing"
                    :aria-busy="form.processing"
                >
                    {{ form.processing
                        ? t('app.serviceRequests.submitting')
                        : t('app.serviceRequests.submitButton') }}
                </Button>

                <Link
                    href="/service-requests"
                    class="text-sm text-muted-foreground hover:underline"
                >
                    {{ t('app.serviceRequests.cancelButton') }}
                </Link>
            </div>
        </form>
    </div>
</template>
