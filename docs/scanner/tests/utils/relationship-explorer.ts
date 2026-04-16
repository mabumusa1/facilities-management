import { Page } from '@playwright/test';
import { CapturedRequest, API_DOMAIN } from './types';

export interface DropdownRelationship {
  triggerDropdown: {
    selector: string;
    name: string;
    selectedValue: string;
    selectedLabel: string;
  };
  triggeredApiCalls: Array<{
    method: string;
    url: string;
    queryParams: Record<string, string>;
  }>;
  affectedDropdowns: Array<{
    selector: string;
    name: string;
    optionsBefore: string[];
    optionsAfter: string[];
  }>;
}

export interface HardcodedValue {
  source: string; // JS file or inline script
  type: 'enum' | 'constant' | 'array' | 'object';
  name: string;
  value: unknown;
  context?: string; // surrounding code for context
}

export interface PageRelationships {
  dropdownRelationships: DropdownRelationship[];
  hardcodedValues: HardcodedValue[];
  staticOptions: Record<string, string[]>; // dropdown name -> static options
}

/**
 * Find all interactive dropdowns on the page (React Select, MUI Select, native select, etc.)
 */
async function findDropdowns(page: Page): Promise<Array<{ selector: string; name: string; element: any }>> {
  const dropdowns: Array<{ selector: string; name: string; element: any }> = [];
  const seenNames = new Set<string>();

  // Strategy 1: Native select elements
  const nativeSelects = await page.locator('select').all();
  for (let i = 0; i < nativeSelects.length; i++) {
    const el = nativeSelects[i];
    const isVisible = await el.isVisible().catch(() => false);
    if (!isVisible) continue;

    const name = await el.getAttribute('name') || await el.getAttribute('id') || `native-select-${i}`;
    if (seenNames.has(name)) continue;
    seenNames.add(name);

    dropdowns.push({
      selector: `select[name="${name}"], select#${name}`,
      name,
      element: el,
    });
  }

  // Strategy 2: MUI Select components - look for the clickable div, not the hidden input
  const muiSelectDivs = await page.locator('.MuiSelect-select[role="combobox"]').all();
  for (let i = 0; i < muiSelectDivs.length; i++) {
    const el = muiSelectDivs[i];
    const isVisible = await el.isVisible().catch(() => false);
    if (!isVisible) continue;

    const name = await el.getAttribute('id') ||
                 await el.getAttribute('aria-labelledby') ||
                 `mui-select-${i}`;

    if (seenNames.has(name)) continue;
    seenNames.add(name);

    dropdowns.push({
      selector: `.MuiSelect-select[id="${name}"]`,
      name,
      element: el,
    });
  }

  // Strategy 3: React Select components
  const reactSelects = await page.locator('[class*="select__control"]').all();
  for (let i = 0; i < reactSelects.length; i++) {
    const el = reactSelects[i];
    const isVisible = await el.isVisible().catch(() => false);
    if (!isVisible) continue;

    const name = await el.getAttribute('id') ||
                 await el.getAttribute('aria-label') ||
                 `react-select-${i}`;

    if (seenNames.has(name)) continue;
    seenNames.add(name);

    dropdowns.push({
      selector: `[class*="select__control"]`,
      name,
      element: el,
    });
  }

  // Strategy 4: General combobox role (skip if already found)
  const comboboxes = await page.locator('[role="combobox"]:not(.MuiSelect-select)').all();
  for (let i = 0; i < comboboxes.length; i++) {
    const el = comboboxes[i];
    const isVisible = await el.isVisible().catch(() => false);
    if (!isVisible) continue;

    const name = await el.getAttribute('id') ||
                 await el.getAttribute('aria-label') ||
                 `combobox-${i}`;

    if (seenNames.has(name)) continue;
    seenNames.add(name);

    dropdowns.push({
      selector: `[role="combobox"][id="${name}"]`,
      name,
      element: el,
    });
  }

  return dropdowns;
}

/**
 * Close any open MUI menus/dropdowns
 */
