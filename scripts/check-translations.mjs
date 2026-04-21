import fs from 'node:fs';
import path from 'node:path';

const ROOT_DIR = process.cwd();
const RESOURCES_JS_DIR = path.join(ROOT_DIR, 'resources', 'js');
const MESSAGES_FILE = path.join(RESOURCES_JS_DIR, 'i18n', 'messages.ts');
const EN_BASE_FILE = path.join(RESOURCES_JS_DIR, 'locales', 'en.json');
const AR_BASE_FILE = path.join(RESOURCES_JS_DIR, 'locales', 'ar.json');
const EN_FALLBACK_FILE = path.join(RESOURCES_JS_DIR, 'i18n', 'appEnFallback.ts');
const AR_FALLBACK_FILE = path.join(RESOURCES_JS_DIR, 'i18n', 'appArFallback.ts');

const MODE_FULL = process.argv.includes('--full');
const MODE_JSON = process.argv.includes('--json');

function readFile(filePath) {
    return fs.readFileSync(filePath, 'utf8');
}

function readJson(filePath) {
    return JSON.parse(readFile(filePath));
}

function isPlainObject(value) {
    return typeof value === 'object' && value !== null && !Array.isArray(value);
}

function deepMerge(base, overrides) {
    const merged = { ...base };

    for (const [key, value] of Object.entries(overrides)) {
        const baseValue = merged[key];

        if (isPlainObject(baseValue) && isPlainObject(value)) {
            merged[key] = deepMerge(baseValue, value);
            continue;
        }

        merged[key] = value;
    }

    return merged;
}

function extractObjectLiteral(source, variableName) {
    const declarationIndex = source.indexOf(`const ${variableName}`);

    if (declarationIndex === -1) {
        throw new Error(`Could not find declaration for ${variableName}.`);
    }

    const equalsIndex = source.indexOf('=', declarationIndex);

    if (equalsIndex === -1) {
        throw new Error(`Could not find assignment for ${variableName}.`);
    }

    const openBraceIndex = source.indexOf('{', equalsIndex);

    if (openBraceIndex === -1) {
        throw new Error(`Could not find opening brace for ${variableName}.`);
    }

    let depth = 0;
    let inString = false;
    let quoteChar = '';
    let escaping = false;

    for (let index = openBraceIndex; index < source.length; index += 1) {
        const char = source[index];

        if (inString) {
            if (escaping) {
                escaping = false;
                continue;
            }

            if (char === '\\') {
                escaping = true;
                continue;
            }

            if (char === quoteChar) {
                inString = false;
                quoteChar = '';
            }

            continue;
        }

        if (char === '"' || char === '\'' || char === '`') {
            inString = true;
            quoteChar = char;
            continue;
        }

        if (char === '{') {
            depth += 1;
            continue;
        }

        if (char === '}') {
            depth -= 1;

            if (depth === 0) {
                return source.slice(openBraceIndex, index + 1);
            }
        }
    }

    throw new Error(`Could not extract object literal for ${variableName}.`);
}

function evaluateObjectLiteral(literal, label) {
    try {
        return Function(`"use strict"; return (${literal});`)();
    } catch (error) {
        throw new Error(`Failed to evaluate ${label}: ${error.message}`);
    }
}

function walkFiles(startDir, collector) {
    const entries = fs.readdirSync(startDir, { withFileTypes: true });

    for (const entry of entries) {
        const entryPath = path.join(startDir, entry.name);

        if (entry.isDirectory()) {
            walkFiles(entryPath, collector);
            continue;
        }

        if (!entry.isFile()) {
            continue;
        }

        collector(entryPath);
    }
}

