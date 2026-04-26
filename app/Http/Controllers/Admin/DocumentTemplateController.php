<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use App\Models\DocumentVersion;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class DocumentTemplateController extends Controller
{
    public function index(): Response
    {
        $tenant = Tenant::current();
        abort_unless($tenant !== null, 404);

        $this->authorize('viewAny', DocumentTemplate::class);

        $templates = DocumentTemplate::query()
            ->with('currentVersion:id,document_template_id,version_number,published_at')
            ->where('account_tenant_id', $tenant->id)
            ->latest('id')
            ->paginate(15)
            ->through(fn (DocumentTemplate $template): array => [
                'id' => $template->id,
                'name' => $template->name,
                'type' => $template->type,
                'status' => $template->status,
                'format' => $template->format,
                'current_version' => $template->currentVersion ? [
                    'id' => $template->currentVersion->id,
                    'version_number' => $template->currentVersion->version_number,
                    'published_at' => $template->currentVersion->published_at,
                ] : null,
                'created_at' => $template->created_at?->toJSON(),
            ]);

        return Inertia::render('admin/documents/Index', [
            'templates' => $templates,
            'templateTypes' => [
                ['value' => 'lease', 'label' => 'Lease'],
                ['value' => 'booking', 'label' => 'Booking'],
                ['value' => 'invoice', 'label' => 'Invoice'],
                ['value' => 'receipt', 'label' => 'Receipt'],
                ['value' => 'custom', 'label' => 'Custom'],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', DocumentTemplate::class);

        $validated = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in(['lease', 'booking', 'invoice', 'receipt', 'custom'])],
            'format' => ['required', 'string', Rule::in(['word_upload', 'in_platform'])],
            'body_en' => ['nullable', 'string'],
            'body_ar' => ['nullable', 'string'],
            'merge_fields' => ['nullable', 'array'],
            'merge_fields.*.key' => ['required', 'string'],
            'merge_fields.*.label_en' => ['required', 'string'],
            'merge_fields.*.label_ar' => ['nullable', 'string'],
            'merge_fields.*.type' => ['required', 'string', Rule::in(['date', 'string', 'currency', 'number'])],
            'merge_fields.*.source_path' => ['required', 'string'],
        ]);

        $template = DB::transaction(function () use ($validated, $request) {
            $template = DocumentTemplate::create([
                'account_tenant_id' => Tenant::current()->id,
                'name' => [
                    'en' => $validated['name_en'],
                    'ar' => $validated['name_ar'] ?? null,
                ],
                'type' => $validated['type'],
                'status' => 'draft',
                'format' => $validated['format'],
                'created_by' => $request->user()->id,
            ]);

            $version = DocumentVersion::create([
                'document_template_id' => $template->id,
                'version_number' => 1,
                'body' => json_encode([
                    'en' => $validated['body_en'] ?? '',
                    'ar' => $validated['body_ar'] ?? '',
                ]),
                'merge_fields' => $validated['merge_fields'] ?? [],
                'published_at' => now(),
                'created_by' => $request->user()->id,
            ]);

            $template->update(['current_version_id' => $version->id]);

            return $template;
        });

        return redirect()->route('admin.documents.index')
            ->with('success', __('Template created successfully.'));
    }

    public function update(Request $request, DocumentTemplate $documentTemplate): RedirectResponse
    {
        $this->authorize('update', $documentTemplate);

        $validated = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in(['lease', 'booking', 'invoice', 'receipt', 'custom'])],
            'format' => ['required', 'string', Rule::in(['word_upload', 'in_platform'])],
            'body_en' => ['nullable', 'string'],
            'body_ar' => ['nullable', 'string'],
            'merge_fields' => ['nullable', 'array'],
            'merge_fields.*.key' => ['required', 'string'],
            'merge_fields.*.label_en' => ['required', 'string'],
            'merge_fields.*.label_ar' => ['nullable', 'string'],
            'merge_fields.*.type' => ['required', 'string', Rule::in(['date', 'string', 'currency', 'number'])],
            'merge_fields.*.source_path' => ['required', 'string'],
        ]);

        DB::transaction(function () use ($documentTemplate, $validated, $request) {
            $latestVersion = $documentTemplate->currentVersion;
            $newVersionNumber = ($latestVersion?->version_number ?? 0) + 1;

            $version = DocumentVersion::create([
                'document_template_id' => $documentTemplate->id,
                'version_number' => $newVersionNumber,
                'body' => json_encode([
                    'en' => $validated['body_en'] ?? '',
                    'ar' => $validated['body_ar'] ?? '',
                ]),
                'merge_fields' => $validated['merge_fields'] ?? [],
                'published_at' => now(),
                'created_by' => $request->user()->id,
            ]);

            $documentTemplate->update([
                'name' => [
                    'en' => $validated['name_en'],
                    'ar' => $validated['name_ar'] ?? null,
                ],
                'type' => $validated['type'],
                'format' => $validated['format'],
                'current_version_id' => $version->id,
            ]);
        });

        return redirect()->route('admin.documents.index')
            ->with('success', __('Template updated to version :version.', [
                'version' => $documentTemplate->currentVersion?->version_number,
            ]));
    }

    public function destroy(DocumentTemplate $documentTemplate): RedirectResponse
    {
        $this->authorize('delete', $documentTemplate);

        $documentTemplate->delete();

        return redirect()->route('admin.documents.index')
            ->with('success', __('Template archived.'));
    }

    public function activate(DocumentTemplate $documentTemplate): RedirectResponse
    {
        $this->authorize('update', $documentTemplate);

        $documentTemplate->update(['status' => 'active']);

        return redirect()->route('admin.documents.index')
            ->with('success', __('Template activated.'));
    }

    public function archive(DocumentTemplate $documentTemplate): RedirectResponse
    {
        $this->authorize('update', $documentTemplate);

        $documentTemplate->update(['status' => 'archived']);

        return redirect()->route('admin.documents.index')
            ->with('success', __('Template archived.'));
    }

    public function show(DocumentTemplate $documentTemplate): Response
    {
        $this->authorize('view', $documentTemplate);

        $documentTemplate->load(['versions' => function ($query) {
            $query->orderBy('version_number', 'desc');
        }, 'versions.creator:id,name']);

        return Inertia::render('admin/documents/Editor', [
            'template' => [
                'id' => $documentTemplate->id,
                'name' => $documentTemplate->name,
                'type' => $documentTemplate->type,
                'status' => $documentTemplate->status,
                'format' => $documentTemplate->format,
                'current_version_id' => $documentTemplate->current_version_id,
                'versions' => $documentTemplate->versions->map(fn (DocumentVersion $v): array => [
                    'id' => $v->id,
                    'version_number' => $v->version_number,
                    'body' => $v->body,
                    'merge_fields' => $v->merge_fields,
                    'published_at' => $v->published_at?->toJSON(),
                    'creator' => $v->creator?->only(['id', 'name']),
                ]),
                'created_at' => $documentTemplate->created_at?->toJSON(),
            ],
            'templateTypes' => [
                ['value' => 'lease', 'label' => 'Lease'],
                ['value' => 'booking', 'label' => 'Booking'],
                ['value' => 'invoice', 'label' => 'Invoice'],
                ['value' => 'receipt', 'label' => 'Receipt'],
                ['value' => 'custom', 'label' => 'Custom'],
            ],
            'fieldTypes' => [
                ['value' => 'string', 'label' => 'Text'],
                ['value' => 'date', 'label' => 'Date'],
                ['value' => 'currency', 'label' => 'Currency'],
                ['value' => 'number', 'label' => 'Number'],
            ],
        ]);
    }
}
