<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

defineProps<{
    title: string;
    description?: string;
    createHref?: string;
    createLabel?: string;
}>();
</script>

<template>
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">{{ title }}</h2>
            <p v-if="description" class="text-muted-foreground text-sm">
                {{ description }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <slot name="actions" />
            <Button v-if="createHref" as-child>
                <Link :href="createHref">
                    <Plus class="mr-2 h-4 w-4" />
                    {{ createLabel || 'Create' }}
                </Link>
            </Button>
        </div>
    </div>
</template>
