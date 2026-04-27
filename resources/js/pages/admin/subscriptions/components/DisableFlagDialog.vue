<script setup lang="ts">
import { useI18n } from '@/composables/useI18n';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import type { FeatureFlag } from './FeaturesTab.vue';

const { t } = useI18n();

defineProps<{
    flag: FeatureFlag;
    open: boolean;
}>();

const emit = defineEmits<{
    confirm: [flag: FeatureFlag];
    cancel: [];
}>();
</script>

<template>
    <Dialog :open="open" @update:open="(val) => !val && emit('cancel')">
        <DialogContent role="alertdialog" :aria-labelledby="'dialog-title-' + flag.key" :aria-describedby="'dialog-desc-' + flag.key">
            <DialogHeader>
                <DialogTitle :id="'dialog-title-' + flag.key">
                    {{ t('app.admin.featureFlags.disableHeading', { feature: flag.label_en }, 'Disable ' + flag.label_en + '?') }}
                </DialogTitle>
                <DialogDescription :id="'dialog-desc-' + flag.key">
                    <div class="mt-2 rounded-md border border-amber-200 bg-amber-50 p-3 dark:border-amber-800 dark:bg-amber-950">
                        <p class="text-amber-800 dark:text-amber-200 font-medium text-sm">
                            {{ t('app.admin.featureFlags.disableImpactLabel') }}
                        </p>
                        <p class="mt-1 text-amber-700 dark:text-amber-300 text-sm" dir="ltr">
                            {{ t('app.admin.featureFlags.disableImpactBody', { feature: flag.label_en }) }}
                        </p>
                        <p class="mt-1 text-amber-700 dark:text-amber-300 text-sm" dir="rtl" lang="ar">
                            {{ t('app.admin.featureFlags.disableImpactBody', { feature: flag.label_ar }) }}
                        </p>
                    </div>
                    <p class="mt-3 text-muted-foreground text-sm">
                        {{ t('app.admin.featureFlags.disableReversible') }}
                    </p>
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" @click="emit('cancel')">
                    {{ t('app.actions.cancel') }}
                </Button>
                <Button variant="destructive" @click="emit('confirm', flag)">
                    {{ t('app.admin.featureFlags.disableCta') }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
