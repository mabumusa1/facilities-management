import * as fs from 'fs/promises';
import * as path from 'path';
import { ScanResult } from './types';

const SRC_PAGES_DIR = path.join(process.cwd(), 'src', 'pages');

export async function ensureDir(dir: string): Promise<void> {
  await fs.mkdir(dir, { recursive: true });
}

export async function writePageScan(pageName: string, data: ScanResult): Promise<void> {
  const pageDir = path.join(SRC_PAGES_DIR, pageName);
  const apiDir = path.join(pageDir, 'api');

  await ensureDir(apiDir);

  // Write endpoints.json (simple log format)
  const endpointsContent = data.endpoints.join('\n');
  await fs.writeFile(path.join(apiDir, 'endpoints.json'), endpointsContent, 'utf-8');

  // Write screenshot if available
  if (data.screenshot) {
    await fs.writeFile(path.join(pageDir, 'screenshot.png'), data.screenshot);
  }

  // Write snapshot if available
  if (data.snapshot) {
    await fs.writeFile(path.join(pageDir, 'snapshot.yml'), data.snapshot, 'utf-8');
  }
}

export async function writeEndpoints(pageName: string, endpoints: string[]): Promise<void> {
  const pageDir = path.join(SRC_PAGES_DIR, pageName);
  const apiDir = path.join(pageDir, 'api');
  await ensureDir(apiDir);
  await fs.writeFile(path.join(apiDir, 'endpoints.json'), endpoints.join('\n'), 'utf-8');
}

export async function writeScreenshot(pageName: string, screenshot: Buffer): Promise<void> {
  const pageDir = path.join(SRC_PAGES_DIR, pageName);
  await ensureDir(pageDir);
  await fs.writeFile(path.join(pageDir, 'screenshot.png'), screenshot);
}

export async function writeSnapshot(pageName: string, snapshot: string): Promise<void> {
  const pageDir = path.join(SRC_PAGES_DIR, pageName);
  await ensureDir(pageDir);
  await fs.writeFile(path.join(pageDir, 'snapshot.yml'), snapshot, 'utf-8');
}
