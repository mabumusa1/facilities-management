<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\DocumentRecord;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SigningController extends Controller
{
    public function show(string $token): Response|JsonResponse
    {
        $record = DocumentRecord::with('templateVersion')->where('signing_token', $token)->first();

        if (! $record) {
            abort(404, 'Invalid or expired signing link.');
        }

        if ($record->status === 'signed') {
            return Inertia::render('documents/SigningDone', [
                'record' => ['id' => $record->id, 'status' => 'signed'],
            ]);
        }

        if ($record->sent_at && $record->sent_at->diffInDays(now()) > 7) {
            $record->update(['status' => 'link_expired']);

            abort(410, 'This signing link has expired (7 days). Please contact the sender for a new link.');
        }

        $body = json_decode($record->templateVersion?->body, true);

        return Inertia::render('documents/Signing', [
            'record' => [
                'id' => $record->id,
                'signing_token' => $record->signing_token,
                'status' => $record->status,
                'body' => $body,
                'signer_name' => $record->signatures->first()?->signer_name ?? '',
            ],
        ]);
    }

    public function requestOtp(Request $request, string $token): JsonResponse
    {
        $record = DocumentRecord::where('signing_token', $token)->firstOrFail();

        if ($record->status !== 'sent') {
            return response()->json(['error' => 'Document is not in signable state.'], 422);
        }

        $signature = $record->signatures->first();

        if (! $signature || ! $signature->signer_email) {
            return response()->json(['error' => 'No signature recipient found.'], 422);
        }

        $otpService = new OtpService;
        $otp = $otpService->generate("sign:{$record->id}");

        return response()->json([
            'message' => 'OTP sent to signer.',
            'otp' => app()->environment('testing') ? $otp : null,
        ]);
    }

    public function sign(Request $request, string $token): JsonResponse
    {
        $record = DocumentRecord::with('signatures')->where('signing_token', $token)->firstOrFail();

        if ($record->status !== 'sent') {
            return response()->json(['error' => 'Document is not in signable state.'], 422);
        }

        $validated = $request->validate([
            'otp' => ['required', 'string', 'size:6'],
            'signer_name' => ['required', 'string', 'max:255'],
        ]);

        $otpService = new OtpService;

        if (! $otpService->verify("sign:{$record->id}", $validated['otp'])) {
            return response()->json(['error' => 'Invalid or expired OTP. Please request a new one.'], 422);
        }

        $signature = $record->signatures->first();

        if ($signature) {
            $signature->update([
                'signer_name' => $validated['signer_name'],
                'signed_at' => now(),
                'ip_address' => $request->ip(),
                'otp_verified_at' => now(),
                'signed_file_path' => $record->file_path,
            ]);
        }

        $record->update(['status' => 'signed']);

        return response()->json([
            'message' => 'Document signed successfully.',
            'record' => ['id' => $record->id, 'status' => 'signed'],
        ]);
    }
}
