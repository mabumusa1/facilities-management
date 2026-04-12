/**
 * Validation Rules Extractor
 *
 * Extracts comprehensive validation rules from API error messages.
 * Handles both Arabic and English validation messages.
 */

export interface ExtractedRule {
  rule: string;
  type?: 'string' | 'integer' | 'number' | 'boolean' | 'array' | 'date' | 'email' | 'object';
  constraints?: {
    min?: number;
    max?: number;
    minLength?: number;
    maxLength?: number;
    pattern?: string;
    enum?: string[];
  };
  conditional?: string;
  rawMessage: string;
}

export interface FieldValidation {
  field: string;
  required: boolean;
  type: string;
  rules: ExtractedRule[];
  examples: {
    valid: unknown[];
    invalid: unknown[];
  };
}

export interface EndpointValidation {
  endpoint: string;
  method: string;
  fields: Record<string, FieldValidation>;
  requiredFields: string[];
  optionalFields: string[];
}

// Arabic pattern definitions with regex
const ARABIC_PATTERNS: Array<{
  pattern: RegExp;
  rule: string;
  type?: ExtractedRule['type'];
  extractConstraint?: (match: RegExpMatchArray) => Partial<ExtractedRule['constraints']>;
}> = [
  // Required
  { pattern: /مطلوب/i, rule: 'required' },
  { pattern: /required/i, rule: 'required' },

  // String type
  { pattern: /يجب أن يكون .* نصآ/i, rule: 'string', type: 'string' },
  { pattern: /يجب أن يكون .* نص/i, rule: 'string', type: 'string' },
  { pattern: /must be a string/i, rule: 'string', type: 'string' },

  // Boolean type
  { pattern: /true أو false/i, rule: 'boolean', type: 'boolean' },
  { pattern: /يجب أن تكون قيمة .* إما true أو false/i, rule: 'boolean', type: 'boolean' },
  { pattern: /must be true or false/i, rule: 'boolean', type: 'boolean' },

  // Numeric type
  { pattern: /يجب أن يكون .* رقم/i, rule: 'numeric', type: 'number' },
  { pattern: /رقم صحيح/i, rule: 'integer', type: 'integer' },
  { pattern: /must be a number/i, rule: 'numeric', type: 'number' },
  { pattern: /must be an integer/i, rule: 'integer', type: 'integer' },
  { pattern: /numeric/i, rule: 'numeric', type: 'number' },

  // Array type
  { pattern: /مصفوفة/i, rule: 'array', type: 'array' },
  { pattern: /must be an array/i, rule: 'array', type: 'array' },

  // Date type
  { pattern: /تاريخ صالح/i, rule: 'date', type: 'date' },
  { pattern: /تاريخ/i, rule: 'date', type: 'date' },
  { pattern: /valid date/i, rule: 'date', type: 'date' },
  { pattern: /date format/i, rule: 'date', type: 'date' },

  // Email type
  { pattern: /بريد إلكتروني صالح/i, rule: 'email', type: 'email' },
  { pattern: /بريد إلكتروني/i, rule: 'email', type: 'email' },
  { pattern: /valid email/i, rule: 'email', type: 'email' },
  { pattern: /email address/i, rule: 'email', type: 'email' },

  // MinLength
  {
    pattern: /على الأقل (\d+) حرف/i,
    rule: 'minLength',
    type: 'string',
    extractConstraint: (match) => ({ minLength: parseInt(match[1], 10) }),
  },
  {
    pattern: /at least (\d+) character/i,
    rule: 'minLength',
    type: 'string',
    extractConstraint: (match) => ({ minLength: parseInt(match[1], 10) }),
  },
  {
    pattern: /minimum (\d+) character/i,
    rule: 'minLength',
    type: 'string',
    extractConstraint: (match) => ({ minLength: parseInt(match[1], 10) }),
  },

  // MaxLength
  {
    pattern: /لا يزيد عن (\d+) حرف/i,
    rule: 'maxLength',
    type: 'string',
    extractConstraint: (match) => ({ maxLength: parseInt(match[1], 10) }),
  },
  {
    pattern: /لا يتجاوز (\d+)/i,
    rule: 'maxLength',
    type: 'string',
    extractConstraint: (match) => ({ maxLength: parseInt(match[1], 10) }),
  },
  {
    pattern: /maximum (\d+) character/i,
    rule: 'maxLength',
    type: 'string',
    extractConstraint: (match) => ({ maxLength: parseInt(match[1], 10) }),
  },
  {
    pattern: /no more than (\d+)/i,
    rule: 'maxLength',
    type: 'string',
    extractConstraint: (match) => ({ maxLength: parseInt(match[1], 10) }),
  },

  // Min (numeric)
  {
    pattern: /أكبر من (\d+)/i,
    rule: 'min',
    type: 'number',
    extractConstraint: (match) => ({ min: parseInt(match[1], 10) }),
  },
  {
    pattern: /على الأقل (\d+)$/i,
    rule: 'min',
    type: 'number',
    extractConstraint: (match) => ({ min: parseInt(match[1], 10) }),
  },
  {
    pattern: /greater than (\d+)/i,
    rule: 'min',
    type: 'number',
    extractConstraint: (match) => ({ min: parseInt(match[1], 10) }),
  },
  {
    pattern: /minimum (\d+)$/i,
    rule: 'min',
    type: 'number',
    extractConstraint: (match) => ({ min: parseInt(match[1], 10) }),
  },

  // Max (numeric)
  {
    pattern: /أقل من (\d+)/i,
    rule: 'max',
    type: 'number',
    extractConstraint: (match) => ({ max: parseInt(match[1], 10) }),
  },
  {
    pattern: /less than (\d+)/i,
    rule: 'max',
    type: 'number',
    extractConstraint: (match) => ({ max: parseInt(match[1], 10) }),
  },
  {
    pattern: /maximum (\d+)$/i,
    rule: 'max',
    type: 'number',
    extractConstraint: (match) => ({ max: parseInt(match[1], 10) }),
  },

  // Unique
  { pattern: /مستخدم بالفعل/i, rule: 'unique' },
  { pattern: /مستخدمة بالفعل/i, rule: 'unique' },
  { pattern: /مُستخدمة من قبل/i, rule: 'unique' },
  { pattern: /مستخدم من قبل/i, rule: 'unique' },
  { pattern: /already taken/i, rule: 'unique' },
  { pattern: /already exists/i, rule: 'unique' },
  { pattern: /has already been taken/i, rule: 'unique' },

  // Exists (foreign key)
  { pattern: /غير موجود/i, rule: 'exists' },
  { pattern: /غير صالح/i, rule: 'invalid' },
  { pattern: /غير صحيح/i, rule: 'invalid' },
  { pattern: /does not exist/i, rule: 'exists' },
  { pattern: /is invalid/i, rule: 'invalid' },

  // Specific validations
  { pattern: /المرجع المحدد .* غير صالح/i, rule: 'exists' },
  { pattern: /القيمة المحددة .* غير صالحة/i, rule: 'invalid' },

  // Enum / In
  { pattern: /من بين/i, rule: 'enum' },
  { pattern: /must be one of/i, rule: 'enum' },
  { pattern: /القيم المسموح بها/i, rule: 'enum' },

  // Confirmed
  { pattern: /غير متطابق/i, rule: 'confirmed' },
  { pattern: /confirmation does not match/i, rule: 'confirmed' },
  { pattern: /must match/i, rule: 'confirmed' },

  // URL
  { pattern: /رابط صالح/i, rule: 'url', type: 'string' },
  { pattern: /valid URL/i, rule: 'url', type: 'string' },

  // Phone
  { pattern: /رقم جوال/i, rule: 'phone', type: 'string' },
  { pattern: /رقم هاتف/i, rule: 'phone', type: 'string' },
  { pattern: /phone number/i, rule: 'phone', type: 'string' },

  // Conditional required
  { pattern: /مطلوب إذا/i, rule: 'required_if' },
  { pattern: /required when/i, rule: 'required_if' },
  { pattern: /required if/i, rule: 'required_if' },

  // Selection/choice required
  { pattern: /الرجاء اختيار/i, rule: 'required' },
  { pattern: /يجب اختيار/i, rule: 'required' },
  { pattern: /على الأقل واحدة/i, rule: 'required' },
  { pattern: /at least one/i, rule: 'required' },
  { pattern: /please select/i, rule: 'required' },

  // File/Image
  { pattern: /يجب أن يكون ملف/i, rule: 'file' },
  { pattern: /يجب أن يكون صورة/i, rule: 'image' },
  { pattern: /must be a file/i, rule: 'file' },
  { pattern: /must be an image/i, rule: 'image' },

  // Size constraints for files
  {
    pattern: /حجم .* يجب ألا يتجاوز (\d+)/i,
    rule: 'max_size',
    extractConstraint: (match) => ({ max: parseInt(match[1], 10) }),
  },

  // Digits
  {
    pattern: /(\d+) أرقام/i,
    rule: 'digits',
    type: 'string',
    extractConstraint: (match) => ({ minLength: parseInt(match[1], 10), maxLength: parseInt(match[1], 10) }),
  },
  {
    pattern: /must be (\d+) digits/i,
    rule: 'digits',
    type: 'string',
    extractConstraint: (match) => ({ minLength: parseInt(match[1], 10), maxLength: parseInt(match[1], 10) }),
  },

  // Digits between
  {
    pattern: /بين (\d+) و (\d+) أرقام/i,
    rule: 'digits_between',
    type: 'string',
    extractConstraint: (match) => ({ minLength: parseInt(match[1], 10), maxLength: parseInt(match[2], 10) }),
  },
];

