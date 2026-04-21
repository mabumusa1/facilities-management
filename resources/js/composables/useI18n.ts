import { computed, readonly, ref } from 'vue';
import { messages } from '@/i18n/messages';

export type SupportedLocale = 'ar' | 'en';

type TranslationParams = Record<string, string | number | boolean | null | undefined>;

type MessageTree = Record<string, unknown>;

const LOCALE_STORAGE_KEY = 'locale';
const DEFAULT_LOCALE: SupportedLocale = 'ar';
const FALLBACK_LOCALE: SupportedLocale = 'en';

function hasWindow(): boolean {
    return typeof window !== 'undefined';
}

function hasDocument(): boolean {
    return typeof document !== 'undefined';
}

function isSupportedLocale(value: string | null | undefined): value is SupportedLocale {
    return value === 'ar' || value === 'en';
}

function resolveInitialLocale(): SupportedLocale {
    if (!hasWindow()) {
        return DEFAULT_LOCALE;
    }

    const savedLocale = localStorage.getItem(LOCALE_STORAGE_KEY);

    if (isSupportedLocale(savedLocale)) {
        return savedLocale;
    }

    const browserLanguage = window.navigator.language?.toLowerCase() ?? '';

    if (browserLanguage.startsWith('ar')) {
        return 'ar';
    }

    if (browserLanguage.startsWith('en')) {
        return 'en';
    }

    return DEFAULT_LOCALE;
}

const locale = ref<SupportedLocale>(resolveInitialLocale());

function resolveFromPath(dictionary: unknown, key: string): unknown {
    if (!key.includes('.')) {
        return undefined;
    }

    const segments = key.split('.');
    let current: unknown = dictionary;

    for (const segment of segments) {
        if (typeof current !== 'object' || current === null || Array.isArray(current)) {
            return undefined;
        }

        current = (current as MessageTree)[segment];
    }

    return current;
}

function resolveTranslationValue(activeLocale: SupportedLocale, key: string): unknown {
    const activeDictionary = messages[activeLocale] as MessageTree;

    if (key in activeDictionary) {
        return activeDictionary[key];
    }

    const nestedActiveValue = resolveFromPath(activeDictionary, key);

    if (nestedActiveValue !== undefined) {
        return nestedActiveValue;
    }

    const fallbackDictionary = messages[FALLBACK_LOCALE] as MessageTree;

    if (key in fallbackDictionary) {
        return fallbackDictionary[key];
    }

    return resolveFromPath(fallbackDictionary, key);
}

function interpolate(template: string, params?: TranslationParams): string {
    if (!params) {
        return template;
    }

    return template.replace(/\{\{\s*([\w.]+)\s*\}\}/g, (_, token: string) => {
        const value = params[token];

        if (value === undefined || value === null) {
            return '';
        }

        return String(value);
    });
}

function syncDocumentLocale(activeLocale: SupportedLocale): void {
    if (!hasDocument()) {
        return;
    }

    document.documentElement.lang = activeLocale;
    document.documentElement.dir = activeLocale === 'ar' ? 'rtl' : 'ltr';
}

function persistLocale(activeLocale: SupportedLocale): void {
    if (!hasWindow()) {
        return;
    }

    localStorage.setItem(LOCALE_STORAGE_KEY, activeLocale);
}

function setLocale(nextLocale: SupportedLocale): void {
    locale.value = nextLocale;
    persistLocale(nextLocale);
    syncDocumentLocale(nextLocale);
}

function toggleLocale(): void {
    setLocale(locale.value === 'ar' ? 'en' : 'ar');
}

function t(key: string, params?: TranslationParams, fallback?: string): string {
    const translatedValue = resolveTranslationValue(locale.value, key);

    if (typeof translatedValue === 'string') {
        return interpolate(translatedValue, params);
    }

    if (typeof translatedValue === 'number' || typeof translatedValue === 'boolean') {
        return String(translatedValue);
    }

    if (fallback) {
        return interpolate(fallback, params);
    }

    return key;
}

export function initializeI18n(): void {
    syncDocumentLocale(locale.value);
}

export function useI18n() {
    return {
        locale: readonly(locale),
        currentLocale: computed(() => locale.value),
        isArabic: computed(() => locale.value === 'ar'),
        setLocale,
        toggleLocale,
        t,
    };
}
