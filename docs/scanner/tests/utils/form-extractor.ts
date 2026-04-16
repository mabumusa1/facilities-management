import { Page } from '@playwright/test';
import { FormFieldData, PageFormData } from './types';

/**
 * Extract all form field data from the current page.
 * This captures input values, select options, checkbox/radio states, and data attributes.
 */
export async function extractFormData(page: Page): Promise<PageFormData> {
  return await page.evaluate(() => {
    const result: PageFormData = { forms: [], orphanFields: [] };

    function mapFieldType(type: string): FormFieldData['type'] {
      const typeMap: Record<string, FormFieldData['type']> = {
        text: 'text',
        email: 'text',
        tel: 'text',
        url: 'text',
        search: 'text',
        password: 'text',
        number: 'number',
        date: 'date',
        datetime: 'date',
        'datetime-local': 'date',
        time: 'date',
        month: 'date',
        week: 'date',
        select: 'select',
        'select-one': 'select',
        'select-multiple': 'select',
        checkbox: 'checkbox',
        radio: 'radio',
        textarea: 'textarea',
        hidden: 'hidden',
      };
      return typeMap[type] || 'other';
    }

    function extractDataAttributes(el: HTMLElement): Record<string, string> | undefined {
      const attrs: Record<string, string> = {};
      Array.from(el.attributes).forEach((attr) => {
        if (attr.name.startsWith('data-')) {
          attrs[attr.name] = attr.value;
        }
      });
      return Object.keys(attrs).length > 0 ? attrs : undefined;
    }

    function extractFieldData(el: HTMLElement): FormFieldData | null {
      const tagName = el.tagName.toLowerCase();
      let type = (el as HTMLInputElement).type?.toLowerCase() || tagName;

      // Handle select elements
      if (tagName === 'select') {
        type = 'select';
      } else if (tagName === 'textarea') {
        type = 'textarea';
      }

      const field: FormFieldData = {
        type: mapFieldType(type),
        name: el.getAttribute('name') || '',
        id: el.getAttribute('id') || undefined,
        value: (el as HTMLInputElement).value || undefined,
        placeholder: el.getAttribute('placeholder') || undefined,
        required: el.hasAttribute('required'),
        disabled: el.hasAttribute('disabled'),
        attributes: extractDataAttributes(el),
      };

      // Get associated label
      const id = el.getAttribute('id');
      if (id) {
        const label = document.querySelector(`label[for="${id}"]`);
        if (label) {
          field.label = label.textContent?.trim();
        }
      }

      // If no label found by for attribute, check parent label
      if (!field.label) {
        const parentLabel = el.closest('label');
        if (parentLabel) {
          // Get text content excluding the input element's text
          const clone = parentLabel.cloneNode(true) as HTMLElement;
          clone.querySelectorAll('input, select, textarea').forEach((input) => input.remove());
          field.label = clone.textContent?.trim();
        }
      }

      // Extract options for select elements
      if (tagName === 'select') {
        const select = el as HTMLSelectElement;
        field.options = Array.from(select.options).map((opt) => ({
          value: opt.value,
          label: opt.textContent?.trim() || opt.value,
          selected: opt.selected,
        }));
      }

      // Handle checkboxes and radios
      if (type === 'checkbox' || type === 'radio') {
        field.checked = (el as HTMLInputElement).checked;
        // Get the value attribute for radios
        field.value = el.getAttribute('value') || undefined;
      }

      return field;
    }

    // Process all form tags
    const processedElements = new Set<HTMLElement>();

    document.querySelectorAll('form').forEach((form) => {
      const formData = {
        id: form.id || undefined,
        name: form.getAttribute('name') || undefined,
        action: form.action || undefined,
        fields: [] as FormFieldData[],
      };

      form.querySelectorAll('input, select, textarea').forEach((el) => {
        const field = extractFieldData(el as HTMLElement);
        if (field) {
          formData.fields.push(field);
          processedElements.add(el as HTMLElement);
        }
      });

      result.forms.push(formData);
    });

    // Find orphan fields (inputs not in a form)
    document.querySelectorAll('input, select, textarea').forEach((el) => {
      if (!processedElements.has(el as HTMLElement)) {
        const field = extractFieldData(el as HTMLElement);
        if (field) {
          result.orphanFields.push(field);
        }
      }
    });

    return result;
  });
}

/**
 * Extract dropdown options by clicking to open custom dropdowns (React Select, MUI, etc.)
 * This handles dropdowns that don't use native <select> elements.
 */
export async function extractDropdownOptions(page: Page): Promise<Record<string, string[]>> {
  const dropdowns: Record<string, string[]> = {};

  // Find React Select, MUI Select, and similar custom dropdowns
  const dropdownSelectors = [
    '[class*="MuiSelect"]',
    '[class*="react-select"]',
    '[role="combobox"]:not(select)',
    '[data-testid*="select"]',
    '[class*="ant-select"]',
  ];

  for (const selector of dropdownSelectors) {
    const elements = await page.locator(selector).all();
    for (let i = 0; i < elements.length; i++) {
      const el = elements[i];
      try {
        // Check if element is visible and interactable
        const isVisible = await el.isVisible({ timeout: 500 }).catch(() => false);
        if (!isVisible) continue;

        // Get identifier
        const id =
          (await el.getAttribute('id')) ||
          (await el.getAttribute('name')) ||
          (await el.getAttribute('data-testid')) ||
          (await el.getAttribute('aria-labelledby')) ||
          `${selector.replace(/[^a-zA-Z]/g, '')}-${i}`;

        // Skip if already captured
        if (dropdowns[id]) continue;

        // Click to open
        await el.click({ timeout: 1000 });
        await page.waitForTimeout(300);

        // Capture options from various dropdown implementations
        const optionSelectors = [
          '[role="option"]',
          '[class*="option"]',
          '[class*="MenuItem"]',
          'li[data-value]',
          '[class*="ant-select-item"]',
        ];

        let options: string[] = [];
        for (const optSelector of optionSelectors) {
          const optionTexts = await page.locator(optSelector).allTextContents();
          if (optionTexts.length > 0) {
            options = optionTexts.map((o) => o.trim()).filter(Boolean);
            break;
          }
        }

        if (options.length > 0) {
          dropdowns[id] = options;
        }

        // Close dropdown (press Escape or click outside)
        await page.keyboard.press('Escape');
        await page.waitForTimeout(100);
      } catch {
        // Skip if can't interact with this dropdown
        continue;
      }
    }
  }

  return dropdowns;
}
