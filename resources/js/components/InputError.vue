<script setup lang="ts">
defineProps<{
    message?: string;
}>();
</script>

<template>
    <!-- Gap 9: aria-live="polite" ensures screen readers announce validation errors
         when Inertia updates the DOM in-place (no page reload). -->
    <div v-show="message" aria-live="polite">
        <p class="text-sm text-red-600 dark:text-red-500">
            {{ message }}
        </p>
    </div>
</template>
