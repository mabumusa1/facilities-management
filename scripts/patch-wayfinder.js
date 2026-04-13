/**
 * Patches Wayfinder-generated files that have duplicate const declarations
 * causing TS2395 errors (merged declarations must all be exported or all local).
 *
 * This is a known Wayfinder bug when formVariants:true is used with routes
 * whose names conflict with their form variant names.
 */

import { readFileSync, writeFileSync, existsSync } from 'fs';
import { join, dirname } from 'path';
import { fileURLToPath } from 'url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const root = join(__dirname, '..');

const filesToPatch = [
    'resources/js/actions/App/Http/Controllers/LeaseController.ts',
];

for (const relPath of filesToPatch) {
    const filePath = join(root, relPath);
    if (!existsSync(filePath)) continue;

    const content = readFileSync(filePath, 'utf8');
    if (content.startsWith('// @ts-nocheck')) continue;

    writeFileSync(filePath, '// @ts-nocheck\n' + content, 'utf8');
    console.log(`Patched: ${relPath}`);
}