/**
 * Extract validation rule from error message
 */
export function extractRule(message: unknown): ExtractedRule | null {
  if (typeof message !== 'string') {
    if (Array.isArray(message)) {
      return extractRule(message[0]);
    }
    return null;
  }

  for (const { pattern, rule, type, extractConstraint } of ARABIC_PATTERNS) {
    const match = message.match(pattern);
    if (match) {
      const extractedRule: ExtractedRule = {
        rule,
        rawMessage: message,
      };

      if (type) {
        extractedRule.type = type;
      }

      if (extractConstraint) {
        extractedRule.constraints = extractConstraint(match);
      }

      return extractedRule;
    }
  }

  // Return unknown rule with original message
  return {
    rule: 'unknown',
    rawMessage: message,
  };
}

/**
 * Extract all rules from a validation error response
 */
export function extractRulesFromErrors(
  errors: Record<string, string[]>
): Record<string, ExtractedRule[]> {
  const result: Record<string, ExtractedRule[]> = {};

  for (const [field, messages] of Object.entries(errors)) {
    result[field] = [];
    for (const message of messages) {
      const rule = extractRule(message);
      if (rule) {
        result[field].push(rule);
      }
    }
  }

  return result;
}

/**
 * Infer field type from extracted rules
 */
export function inferFieldType(rules: ExtractedRule[]): string {
  // Priority: explicit type > inferred type > string
  for (const rule of rules) {
    if (rule.type) {
      return rule.type;
    }
  }

  // Infer from rule names
  for (const rule of rules) {
    switch (rule.rule) {
      case 'email':
        return 'email';
      case 'date':
        return 'date';
      case 'numeric':
      case 'integer':
      case 'min':
      case 'max':
        return 'number';
      case 'boolean':
        return 'boolean';
      case 'array':
        return 'array';
      case 'file':
      case 'image':
        return 'file';
    }
  }

  return 'string';
}

