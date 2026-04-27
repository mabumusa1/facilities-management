<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Skeleton } from '@/components/ui/skeleton';
import { Switch } from '@/components/ui/switch';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useI18n } from '@/composables/useI18n';
import { toggle as toggleRoute } from '@/routes/admin/subscriptions/features';
import { toast } from 'vue-sonner';
import EnableFlagDialog from './EnableFlagPopover.vue';
import DisableFlagDialog from './DisableFlagDialog.vue';

export type FeatureFlag = {
    key: string;
    label_en: string;
    label_ar: string;
    enabled: boolean;
    in_tier: boolean;
    plan_name: string;
};

const { t, isArabic } = useI18n();

const props = defineProps<{
    features: FeatureFlag[] | undefined;
    tenantId: number;
}>();

const pendingFlag = ref<FeatureFlag | null>(null);
const pendingAction = ref<'enable' | 'disable' | null>(null);
const optimisticStates = ref<Record<string, boolean>>({});
const errors = ref<Record<string, string>>({});

function flagEnabled(flag: FeatureFlag): boolean {
    if (flag.key in optimisticStates.value) {
        return optimisticStates.value[flag.key];
    }
    return flag.enabled;
}

function onSwitchChange(flag: FeatureFlag, checked: boolean) {
    errors.value = { ...errors.value, [flag.key]: '' };

    if (checked) {
        pendingFlag.value = flag;
        pendingAction.value = 'enable';
    } else {
        pendingFlag.value = flag;
        pendingAction.value = 'disable';
    }
}

function handleConfirm(flag: FeatureFlag) {
    const newValue = pendingAction.value === 'enable';
    const previousValue = flagEnabled(flag);

    optimisticStates.value = { ...optimisticStates.value, [flag.key]: newValue };
    pendingFlag.value = null;
    pendingAction.value = null;

    router.optimistic((pageProps) => pageProps).patch(
        toggleRoute.url({ tenant: props.tenantId, flagKey: flag.key }),
        { enabled: newValue },
        {
            onSuccess: () => {
                const actionText = newValue ? 'enabled' : 'disabled';
                const name = isArabic.value ? flag.label_ar : flag.label_en;
                toast.success(
                    t('app.admin.featureFlags.toastEnabled', { feature: name }, `${name} ${actionText}`),
                );
                delete optimisticStates.value[flag.key];
            },
            onError: () => {
                optimisticStates.value = { ...optimisticStates.value, [flag.key]: previousValue };
                errors.value = {
                    ...errors.value,
                    [flag.key]: t('app.admin.featureFlags.toggleError'),
                };
            },
        },
    );
}

function handleCancel() {
    pendingFlag.value = null;
    pendingAction.value = null;
}

function tierTooltip(flag: FeatureFlag): string {
    if (flag.in_tier) {
        return t('app.admin.featureFlags.tierIncluded', { plan: flag.plan_name });
    }
    return t('app.admin.featureFlags.tierNotIncluded', { plan: flag.plan_name });
}
</script>

<template>
    <div class="flex flex-col gap-4">
        <p class="text-muted-foreground text-sm">
            {{ t('app.admin.featureFlags.description') }}
        </p>

        <!-- Loading skeleton -->
        <div v-if="features === undefined" class="rounded-lg border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>{{ t('app.admin.featureFlags.colFeature') }}</TableHead>
                        <TableHead>{{ t('app.admin.featureFlags.colStatus') }}</TableHead>
                        <TableHead>{{ t('app.admin.featureFlags.colInTier') }}</TableHead>
                        <TableHead class="sr-only">{{ t('app.admin.featureFlags.colToggle') }}</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="i in 3" :key="i">
                        <TableCell>
                            <Skeleton class="h-4 w-32" />
                            <Skeleton class="mt-1 h-3 w-24" />
                        </TableCell>
                        <TableCell>
                            <Skeleton class="h-5 w-16" />
                        </TableCell>
                        <TableCell>
                            <Skeleton class="h-5 w-20" />
                        </TableCell>
                        <TableCell>
                            <Skeleton class="h-5 w-8" />
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <!-- Empty state -->
        <div v-else-if="features.length === 0" class="rounded-lg border py-12 text-center">
            <p class="text-muted-foreground text-sm font-medium">
                {{ t('app.admin.featureFlags.emptyHeading') }}
            </p>
            <p class="text-muted-foreground mt-1 text-sm" dir="rtl" lang="ar">
                {{ t('app.admin.featureFlags.emptyHeadingAr') }}
            </p>
            <p class="text-muted-foreground mt-3 text-xs">
                {{ t('app.admin.featureFlags.emptyBody') }}
            </p>
        </div>

        <!-- Features table -->
        <div v-else class="rounded-lg border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>{{ t('app.admin.featureFlags.colFeature') }}</TableHead>
                        <TableHead>{{ t('app.admin.featureFlags.colStatus') }}</TableHead>
                        <TableHead>{{ t('app.admin.featureFlags.colInTier') }}</TableHead>
                        <TableHead class="sr-only">{{ t('app.admin.featureFlags.colToggle') }}</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="flag in features" :key="flag.key">
                        <TableCell>
                            <div>
                                <span class="text-sm font-medium" dir="ltr">{{ flag.label_en }}</span>
                                <br>
                                <span class="text-muted-foreground text-xs" dir="rtl" lang="ar">{{ flag.label_ar }}</span>
                            </div>
                            <p
                                v-if="errors[flag.key]"
                                class="mt-1 text-xs text-destructive"
                                aria-live="polite"
                            >
                                {{ errors[flag.key] }}
                            </p>
                        </TableCell>
                        <TableCell>
                            <Badge :variant="flagEnabled(flag) ? 'default' : 'secondary'">
                                {{ flagEnabled(flag) ? t('app.admin.featureFlags.statusEnabled') : t('app.admin.featureFlags.statusDisabled') }}
                            </Badge>
                        </TableCell>
                        <TableCell>
                            <Badge variant="outline">
                                {{ flag.in_tier ? '\u2713' : '\u2014' }}
                                {{ flag.plan_name }}
                            </Badge>
                        </TableCell>
                        <TableCell class="text-end">
                            <Switch
                                :model-value="flagEnabled(flag)"
                                :aria-label="flagEnabled(flag)
                                    ? t('app.admin.featureFlags.ariaDisable', { feature: flag.label_en + ' / ' + flag.label_ar })
                                    : t('app.admin.featureFlags.ariaEnable', { feature: flag.label_en + ' / ' + flag.label_ar })"
                                :disabled="pendingFlag !== null"
                                @update:model-value="(val) => onSwitchChange(flag, val)"
                            />
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <!-- Enable confirm dialog -->
        <EnableFlagDialog
            v-if="pendingFlag && pendingAction === 'enable'"
            :flag="pendingFlag"
            :open="true"
            @confirm="handleConfirm"
            @cancel="handleCancel"
        />

        <!-- Disable confirm dialog -->
        <DisableFlagDialog
            v-if="pendingFlag && pendingAction === 'disable'"
            :flag="pendingFlag"
            :open="true"
            @confirm="handleConfirm"
            @cancel="handleCancel"
        />
    </div>
</template>
