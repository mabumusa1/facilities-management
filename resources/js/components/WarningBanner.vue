<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    message: string;
    linkHref?: string;
    linkLabel?: string;
}>();
</script>

<template>
    <div
        role="status"
        aria-live="polite"
        class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-amber-800 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-200"
    >
        <div class="flex items-start gap-3">
            <svg
                class="mt-0.5 size-5 shrink-0"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z" />
                <path d="M12 9v4" />
                <path d="M12 17h.01" />
            </svg>
            <div class="space-y-1">
                <p v-if="title" class="text-sm font-semibold">{{ title }}</p>
                <p class="text-sm">{{ message }}</p>
                <Link
                    v-if="linkHref && linkLabel"
                    :href="linkHref"
                    class="text-sm font-medium underline underline-offset-4 hover:no-underline"
                >
                    {{ linkLabel }}
                </Link>
            </div>
        </div>
    </div>
</template>