/**
 * Check if field is required based on rules
 */
export function isFieldRequired(rules: ExtractedRule[]): boolean {
  return rules.some((r) => r.rule === 'required' || r.rule === 'required_if');
}

/**
 * Build field validation from rules
 */
export function buildFieldValidation(
  field: string,
  rules: ExtractedRule[]
): FieldValidation {
  const type = inferFieldType(rules);
  const required = isFieldRequired(rules);

  // Generate examples based on type and rules
  const examples: FieldValidation['examples'] = {
    valid: [],
    invalid: [],
  };

  // Add type-based examples
  switch (type) {
    case 'string':
      examples.valid.push('example');
      examples.invalid.push('', null);
      break;
    case 'number':
    case 'integer':
      examples.valid.push(1);
      examples.invalid.push('abc', null);
      break;
    case 'boolean':
      examples.valid.push(true, false);
      examples.invalid.push('yes', 1, null);
      break;
    case 'email':
      examples.valid.push('user@example.com');
      examples.invalid.push('invalid-email', '');
      break;
    case 'date':
      examples.valid.push('2024-01-15');
      examples.invalid.push('not-a-date', '');
      break;
    case 'array':
      examples.valid.push([]);
      examples.invalid.push('not-array', null);
      break;
  }

  if (required) {
    examples.invalid.push(undefined);
  }

  return {
    field,
    required,
    type,
    rules,
    examples,
  };
}

/**
 * Build endpoint validation from multiple error captures
 */
export function buildEndpointValidation(
  endpoint: string,
  method: string,
  errorCaptures: Array<{ errors: Record<string, string[]> }>
): EndpointValidation {
  const fieldRulesMap = new Map<string, ExtractedRule[]>();

  // Collect all rules from all error captures
  for (const capture of errorCaptures) {
    const extractedRules = extractRulesFromErrors(capture.errors);
    for (const [field, rules] of Object.entries(extractedRules)) {
      if (!fieldRulesMap.has(field)) {
        fieldRulesMap.set(field, []);
      }
      // Add unique rules
      for (const rule of rules) {
        const existing = fieldRulesMap.get(field)!;
        if (!existing.some((r) => r.rule === rule.rule && r.rawMessage === rule.rawMessage)) {
          existing.push(rule);
        }
      }
    }
  }

  // Build field validations
  const fields: Record<string, FieldValidation> = {};
  const requiredFields: string[] = [];
  const optionalFields: string[] = [];

  for (const [field, rules] of Array.from(fieldRulesMap.entries())) {
    const validation = buildFieldValidation(field, rules);
    fields[field] = validation;

    if (validation.required) {
      requiredFields.push(field);
    } else {
      optionalFields.push(field);
    }
  }

  return {
    endpoint,
    method,
    fields,
    requiredFields: requiredFields.sort(),
    optionalFields: optionalFields.sort(),
  };
}
