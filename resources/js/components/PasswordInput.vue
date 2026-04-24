<script setup lang="ts">
import { Eye, EyeOff } from 'lucide-vue-next';
import { ref, useTemplateRef } from 'vue';
import type { HTMLAttributes } from 'vue';
import { useI18n } from '@/composables/useI18n';
import { Input } from '@/components/ui/input';
import { cn } from '@/lib/utils';

defineOptions({ inheritAttrs: false });

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();

// Gap 3: i18n for aria-labels so Arabic locale gets translated screen-reader text
const { t } = useI18n();

const showPassword = ref(false);
const inputRef = useTemplateRef('inputRef');

defineExpose({
    $el: inputRef,
    focus: () => inputRef.value?.$el?.focus(),
});
</script>

<template>
    <div class="relative">
        <!-- Gap 5: pe-10 (padding-inline-end) replaces pr-10 for RTL correctness -->
        <Input
            ref="inputRef"
            :type="showPassword ? 'text' : 'password'"
            :class="cn('pe-10', props.class)"
            v-bind="$attrs"
        />
        <!-- Gap 5: end-0 replaces right-0; Gap 10: tabindex 0 for keyboard accessibility -->
        <button
            type="button"
            @click="showPassword = !showPassword"
            :class="
                cn(
                    'absolute inset-y-0 end-0 flex items-center rounded-r-md px-3 text-muted-foreground hover:text-foreground focus-visible:ring-[3px] focus-visible:ring-ring focus-visible:outline-none',
                )
            "
            :aria-label="showPassword ? t('app.auth.login.hidePassword') : t('app.auth.login.showPassword')"
            :tabindex="0"
        >
            <EyeOff v-if="showPassword" class="size-4" />
            <Eye v-else class="size-4" />
        </button>
    </div>
</template>