async function closeOpenDropdowns(page: Page): Promise<void> {
  try {
    // Click on MUI backdrop if present (this closes the menu)
    const backdrop = page.locator('.MuiBackdrop-root, .MuiModal-backdrop');
    if (await backdrop.isVisible({ timeout: 200 }).catch(() => false)) {
      await backdrop.click({ force: true }).catch(() => {});
      await page.waitForTimeout(200);
    }

    // Press Escape multiple times to close any open menus
    await page.keyboard.press('Escape');
    await page.waitForTimeout(150);
    await page.keyboard.press('Escape');
    await page.waitForTimeout(150);

    // Click on body to close any remaining popups
    await page.locator('body').click({ position: { x: 10, y: 10 }, force: true }).catch(() => {});
    await page.waitForTimeout(200);

    // Wait for any MUI menu to be hidden
    await page.waitForSelector('.MuiMenu-root', { state: 'hidden', timeout: 500 }).catch(() => {});
  } catch {
    // Ignore errors
  }
}

/**
 * Get options from a dropdown (works for both native and custom dropdowns)
 */
async function getDropdownOptions(page: Page, dropdown: { selector: string; name: string; element: any }): Promise<string[]> {
  const options: string[] = [];

  try {
    // First close any open dropdowns
    await closeOpenDropdowns(page);

    // For native select
    const tagName = await dropdown.element.evaluate((el: Element) => el.tagName.toLowerCase());
    if (tagName === 'select') {
      const opts = await dropdown.element.locator('option').allTextContents();
      return opts.map(o => o.trim()).filter(Boolean);
    }

    // Skip hidden inputs (MUI uses hidden inputs for value storage)
    const inputType = await dropdown.element.getAttribute('type');
    if (inputType === 'hidden' || await dropdown.element.getAttribute('aria-hidden') === 'true') {
      return [];
    }

    // Check if element is visible
    const isVisible = await dropdown.element.isVisible().catch(() => false);
    if (!isVisible) {
      return [];
    }

    // For custom dropdowns - click to open
    await dropdown.element.click({ timeout: 3000, force: true });
    await page.waitForTimeout(500);

    // Try various option selectors
    const optionSelectors = [
      '[role="option"]',
      '[role="listbox"] [role="option"]',
      '.MuiMenu-list li',
      '.MuiMenuItem-root',
      '[class*="option"]',
      '[class*="menu"] li',
      '[class*="listbox"] li',
      'ul[role="listbox"] li',
      'li[data-value]',
    ];

    for (const optSelector of optionSelectors) {
      const optElements = await page.locator(optSelector).all();
      if (optElements.length > 0) {
        for (const opt of optElements) {
          const isOptVisible = await opt.isVisible().catch(() => false);
          if (isOptVisible) {
            const text = await opt.textContent().catch(() => '');
            const value = await opt.getAttribute('data-value').catch(() => '');
            if (text?.trim()) {
              options.push(value ? `${value}: ${text.trim()}` : text.trim());
            }
          }
        }
        if (options.length > 0) break;
      }
    }

    // Close dropdown
    await closeOpenDropdowns(page);
  } catch (e) {
    console.log(`Could not get options for ${dropdown.name}: ${e}`);
    await closeOpenDropdowns(page);
  }

  return options;
}

/**
 * Select an option from a dropdown and track API calls
 */
async function selectOptionAndTrackCalls(
  page: Page,
  dropdown: { selector: string; name: string; element: any },
  optionIndex: number,
  capturedCalls: CapturedRequest[]
): Promise<{ selectedValue: string; selectedLabel: string; newCalls: CapturedRequest[] }> {
  const callsBefore = capturedCalls.length;
  let selectedLabel = '';
  let selectedValue = '';

  try {
    // Close any open dropdowns first
    await closeOpenDropdowns(page);
    await page.waitForTimeout(200);

    const tagName = await dropdown.element.evaluate((el: Element) => el.tagName.toLowerCase());

    if (tagName === 'select') {
      // Native select
      const options = await dropdown.element.locator('option').all();
      if (optionIndex < options.length) {
        selectedValue = await options[optionIndex].getAttribute('value') || '';
        selectedLabel = await options[optionIndex].textContent() || '';
        await dropdown.element.selectOption({ index: optionIndex });
      }
    } else {
      // Custom dropdown - open it
      await dropdown.element.click({ timeout: 3000, force: true });
      await page.waitForTimeout(500);

      // Find visible options
      const optionSelectors = [
        '.MuiMenuItem-root',
        '[role="option"]',
        '[role="listbox"] li',
        '[class*="option"]',
        'li[data-value]',
      ];

      for (const optSelector of optionSelectors) {
        const optElements = await page.locator(optSelector).all();
        const visibleOptions = [];

        for (const opt of optElements) {
          const isVisible = await opt.isVisible().catch(() => false);
          if (isVisible) {
            visibleOptions.push(opt);
          }
        }

        if (visibleOptions.length > optionIndex) {
          const targetOption = visibleOptions[optionIndex];
          selectedLabel = await targetOption.textContent() || '';
          selectedValue = await targetOption.getAttribute('data-value') || selectedLabel;

          // Click the option
          await targetOption.click({ timeout: 2000 });
          break;
        }
      }
    }

    // Wait for any triggered API calls
    await page.waitForTimeout(1500);

    // Make sure dropdown is closed
    await closeOpenDropdowns(page);

  } catch (e) {
    console.log(`Could not select option ${optionIndex} for ${dropdown.name}: ${e}`);
    await closeOpenDropdowns(page);
  }

  const newCalls = capturedCalls.slice(callsBefore);
  return { selectedValue, selectedLabel: selectedLabel.trim(), newCalls };
}

