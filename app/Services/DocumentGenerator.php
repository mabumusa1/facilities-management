<?php

namespace App\Services;

use App\Models\DocumentRecord;
use App\Models\DocumentTemplate;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class DocumentGenerator
{
    /**
     * Generate a document from a template and caller-provided merge values.
     *
     * @param  array<string, string>  $mergeValues  keyed by merge field key → resolved value
     * @return array{ document_record_id: int, status: string, errors: array<int, array{key: string, label_en: string}> }
     *
     * @throws \RuntimeException when no active template is found or no version exists
     */
    public function generate(string $templateType, string $sourceType, int $sourceId, string $locale, array $mergeValues): array
    {
        $tenant = Tenant::current();

        if (! $tenant) {
            throw new \RuntimeException('No tenant context for document generation.');
        }

        $template = DocumentTemplate::query()
            ->with('currentVersion')
            ->where('account_tenant_id', $tenant->id)
            ->where('type', $templateType)
            ->where('status', 'active')
            ->first();

        if (! $template) {
            throw new \RuntimeException("No active '{$templateType}' template found.");
        }

        $version = $template->currentVersion;

        if (! $version) {
            throw new \RuntimeException("Template '{$template->name['en']}' has no published version.");
        }

        $mergeFields = $version->merge_fields ?? [];
        $body = json_decode($version->body, true);
        $templateBody = $body[$locale] ?? $body['en'] ?? '';

        $resolved = [];
        $errors = [];

        foreach ($mergeFields as $field) {
            $key = $field['key'] ?? '';

            if ($key === '') {
                continue;
            }

            if (array_key_exists($key, $mergeValues) && $mergeValues[$key] !== null && $mergeValues[$key] !== '') {
                $resolved[$key] = (string) $mergeValues[$key];
            } else {
                $errors[] = [
                    'key' => $key,
                    'label_en' => $field['label_en'] ?? $key,
                ];
            }
        }

        if (count($errors) > 0) {
            return [
                'document_record_id' => 0,
                'status' => 'error',
                'errors' => $errors,
            ];
        }

        $rendered = $templateBody;

        foreach ($resolved as $key => $value) {
            $rendered = str_replace('{{'.$key.'}}', $value, $rendered);
        }

        $record = DB::transaction(function () use ($version, $tenant, $sourceType, $sourceId, $rendered) {
            return DocumentRecord::create([
                'account_tenant_id' => $tenant->id,
                'document_template_version_id' => $version->id,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'generated_at' => now(),
                'file_path' => $rendered,
                'status' => 'draft',
            ]);
        });

        return [
            'document_record_id' => $record->id,
            'status' => 'draft',
            'errors' => [],
        ];
    }
}
