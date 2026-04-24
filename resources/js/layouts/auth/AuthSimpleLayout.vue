<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
    /**
     * Optional company logo URL (Gap 2 — #235).
     * Populated once story #225 (Company Profile) ships.
     * Falls back to AppLogoIcon when null.
     */
    companyLogoUrl?: string | null;
}>();
</script>

<template>
    <div
        class="relative flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 md:p-10"
    >
        <!-- Gap 6: logical property end-4 replaces right-4 for RTL correctness -->
        <div class="absolute top-4 end-4">
            <LanguageSwitcher />
        </div>

        <div class="w-full max-w-sm">
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-4">
                    <Link
                        :href="home()"
                        class="flex flex-col items-center gap-2 font-medium"
                    >
                        <div
                            class="mb-1 flex h-9 w-9 items-center justify-center rounded-md"
                        >
                            <!-- Gap 2: render company logo when provided, else AppLogoIcon -->
                            <img
                                v-if="companyLogoUrl"
                                :src="companyLogoUrl"
                                :alt="title"
                                class="h-9 w-auto object-contain"
                            />
                            <AppLogoIcon
                                v-else
                                class="size-9 fill-current text-[var(--foreground)] dark:text-white"
                            />
                        </div>
                        <span class="sr-only">{{ title }}</span>
                    </Link>
                    <div class="space-y-2 text-center">
                        <h1 class="text-xl font-medium leading-relaxed">{{ title }}</h1>
                        <p class="text-center text-sm leading-relaxed text-muted-foreground">
                            {{ description }}
                        </p>
                    </div>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
