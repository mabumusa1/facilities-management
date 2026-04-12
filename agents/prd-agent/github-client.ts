/**
 * GitHub Client
 *
 * Wrapper around GitHub CLI (gh) for repository and issue management
 */

import { execSync } from 'child_process';
import { LabelConfig, MilestoneConfig, RepoConfig } from './config';

export interface IssueConfig {
  title: string;
  body: string;
  labels: string[];
  milestone: string; // Milestone title
}

export class GitHubClient {
  private owner: string = '';

  constructor(private config: RepoConfig) {}

  /**
   * Get the authenticated GitHub username
   */
  async getUsername(): Promise<string> {
    if (this.owner) return this.owner;

    try {
      const response = execSync('gh api user --jq .login', {
        encoding: 'utf-8'
      }).trim();
      this.owner = response;
      return this.owner;
    } catch (e) {
      throw new Error('Failed to get GitHub username. Make sure gh is authenticated.');
    }
  }

  /**
   * Get the full repo identifier (owner/name)
   */
  async getRepoId(): Promise<string> {
    const owner = await this.getUsername();
    return `${owner}/${this.config.name}`;
  }

  /**
   * Create the GitHub repository
   */
  async createRepo(): Promise<void> {
    const { name, description, isPublic } = this.config;
    const visibility = isPublic ? '--public' : '--private';

    console.log(`Creating repository: ${name}...`);

    try {
      execSync(
        `gh repo create ${name} ${visibility} --description "${description}"`,
        { stdio: 'inherit' }
      );
      console.log(`Repository created: ${name}`);
    } catch (e) {
      console.log('Repository may already exist, continuing...');
    }
  }

  /**
   * Create labels in the repository
   */
  async createLabels(labels: LabelConfig[]): Promise<void> {
    const repo = await this.getRepoId();
    console.log(`Creating ${labels.length} labels...`);

    for (const label of labels) {
      try {
        execSync(
          `gh label create "${label.name}" --color "${label.color}" --description "${label.description}" -R ${repo}`,
          { stdio: 'pipe' }
        );
        console.log(`  Created label: ${label.name}`);
      } catch (e) {
        // Label may already exist
        console.log(`  Label exists: ${label.name}`);
      }
    }
  }

  /**
   * Create milestones in the repository
   */
  async createMilestones(milestones: MilestoneConfig[]): Promise<Record<string, number>> {
    const repo = await this.getRepoId();
    const result: Record<string, number> = {};

    console.log(`Creating ${milestones.length} milestones...`);

    for (const ms of milestones) {
      try {
        const response = execSync(
          `gh api repos/${repo}/milestones -X POST -f title="${ms.title}" -f description="${ms.description}"`,
          { encoding: 'utf-8' }
        );
        const data = JSON.parse(response);
        result[ms.title] = data.number;
        console.log(`  Created milestone: ${ms.title} (#${data.number})`);
      } catch (e: any) {
        // Milestone may already exist, try to get it
        try {
          const existingResponse = execSync(
            `gh api "repos/${repo}/milestones?state=all" --jq '.[] | select(.title == "${ms.title}") | .number'`,
            { encoding: 'utf-8' }
          ).trim();
          if (existingResponse) {
            result[ms.title] = parseInt(existingResponse, 10);
            console.log(`  Milestone exists: ${ms.title} (#${result[ms.title]})`);
          }
        } catch {
          console.log(`  Failed to create/find milestone: ${ms.title}`);
        }
      }
    }

    return result;
  }

  /**
   * Create an issue in the repository
   */
  async createIssue(issue: IssueConfig): Promise<{ number: number; url: string }> {
    const repo = await this.getRepoId();
    const labels = issue.labels.join(',');

    // Write body to temp file to handle special characters
    const fs = require('fs');
    const path = require('path');
    const tempFile = path.join(require('os').tmpdir(), `prd-body-${Date.now()}.md`);
    fs.writeFileSync(tempFile, issue.body);

    try {
      // Use milestone title wrapped in quotes
      const response = execSync(
        `gh issue create -R ${repo} --title "${issue.title.replace(/"/g, '\\"')}" --body-file "${tempFile}" --label "${labels}" --milestone "${issue.milestone}"`,
        { encoding: 'utf-8' }
      );

      // Clean up temp file
      fs.unlinkSync(tempFile);

      // Extract issue number from URL
      const match = response.match(/\/issues\/(\d+)/);
      const number = parseInt(match?.[1] || '0', 10);

      return { number, url: response.trim() };
    } catch (e: any) {
      // Clean up temp file
      try { require('fs').unlinkSync(tempFile); } catch {}
      throw new Error(`Failed to create issue: ${issue.title}\n${e.message}`);
    }
  }

  /**
   * Check if repository exists
   */
  async repoExists(): Promise<boolean> {
    const repo = await this.getRepoId();
    try {
      execSync(`gh repo view ${repo}`, { stdio: 'pipe' });
      return true;
    } catch {
      return false;
    }
  }

  /**
   * Get issue count in repository
   */
  async getIssueCount(): Promise<number> {
    const repo = await this.getRepoId();
    try {
      const response = execSync(
        `gh issue list -R ${repo} --state all --limit 1000 --json number | jq length`,
        { encoding: 'utf-8' }
      );
      return parseInt(response.trim(), 10);
    } catch {
      return 0;
    }
  }
}