/**
 * Parse URL query parameters
 */
function parseQueryParams(url: string): Record<string, string> {
  try {
    const urlObj = new URL(url);
    const params: Record<string, string> = {};
    urlObj.searchParams.forEach((value, key) => {
      params[key] = value;
    });
    return params;
  } catch {
    return {};
  }
}

/**
 * Explore dropdown relationships by selecting options and tracking API calls
 */
export async function exploreDropdownRelationships(
  page: Page,
  capturedRequests: CapturedRequest[]
): Promise<DropdownRelationship[]> {
  const relationships: DropdownRelationship[] = [];
  const dropdowns = await findDropdowns(page);

  console.log(`Found ${dropdowns.length} dropdowns to explore`);

  for (const dropdown of dropdowns) {
    try {
      // Get initial options for all dropdowns
      const initialOptionsMap: Record<string, string[]> = {};
      for (const dd of dropdowns) {
        initialOptionsMap[dd.name] = await getDropdownOptions(page, dd);
      }

      // Get options for this dropdown
      const options = await getDropdownOptions(page, dropdown);
      if (options.length === 0) continue;

      console.log(`Exploring dropdown: ${dropdown.name} (${options.length} options)`);

      // Try selecting the first few options (limit to avoid too much exploration)
      const maxOptionsToTry = Math.min(3, options.length);
      for (let i = 0; i < maxOptionsToTry; i++) {
        const callsBefore = capturedRequests.length;

        const { selectedValue, selectedLabel, newCalls } = await selectOptionAndTrackCalls(
          page,
          dropdown,
          i,
          capturedRequests
        );

        // Check if any API calls were triggered
        const apiCalls = newCalls.filter(c => c.url.includes(API_DOMAIN));

        if (apiCalls.length > 0) {
          // Check if other dropdowns' options changed
          const affectedDropdowns: DropdownRelationship['affectedDropdowns'] = [];

          for (const otherDd of dropdowns) {
            if (otherDd.name === dropdown.name) continue;

            const newOptions = await getDropdownOptions(page, otherDd);
            const oldOptions = initialOptionsMap[otherDd.name] || [];

            if (JSON.stringify(newOptions) !== JSON.stringify(oldOptions)) {
              affectedDropdowns.push({
                selector: otherDd.selector,
                name: otherDd.name,
                optionsBefore: oldOptions,
                optionsAfter: newOptions,
              });
            }
          }

          relationships.push({
            triggerDropdown: {
              selector: dropdown.selector,
              name: dropdown.name,
              selectedValue,
              selectedLabel,
            },
            triggeredApiCalls: apiCalls.map(c => ({
              method: c.method,
              url: c.url,
              queryParams: parseQueryParams(c.url),
            })),
            affectedDropdowns,
          });
        }
      }
    } catch (e) {
      console.log(`Error exploring dropdown ${dropdown.name}: ${e}`);
    }
  }

  return relationships;
}

/**
 * Extract hardcoded values from JavaScript bundles
 */
