<script setup lang="ts">
import { AlertCircle } from 'lucide-vue-next';
import { computed } from 'vue';
import { useI18n } from '@/composables/useI18n';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';

type Props = {
    errors: string[];
    title?: string;
};

const { t } = useI18n();
const props = defineProps<Props>();

const title = computed(() => props.title ?? t('app.common.somethingWentWrong'));

const uniqueErrors = computed(() => Array.from(new Set(props.errors)));
</script>

<template>
    <Alert variant="destructive">
        <AlertCircle class="size-4" />
        <AlertTitle>{{ title }}</AlertTitle>
        <AlertDescription>
            <ul class="list-inside list-disc text-sm">
                <li v-for="(error, index) in uniqueErrors" :key="index">
                    {{ error }}
                </li>
            </ul>
        </AlertDescription>
    </Alert>
</template>
