import * as fs from 'fs/promises';
import * as path from 'path';
import { CapturedRequest, PageFormData, ScanResult, PageRelationships, DataAnalysis } from './types';

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

export async function writeFormData(pageName: string, formData: PageFormData): Promise<void> {
  const pageDir = path.join(SRC_PAGES_DIR, pageName);
  await ensureDir(pageDir);
  await fs.writeFile(path.join(pageDir, 'form-data.json'), JSON.stringify(formData, null, 2), 'utf-8');
}

/**
 * Extract a clean filename from a URL path
 */
function urlToFilename(url: string): string {
  try {
    const urlObj = new URL(url);
    // Get the pathname and remove leading slash
    let filename = urlObj.pathname.replace(/^\//, '').replace(/\//g, '_');
    // Add query params hash if present
    if (urlObj.search) {
      const queryHash = urlObj.search.replace(/[?&=]/g, '_').slice(0, 30);
      filename += queryHash;
    }
    // Sanitize and limit length
    filename = filename.replace(/[^a-zA-Z0-9_-]/g, '_').slice(0, 100);
    return filename || 'root';
  } catch {
    return 'unknown';
  }
}

/**
 * Write API responses to individual JSON files for easy analysis
 */
export async function writeRelationships(pageName: string, relationships: PageRelationships): Promise<void> {
  const pageDir = path.join(SRC_PAGES_DIR, pageName);
  await ensureDir(pageDir);
  await fs.writeFile(
    path.join(pageDir, 'relationships.json'),
    JSON.stringify(relationships, null, 2),
    'utf-8'
  );
}

export async function writeDataAnalysis(pageName: string, analysis: DataAnalysis): Promise<void> {
  const pageDir = path.join(SRC_PAGES_DIR, pageName);
  await ensureDir(pageDir);
  await fs.writeFile(
    path.join(pageDir, 'data-analysis.json'),
    JSON.stringify(analysis, null, 2),
    'utf-8'
  );
}

export async function writeApiResponses(pageName: string, apiRequests: CapturedRequest[]): Promise<void> {
  const pageDir = path.join(SRC_PAGES_DIR, pageName);
  const apiDir = path.join(pageDir, 'api');
  const responsesDir = path.join(apiDir, 'responses');

  await ensureDir(responsesDir);

  // Write a summary index file
  const summary = apiRequests.map((req, index) => ({
    index,
    method: req.method,
    url: req.url,
    status: req.status,
    hasRequestBody: req.requestBody !== undefined,
    hasResponseBody: req.responseBody !== undefined,
    filename: `${index.toString().padStart(3, '0')}_${urlToFilename(req.url)}.json`,
  }));

  await fs.writeFile(
    path.join(apiDir, 'api-responses-index.json'),
    JSON.stringify(summary, null, 2),
    'utf-8'
  );

  // Write individual response files
  for (let i = 0; i < apiRequests.length; i++) {
    const req = apiRequests[i];
    const filename = `${i.toString().padStart(3, '0')}_${urlToFilename(req.url)}.json`;

    const responseData = {
      method: req.method,
      url: req.url,
      status: req.status,
      contentType: req.contentType,
      timestamp: req.timestamp,
      request: req.requestBody,
      response: req.responseBody,
    };

    await fs.writeFile(
      path.join(responsesDir, filename),
      JSON.stringify(responseData, null, 2),
      'utf-8'
    );
  }
}