export async function extractHardcodedValues(page: Page): Promise<HardcodedValue[]> {
  const hardcodedValues: HardcodedValue[] = [];

  // Common patterns for hardcoded values in React/JS apps
  const patterns = [
    // Enums: { PENDING: 'pending', ACTIVE: 'active' }
    /(?:const|let|var)\s+(\w+)\s*=\s*\{([^}]+(?:['"][A-Z_]+['"]\s*:\s*['"][^'"]+['"],?\s*)+)\}/g,
    // Status arrays: ['pending', 'active', 'completed']
    /(?:const|let|var)\s+(\w+(?:Status|Type|State|Option)s?)\s*=\s*\[([^\]]+)\]/gi,
    // Object maps: { 1: 'Active', 2: 'Inactive' }
    /(?:const|let|var)\s+(\w+(?:Map|Lookup|Dict))\s*=\s*\{([^}]+)\}/gi,
  ];

  try {
    // Get all script sources
    const scripts = await page.evaluate(() => {
      const scriptTags = Array.from(document.querySelectorAll('script[src]'));
      return scriptTags.map(s => s.getAttribute('src')).filter(Boolean);
    });

    // Also get inline scripts
    const inlineScripts = await page.evaluate(() => {
      const scriptTags = Array.from(document.querySelectorAll('script:not([src])'));
      return scriptTags.map(s => s.textContent).filter(Boolean);
    });

    // Process inline scripts
    for (const script of inlineScripts) {
      if (!script) continue;
      for (const pattern of patterns) {
        let match;
        while ((match = pattern.exec(script)) !== null) {
          try {
            hardcodedValues.push({
              source: 'inline-script',
              type: 'constant',
              name: match[1],
              value: match[2].trim(),
              context: match[0].slice(0, 200),
            });
          } catch {
            // Skip if can't parse
          }
        }
      }
    }

    // Look for common hardcoded patterns in window/global scope
    const globalValues = await page.evaluate(() => {
      const values: Array<{ name: string; value: unknown; type: string }> = [];

      // Check for common global config patterns
      const checkObjects = [
        'window.__INITIAL_STATE__',
        'window.__CONFIG__',
        'window.APP_CONFIG',
        'window.CONSTANTS',
        'window.__NUXT__',
        'window.__NEXT_DATA__',
      ];

      for (const objPath of checkObjects) {
        try {
          const obj = eval(objPath);
          if (obj) {
            values.push({
              name: objPath,
              value: JSON.stringify(obj).slice(0, 5000), // Limit size
              type: 'object',
            });
          }
        } catch {
          // Object doesn't exist
        }
      }

      return values;
    });

    for (const gv of globalValues) {
      hardcodedValues.push({
        source: 'window-global',
        type: gv.type as 'object',
        name: gv.name,
        value: gv.value,
      });
    }

  } catch (e) {
    console.log(`Error extracting hardcoded values: ${e}`);
  }

  return hardcodedValues;
}

/**
 * Extract static dropdown options that are hardcoded in the page
 */
export async function extractStaticDropdownOptions(page: Page): Promise<Record<string, string[]>> {
  const staticOptions: Record<string, string[]> = {};

  try {
    // Look for data embedded in script tags (common in SSR apps)
    const embeddedData = await page.evaluate(() => {
      const results: Record<string, string[]> = {};

      // Check for select elements with hardcoded options
      document.querySelectorAll('select').forEach((select, idx) => {
        const name = select.name || select.id || `select-${idx}`;
        const options = Array.from(select.options).map(o => ({
          value: o.value,
          label: o.textContent?.trim() || o.value,
        }));
        if (options.length > 0) {
          results[name] = options.map(o => `${o.value}: ${o.label}`);
        }
      });

      // Check for datalist elements
      document.querySelectorAll('datalist').forEach((datalist, idx) => {
        const id = datalist.id || `datalist-${idx}`;
        const options = Array.from(datalist.querySelectorAll('option')).map(o => o.value);
        if (options.length > 0) {
          results[id] = options;
        }
      });

      return results;
    });

    Object.assign(staticOptions, embeddedData);

  } catch (e) {
    console.log(`Error extracting static options: ${e}`);
  }

  return staticOptions;
}

/**
 * Main function to explore all relationships on a page
 */
export async function explorePageRelationships(
  page: Page,
  capturedRequests: CapturedRequest[]
): Promise<PageRelationships> {
  console.log('Exploring page relationships...');

  const [dropdownRelationships, hardcodedValues, staticOptions] = await Promise.all([
    exploreDropdownRelationships(page, capturedRequests),
    extractHardcodedValues(page),
    extractStaticDropdownOptions(page),
  ]);

  return {
    dropdownRelationships,
    hardcodedValues,
    staticOptions,
  };
}
