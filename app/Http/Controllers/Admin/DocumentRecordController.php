<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentRecord;
use App\Models\DocumentSignature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DocumentRecordController extends Controller
{
    public function show(DocumentRecord $documentRecord): Response
    {
        $this->authorize('view', $documentRecord->templateVersion?->template);

        $documentRecord->load(['signatures', 'templateVersion.template']);

        return Inertia::render('admin/documents/Record', [
            'record' => [
                'id' => $documentRecord->id,
                'status' => $documentRecord->status,
                'source_type' => $documentRecord->source_type,
                'source_id' => $documentRecord->source_id,
                'generated_at' => $documentRecord->generated_at?->toJSON(),
                'signing_token' => $documentRecord->signing_token,
                'signed_at' => $documentRecord->signed_at?->toJSON(),
                'template_name' => $documentRecord->templateVersion?->template?->name,
                'version_number' => $documentRecord->templateVersion?->version_number,
                'signatures' => $documentRecord->signatures->map(fn (DocumentSignature $s): array => [
                    'id' => $s->id,
                    'signer_name' => $s->signer_name,
                    'signer_email' => $s->signer_email,
                    'signed_at' => $s->signed_at?->toJSON(),
                    'otp_verified_at' => $s->otp_verified_at?->toJSON(),
                ]),
            ],
        ]);
    }

    public function sendForSignature(Request $request, DocumentRecord $documentRecord): RedirectResponse
    {
        $this->authorize('update', $documentRecord->templateVersion?->template);

        if ($documentRecord->status !== 'draft' && $documentRecord->status !== 'link_expired') {
            return back()->with('error', 'Only draft or link-expired documents can be sent for signing.');
        }

        $validated = $request->validate([
            'signer_name' => ['required', 'string', 'max:255'],
            'signer_email' => ['required', 'email', 'max:255'],
            'signer_phone' => ['nullable', 'string', 'max:20'],
        ]);

        $token = Str::random(64);

        DocumentSignature::create([
            'document_record_id' => $documentRecord->id,
            'signer_name' => $validated['signer_name'],
            'signer_email' => $validated['signer_email'],
        ]);

        $documentRecord->update([
            'status' => 'sent',
            'signing_token' => $token,
            'sent_at' => now(),
        ]);

        return redirect()->route('admin.documents.records.show', ['documentRecord' => $documentRecord->id])
            ->with('success', __('Document sent for signature to :name.', ['name' => $validated['signer_name']]));
    }

    public function resendLink(DocumentRecord $documentRecord): RedirectResponse
    {
        $this->authorize('update', $documentRecord->templateVersion?->template);

        if (! in_array($documentRecord->status, ['sent', 'link_expired'], true)) {
            return back()->with('error', 'Only sent or link-expired documents can be resent.');
        }

        $token = Str::random(64);
        $documentRecord->update([
            'status' => 'sent',
            'signing_token' => $token,
            'sent_at' => now(),
        ]);

        return back()->with('success', __('Signing link resent.'));
    }
}
