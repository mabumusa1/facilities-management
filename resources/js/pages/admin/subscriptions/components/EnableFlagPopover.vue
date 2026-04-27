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
        <DialogContent role="dialog" :aria-labelledby="'enable-title-' + flag.key">
            <DialogHeader>
                <DialogTitle :id="'enable-title-' + flag.key">
                    {{ t('app.admin.featureFlags.enableHeading', { feature: flag.label_en }, 'Enable ' + flag.label_en + '?') }}
                </DialogTitle>
                <DialogDescription>
                    {{ t('app.admin.featureFlags.enableBody') }}
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" @click="emit('cancel')">
                    {{ t('app.actions.cancel') }}
                </Button>
                <Button @click="emit('confirm', flag)">
                    {{ t('app.admin.featureFlags.enableCta') }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
