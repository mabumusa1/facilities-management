/**
 * PRD Generator
 *
 * Generates PRD content using templates and source artifacts
 */

import { PRDConfig } from './config';
import { ArtifactReader, ArtifactData } from './artifact-reader';
import { templates, TemplateType } from './templates';

export interface GeneratorOptions {
  issueMap: Map<number, number>; // Maps PRD id to GitHub issue number
}

export class PRDGenerator {
  constructor(private reader: ArtifactReader) {}

  /**
   * Generate PRD content for a given configuration
   */
  async generate(prd: PRDConfig, options: GeneratorOptions): Promise<string> {
    // Load source artifacts
    const sourceData = await this.reader.read(prd.source);

    // Build dependency links
    const dependencyLinks = this.buildDependencyLinks(prd.dependsOn, options.issueMap);

    // Select and render template
    const template = templates[prd.template as TemplateType];

    if (!template) {
      throw new Error(`Unknown template: ${prd.template}`);
    }

    return template.render({
      prd,
      sourceData,
      dependencyLinks,
      issueMap: options.issueMap
    });
  }

  /**
   * Build dependency links for the PRD body
   */
  private buildDependencyLinks(
    dependsOn: number[],
    issueMap: Map<number, number>
  ): string {
    if (dependsOn.length === 0) {
      return '';
    }

    const links = dependsOn.map(prdId => {
      const issueNumber = issueMap.get(prdId);
      return issueNumber ? `#${issueNumber}` : `(PRD ${prdId} - pending)`;
    });

    return links.join(', ');
  }

  /**
   * Validate PRD configuration
   */
  validatePRD(prd: PRDConfig): string[] {
    const errors: string[] = [];

    if (!prd.title) {
      errors.push(`PRD ${prd.id}: Missing title`);
    }

    if (!prd.milestone) {
      errors.push(`PRD ${prd.id}: Missing milestone`);
    }

    if (!prd.labels || prd.labels.length === 0) {
      errors.push(`PRD ${prd.id}: Missing labels`);
    }

    if (!prd.template) {
      errors.push(`PRD ${prd.id}: Missing template`);
    }

    if (!templates[prd.template as TemplateType]) {
      errors.push(`PRD ${prd.id}: Unknown template "${prd.template}"`);
    }

    return errors;
  }

  /**
   * Validate all PRDs for circular dependencies
   */
  validateDependencies(prds: PRDConfig[]): string[] {
    const errors: string[] = [];
    const prdIds = new Set(prds.map(p => p.id));

    for (const prd of prds) {
      for (const depId of prd.dependsOn) {
        if (!prdIds.has(depId)) {
          errors.push(`PRD ${prd.id}: Depends on non-existent PRD ${depId}`);
        }
        if (depId === prd.id) {
          errors.push(`PRD ${prd.id}: Self-dependency detected`);
        }
      }
    }

    // Check for circular dependencies using topological sort
    const visited = new Set<number>();
    const recursionStack = new Set<number>();

    const hasCycle = (id: number): boolean => {
      visited.add(id);
      recursionStack.add(id);

      const prd = prds.find(p => p.id === id);
      if (prd) {
        for (const depId of prd.dependsOn) {
          if (!visited.has(depId) && hasCycle(depId)) {
            return true;
          }
          if (recursionStack.has(depId)) {
            errors.push(`Circular dependency detected involving PRD ${id} and PRD ${depId}`);
            return true;
          }
        }
      }

      recursionStack.delete(id);
      return false;
    };

    for (const prd of prds) {
      if (!visited.has(prd.id)) {
        hasCycle(prd.id);
      }
    }

    return errors;
  }

  /**
   * Get PRDs in dependency order (topological sort)
   */
  getOrderedPRDs(prds: PRDConfig[]): PRDConfig[] {
    const result: PRDConfig[] = [];
    const visited = new Set<number>();
    const prdMap = new Map(prds.map(p => [p.id, p]));

    const visit = (id: number) => {
      if (visited.has(id)) return;
      visited.add(id);

      const prd = prdMap.get(id);
      if (prd) {
        for (const depId of prd.dependsOn) {
          visit(depId);
        }
        result.push(prd);
      }
    };

    for (const prd of prds) {
      visit(prd.id);
    }

    return result;
  }
}
