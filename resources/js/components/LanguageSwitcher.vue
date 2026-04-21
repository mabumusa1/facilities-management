<script setup lang="ts">
import { computed } from 'vue';
import { Languages } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useI18n } from '@/composables/useI18n';

const { currentLocale, setLocale, t } = useI18n();

const currentLocaleLabel = computed(() =>
    currentLocale.value === 'ar'
        ? t('app.language.arabic')
        : t('app.language.english'),
);
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="outline" size="sm" class="h-9 gap-2">
                <Languages class="size-4" />
                <span class="hidden sm:inline">
                    {{ t('app.language.current') }}: {{ currentLocaleLabel }}
                </span>
                <span class="sm:hidden">{{ currentLocale.toUpperCase() }}</span>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" class="w-44">
            <DropdownMenuLabel>{{ t('app.language.current') }}</DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuItem
                class="cursor-pointer"
                :disabled="currentLocale === 'ar'"
                @click="setLocale('ar')"
            >
                {{ t('app.language.arabic') }}
            </DropdownMenuItem>
            <DropdownMenuItem
                class="cursor-pointer"
                :disabled="currentLocale === 'en'"
                @click="setLocale('en')"
            >
                {{ t('app.language.english') }}
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