function extractUsedTranslationKeys() {
    const keys = new Set();
    const includeExtensions = new Set(['.vue', '.ts', '.js']);

    // Match t('foo.bar') and t("foo.bar") while ignoring dynamic keys.
    const pattern = /\bt\(\s*['"]([A-Za-z0-9_.-]+)['"]/g;

    walkFiles(RESOURCES_JS_DIR, (filePath) => {
        if (filePath.includes(path.join('resources', 'js', 'i18n'))) {
            return;
        }

        if (filePath.includes(path.join('resources', 'js', 'locales'))) {
            return;
        }

        if (!includeExtensions.has(path.extname(filePath))) {
            return;
        }

        const content = readFile(filePath);

        for (const match of content.matchAll(pattern)) {
            keys.add(match[1]);
        }
    });

    return Array.from(keys).sort((a, b) => a.localeCompare(b));
}

function flattenToMap(tree, prefix = '', output = new Map()) {
    if (!isPlainObject(tree)) {
        return output;
    }

    for (const [key, value] of Object.entries(tree)) {
        const fullKey = prefix ? `${prefix}.${key}` : key;

        if (isPlainObject(value)) {
            flattenToMap(value, fullKey, output);
            continue;
        }

        output.set(fullKey, value);
    }

    return output;
}

function getValueByPath(tree, dottedPath) {
    const segments = dottedPath.split('.');
    let current = tree;

    for (const segment of segments) {
        if (!isPlainObject(current)) {
            return undefined;
        }

        if (!(segment in current)) {
            return undefined;
        }

        current = current[segment];
    }

    return current;
}

function getValueForKey(tree, key) {
    if (isPlainObject(tree) && key in tree) {
        return tree[key];
    }

    return getValueByPath(tree, key);
}

function sanitizeList(items) {
    return items.slice().sort((a, b) => a.localeCompare(b));
}

function buildReport() {
    const messagesSource = readFile(MESSAGES_FILE);
    const appEnLiteral = extractObjectLiteral(messagesSource, 'appEn');
    const appArLiteral = extractObjectLiteral(messagesSource, 'appAr');
    const fallbackLiteral = extractObjectLiteral(readFile(EN_FALLBACK_FILE), 'appEnFallback');
    const arFallbackLiteral = extractObjectLiteral(readFile(AR_FALLBACK_FILE), 'appArFallback');

    const appEn = evaluateObjectLiteral(appEnLiteral, 'appEn');
    const appAr = evaluateObjectLiteral(appArLiteral, 'appAr');
    const appEnFallback = evaluateObjectLiteral(fallbackLiteral, 'appEnFallback');
    const appArFallback = evaluateObjectLiteral(arFallbackLiteral, 'appArFallback');

    const enBase = readJson(EN_BASE_FILE);
    const arBase = readJson(AR_BASE_FILE);

    const mergedEn = deepMerge(deepMerge(enBase, appEn), appEnFallback);
    const mergedAr = deepMerge(deepMerge(arBase, appAr), appArFallback);

    const flattenedEn = flattenToMap(mergedEn);
    const flattenedAr = flattenToMap(mergedAr);

    const targetKeys = MODE_FULL
        ? sanitizeList(Array.from(flattenedEn.keys()))
        : extractUsedTranslationKeys();

    const missingInEn = [];
    const missingInAr = [];
    const fallbackOnlyInEn = [];
    const sameAsEnglish = [];

    const fallbackKeySet = new Set(Object.keys(appEnFallback));

    for (const key of targetKeys) {
        const enValue = MODE_FULL ? flattenedEn.get(key) : getValueForKey(mergedEn, key);
        const arValue = MODE_FULL ? flattenedAr.get(key) : getValueForKey(mergedAr, key);

        if (enValue === undefined) {
            missingInEn.push(key);
        }

        if (arValue === undefined) {
            missingInAr.push(key);

            if (fallbackKeySet.has(key)) {
                fallbackOnlyInEn.push(key);
            }
        }

        if (typeof enValue === 'string' && typeof arValue === 'string' && enValue === arValue) {
            sameAsEnglish.push(key);
        }
    }

    return {
        mode: MODE_FULL ? 'full' : 'used-keys',
        totalKeysChecked: targetKeys.length,
        missingInEn: sanitizeList(missingInEn),
        missingInAr: sanitizeList(missingInAr),
        fallbackOnlyInEn: sanitizeList(fallbackOnlyInEn),
        sameAsEnglish: sanitizeList(sameAsEnglish),
    };
}

function printReport(report) {
    if (MODE_JSON) {
        console.log(JSON.stringify(report, null, 2));

        return;
    }

    console.log(`Translation check mode: ${report.mode}`);
    console.log(`Checked keys: ${report.totalKeysChecked}`);
    console.log(`Missing in EN: ${report.missingInEn.length}`);
    console.log(`Missing in AR: ${report.missingInAr.length}`);
    console.log(`Fallback-only EN keys: ${report.fallbackOnlyInEn.length}`);
    console.log(`Same EN/AR values: ${report.sameAsEnglish.length}`);

    const sections = [
        ['Missing in EN', report.missingInEn],
        ['Missing in AR', report.missingInAr],
        ['Fallback-only EN', report.fallbackOnlyInEn],
        ['Same EN/AR', report.sameAsEnglish],
    ];

    for (const [title, list] of sections) {
        if (list.length === 0) {
            continue;
        }

        console.log(`\n${title}:`);

        for (const key of list) {
            console.log(`- ${key}`);
        }
    }
}

function shouldFail(report) {
    return report.missingInEn.length > 0 || report.missingInAr.length > 0;
}

const report = buildReport();
printReport(report);

if (shouldFail(report)) {
    process.exitCode = 1;
}
