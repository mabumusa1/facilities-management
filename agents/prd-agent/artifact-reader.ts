/**
 * Artifact Reader
 *
 * Reads source documentation artifacts for PRD generation
 */

import * as fs from 'fs';
import * as path from 'path';

export interface ArtifactData {
  content: string;
  parsed?: any;
}

export class ArtifactReader {
  private basePath: string;
  private cache: Map<string, ArtifactData> = new Map();

  constructor(basePath?: string) {
    this.basePath = basePath || path.join(__dirname, '../../src/api');
  }

  /**
   * Read an artifact by source identifier
   */
  async read(source: string): Promise<ArtifactData> {
    if (this.cache.has(source)) {
      return this.cache.get(source)!;
    }

    let data: ArtifactData;

    if (source.endsWith('.md')) {
      data = this.readMarkdown(source);
    } else if (source.startsWith('queries/')) {
      data = this.readQuerySchemas(source.replace('queries/', ''));
    } else if (source.startsWith('validations/')) {
      data = this.readValidationSchemas(source.replace('validations/', ''));
    } else if (source.endsWith('.json')) {
      data = this.readJSON(source);
    } else {
      data = { content: '' };
    }

    this.cache.set(source, data);

    return data;
  }

  /**
   * Read markdown documentation
   */
  private readMarkdown(filename: string): ArtifactData {
    const filePath = path.join(this.basePath, 'docs', filename);

    try {
      const content = fs.readFileSync(filePath, 'utf-8');

      return { content, parsed: this.parseMarkdown(content) };
    } catch (e) {
      console.warn(`Warning: Could not read ${filePath}`);

      return { content: '' };
    }
  }

  /**
   * Read JSON file
   */
  private readJSON(filename: string): ArtifactData {
    const filePath = path.join(this.basePath, filename);

    try {
      const content = fs.readFileSync(filePath, 'utf-8');

      return { content, parsed: JSON.parse(content) };
    } catch (e) {
      console.warn(`Warning: Could not read ${filePath}`);

      return { content: '' };
    }
  }

  /**
   * Read query schemas for a module
   */
  private readQuerySchemas(module: string): ArtifactData {
    const dirPath = path.join(this.basePath, 'queries', module);

    try {
      if (!fs.existsSync(dirPath)) {
        return { content: '', parsed: {} };
      }

      const schemas: Record<string, any> = {};
      const summaryPath = path.join(dirPath, 'summary.json');

      if (fs.existsSync(summaryPath)) {
        schemas.summary = JSON.parse(fs.readFileSync(summaryPath, 'utf-8'));
      }

      // Read individual schema files
      for (const file of fs.readdirSync(dirPath)) {
        if (file.endsWith('.json') && file !== 'summary.json') {
          const schemaPath = path.join(dirPath, file);
          schemas[file.replace('.json', '')] = JSON.parse(
            fs.readFileSync(schemaPath, 'utf-8')
          );
        }
      }

      return { content: JSON.stringify(schemas, null, 2), parsed: schemas };
    } catch (e) {
      console.warn(`Warning: Could not read query schemas for ${module}`);

      return { content: '', parsed: {} };
    }
  }

  /**
   * Read validation schemas for an entity
   */
  private readValidationSchemas(entity: string): ArtifactData {
    const dirPath = path.join(this.basePath, 'validations');

    try {
      const schemas: Record<string, any> = {};

      // Try exact match first
      const exactPath = path.join(dirPath, `${entity}.json`);

      if (fs.existsSync(exactPath)) {
        schemas[entity] = JSON.parse(fs.readFileSync(exactPath, 'utf-8'));
      }

      // Try pattern match
      for (const file of fs.readdirSync(dirPath)) {
        if (file.includes(entity) && file.endsWith('.json')) {
          const schemaPath = path.join(dirPath, file);
          schemas[file.replace('.json', '')] = JSON.parse(
            fs.readFileSync(schemaPath, 'utf-8')
          );
        }
      }

      return { content: JSON.stringify(schemas, null, 2), parsed: schemas };
    } catch (e) {
      console.warn(`Warning: Could not read validation schemas for ${entity}`);

      return { content: '', parsed: {} };
    }
  }

  /**
   * Parse markdown into sections
   */
  private parseMarkdown(content: string): Record<string, string> {
    const sections: Record<string, string> = {};
    const lines = content.split('\n');
    let currentSection = 'intro';
    let currentContent: string[] = [];

    for (const line of lines) {
      const headerMatch = line.match(/^(#{1,3})\s+(.+)$/);

      if (headerMatch) {
        if (currentContent.length > 0) {
          sections[currentSection] = currentContent.join('\n').trim();
        }

        currentSection = headerMatch[2].toLowerCase().replace(/\s+/g, '-');
        currentContent = [];
      } else {
        currentContent.push(line);
      }
    }

    if (currentContent.length > 0) {
      sections[currentSection] = currentContent.join('\n').trim();
    }

    return sections;
  }

  /**
   * Get all available modules from queries directory
   */
  getAvailableModules(): string[] {
    const queriesDir = path.join(this.basePath, 'queries');

    try {
      return fs.readdirSync(queriesDir).filter(f =>
        fs.statSync(path.join(queriesDir, f)).isDirectory()
      );
    } catch {
      return [];
    }
  }

  /**
   * Get all available validation schemas
   */
  getAvailableValidations(): string[] {
    const validationsDir = path.join(this.basePath, 'validations');

    try {
      return fs.readdirSync(validationsDir)
        .filter(f => f.endsWith('.json'))
        .map(f => f.replace('.json', ''));
    } catch {
      return [];
    }
  }
}
