/**
 * Template Index
 *
 * Export all PRD templates
 */

export * as foundation from './foundation';
export * as dataModel from './data-model';
export * as workflow from './workflow';
export * as moduleUi from './module-ui';

import * as dataModel from './data-model';
import * as foundation from './foundation';
import * as moduleUi from './module-ui';
import * as workflow from './workflow';

export const templates = {
  'foundation': foundation,
  'data-model': dataModel,
  'workflow': workflow,
  'module-ui': moduleUi
};

export type TemplateType = keyof typeof templates;
