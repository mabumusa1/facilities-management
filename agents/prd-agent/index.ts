#!/usr/bin/env npx tsx

/**
 * PRD Agent - Main Entry Point
 *
 * Generates PRD issues in a GitHub repository for the
 * facilities management system.
 *
 * Usage:
 *   npx tsx agents/prd-agent/index.ts
 *
 * Options:
 *   --dry-run     Preview without creating issues
 *   --start-from  Start from a specific PRD ID
 *   --only        Only create a specific PRD by ID
 */

import { CONFIG, PRDConfig } from './config';
import { GitHubClient } from './github-client';
import { ArtifactReader } from './artifact-reader';
import { PRDGenerator } from './prd-generator';

interface CLIOptions {
  dryRun: boolean;
  startFrom?: number;
  only?: number;
}

function parseArgs(): CLIOptions {
  const args = process.argv.slice(2);
  const options: CLIOptions = { dryRun: false };

  for (let i = 0; i < args.length; i++) {
    if (args[i] === '--dry-run') {
      options.dryRun = true;
    } else if (args[i] === '--start-from' && args[i + 1]) {
      options.startFrom = parseInt(args[++i], 10);
    } else if (args[i] === '--only' && args[i + 1]) {
      options.only = parseInt(args[++i], 10);
    }
  }

  return options;
}

async function main() {
  const options = parseArgs();

  console.log('='.repeat(60));
  console.log('PRD Agent - Facilities Management System');
  console.log('='.repeat(60));
  console.log();

  // Initialize components
  const gh = new GitHubClient(CONFIG.repo);
  const reader = new ArtifactReader();
  const generator = new PRDGenerator(reader);

  // Get GitHub username
  const username = await gh.getUsername();
  console.log(`Authenticated as: ${username}`);
  CONFIG.repo.owner = username;

  // Validate all PRDs
  console.log('\nValidating PRD configurations...');
  const allErrors: string[] = [];

  for (const prd of CONFIG.prds) {
    const errors = generator.validatePRD(prd);
    allErrors.push(...errors);
  }

  const depErrors = generator.validateDependencies(CONFIG.prds);
  allErrors.push(...depErrors);

  if (allErrors.length > 0) {
    console.error('\nValidation errors:');
    for (const err of allErrors) {
      console.error(`  - ${err}`);
    }
    process.exit(1);
  }

  console.log(`  ${CONFIG.prds.length} PRDs validated successfully`);

  // Get PRDs in dependency order
  const orderedPRDs = generator.getOrderedPRDs(CONFIG.prds);

  // Filter PRDs based on options
  let prdsToProcess = orderedPRDs;

  if (options.only) {
    prdsToProcess = orderedPRDs.filter(p => p.id === options.only);
    if (prdsToProcess.length === 0) {
      console.error(`PRD ${options.only} not found`);
      process.exit(1);
    }
  } else if (options.startFrom) {
    const startIndex = orderedPRDs.findIndex(p => p.id === options.startFrom);
    if (startIndex === -1) {
      console.error(`PRD ${options.startFrom} not found`);
      process.exit(1);
    }
    prdsToProcess = orderedPRDs.slice(startIndex);
  }

  if (options.dryRun) {
    console.log('\n[DRY RUN MODE - No changes will be made]\n');
    console.log('PRDs to create:');
    for (const prd of prdsToProcess) {
      console.log(`  ${prd.id}. ${prd.title}`);
      console.log(`     Milestone: ${prd.milestone}`);
      console.log(`     Labels: ${prd.labels.join(', ')}`);
      console.log(`     Depends on: ${prd.dependsOn.length > 0 ? prd.dependsOn.join(', ') : 'None'}`);
      console.log();
    }
    return;
  }

  // Create repository
  console.log('\n--- Phase 1: Repository Setup ---');
  await gh.createRepo();

  // Create labels
  console.log('\n--- Phase 2: Labels ---');
  await gh.createLabels(CONFIG.labels);

  // Create milestones
  console.log('\n--- Phase 3: Milestones ---');
  const milestones = await gh.createMilestones(CONFIG.milestones);

  // Track created issues
  const issueMap = new Map<number, number>();

  // Create issues
  console.log('\n--- Phase 4: Creating PRD Issues ---');
  console.log(`Creating ${prdsToProcess.length} PRD issues...\n`);

  for (const prd of prdsToProcess) {
    try {
      // Generate PRD content
      const body = await generator.generate(prd, { issueMap });

      // Verify milestone exists
      const milestoneNumber = milestones[prd.milestone];
      if (!milestoneNumber) {
        console.warn(`Warning: Milestone "${prd.milestone}" not found for PRD ${prd.id}`);
        continue;
      }

      // Create issue (pass milestone title, not number)
      const issue = await gh.createIssue({
        title: prd.title,
        body,
        labels: prd.labels,
        milestone: prd.milestone
      });

      issueMap.set(prd.id, issue.number);

      console.log(`Created: #${issue.number} - ${prd.title}`);
      console.log(`         ${issue.url}`);

      // Small delay to avoid rate limiting
      await new Promise(resolve => setTimeout(resolve, 500));

    } catch (error: any) {
      console.error(`Failed to create PRD ${prd.id}: ${error.message}`);
    }
  }

  // Summary
  console.log('\n' + '='.repeat(60));
  console.log('Summary');
  console.log('='.repeat(60));
  console.log(`Repository: https://github.com/${CONFIG.repo.owner}/${CONFIG.repo.name}`);
  console.log(`Issues created: ${issueMap.size}/${prdsToProcess.length}`);
  console.log(`Labels: ${CONFIG.labels.length}`);
  console.log(`Milestones: ${Object.keys(milestones).length}`);

  if (issueMap.size < prdsToProcess.length) {
    console.log('\nSome issues failed to create. Run again with --start-from to continue.');
  }

  console.log('\nDone!');
}

main().catch(error => {
  console.error('Fatal error:', error);
  process.exit(1);
});
