<?php

namespace App\Http\Controllers\Leasing;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use App\Models\LeaseKycDocument;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class KycController extends Controller
{
    /**
     * Show the KYC document checklist for a lease application.
     */
    public function kyc(Lease $lease): Response
    {
        $this->authorize('update', $lease);

        $lease->load(['status', 'tenant', 'quote']);

        $documentTypes = Setting::query()
            ->where('type', 'kyc_document_type')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'name', 'name_en', 'name_ar', 'metadata']);

        $uploadedDocuments = LeaseKycDocument::query()
            ->where('lease_id', $lease->id)
            ->with('documentType')
            ->get();

        $requiredCount = $documentTypes->filter(
            fn (Setting $type) => (bool) ($type->metadata['is_required'] ?? false)
        )->count();

        $uploadedRequiredCount = $uploadedDocuments
            ->filter(fn (LeaseKycDocument $doc) => $doc->is_required)
            ->unique('document_type_id')
            ->count();

        return Inertia::render('leasing/leases/Kyc', [
            'lease' => $lease,
            'documentTypes' => $documentTypes,
            'uploadedDocuments' => $uploadedDocuments,
            'progress' => [
                'uploaded' => $uploadedRequiredCount,
                'total' => $requiredCount,
            ],
        ]);
    }

    /**
     * Upload a KYC document for a lease application.
     */
    public function uploadKyc(Request $request, Lease $lease): RedirectResponse
    {
        $this->authorize('uploadKyc', $lease);

        $validated = $request->validate([
            'document_type_id' => ['required', 'integer', Rule::exists('rf_settings', 'id')->where('type', 'kyc_document_type')],
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,heic'],
        ]);

        /** @var UploadedFile $file */
        $file = $validated['file'];

        $documentType = Setting::query()->findOrFail((int) $validated['document_type_id']);
        $isRequired = (bool) ($documentType->metadata['is_required'] ?? false);

        $path = $file->store("leases/{$lease->id}/kyc", 'local');

        LeaseKycDocument::create([
            'lease_id' => $lease->id,
            'document_type_id' => (int) $validated['document_type_id'],
            'is_required' => $isRequired,
            'original_file_name' => $file->getClientOriginalName(),
            'stored_path' => (string) $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'account_tenant_id' => $lease->account_tenant_id,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Document uploaded.')]);

        return to_route('leases.kyc', $lease);
    }

    /**
     * Remove a KYC document from a lease application.
     */
    public function removeKycDocument(Lease $lease, LeaseKycDocument $document): RedirectResponse
    {
        $this->authorize('removeKycDocument', $lease);

        // Ensure the document belongs to this lease.
        if ((int) $document->lease_id !== $lease->id) {
            abort(404);
        }

        Storage::disk('local')->delete($document->stored_path);

        $document->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Document removed.')]);

        return to_route('leases.kyc', $lease);
    }

    /**
     * Submit a lease application for approval after validating all required KYC documents are present.
     *
     * @throws ValidationException When required documents are missing.
     */
    public function submitForApproval(Lease $lease): RedirectResponse
    {
        $this->authorize('submitForApproval', $lease);

        $requiredTypes = Setting::query()
            ->where('type', 'kyc_document_type')
            ->get(['id', 'name_en', 'name', 'metadata'])
            ->filter(fn (Setting $type) => (bool) ($type->metadata['is_required'] ?? false));

        $uploadedTypeIds = LeaseKycDocument::query()
            ->where('lease_id', $lease->id)
            ->pluck('document_type_id')
            ->unique();

        $missingTypes = $requiredTypes->filter(
            fn (Setting $type) => ! $uploadedTypeIds->contains($type->id)
        );

        if ($missingTypes->isNotEmpty()) {
            $missingNames = $missingTypes->map(
                fn (Setting $type) => $type->name_en ?? $type->name
            )->implode(', ');

            throw ValidationException::withMessages([
                'kyc_documents' => __(
                    ':count required document(s) missing: :list',
                    ['count' => $missingTypes->count(), 'list' => $missingNames]
                ),
            ]);
        }

        $lease->update([
            'kyc_complete' => true,
            'kyc_submitted_at' => now(),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Lease application submitted for approval.')]);

        return to_route('leases.show', $lease);
    }
}
