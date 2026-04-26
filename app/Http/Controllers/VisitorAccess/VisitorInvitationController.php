<?php

namespace App\Http\Controllers\VisitorAccess;

use App\Http\Controllers\Controller;
use App\Http\Requests\VisitorAccess\StoreVisitorInvitationRequest;
use App\Models\VisitorAccessSetting;
use App\Models\VisitorInvitation;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class VisitorInvitationController extends Controller
{
    /**
     * Display the resident's visitor invitations (active + past).
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $activeInvitations = VisitorInvitation::query()
            ->where('resident_id', $user->id)
            ->where('status', 'active')
            ->orderBy('expected_at', 'asc')
            ->get()
            ->map(fn (VisitorInvitation $invitation): array => $this->formatInvitation($invitation));

        $pastInvitations = VisitorInvitation::query()
            ->where('resident_id', $user->id)
            ->whereIn('status', ['used', 'expired', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(fn (VisitorInvitation $invitation): array => $this->formatInvitation($invitation));

        return Inertia::render('visitor-access/MyVisitors', [
            'activeInvitations' => $activeInvitations,
            'pastInvitations' => $pastInvitations,
        ]);
    }

    /**
     * Show the visitor registration form.
     */
    public function create(): Response
    {
        return Inertia::render('visitor-access/Register');
    }

    /**
     * Create a new visitor invitation and redirect to QR confirmation.
     */
    public function store(StoreVisitorInvitationRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $expectedAt = new \DateTime($validated['expected_at']);
        $qrExpiryMinutes = $this->resolveQrExpiryMinutes();
        $validUntil = (clone $expectedAt)->modify("+{$qrExpiryMinutes} minutes");

        $invitation = VisitorInvitation::create([
            'resident_id' => $user->id,
            'visitor_name' => $validated['visitor_name'],
            'visitor_phone' => $validated['visitor_phone'] ?? null,
            'visitor_purpose' => $validated['visitor_purpose'],
            'expected_at' => $expectedAt,
            'valid_until' => $validUntil,
            'status' => 'active',
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('app.visitorAccess.myVisitors.invitationCreated')]);

        return redirect()->route('visitor-access.invitations.show', $invitation);
    }

    /**
     * Show the QR code confirmation for an invitation.
     */
    public function show(Request $request, VisitorInvitation $visitorInvitation): Response
    {
        Gate::authorize('view', $visitorInvitation);

        return Inertia::render('visitor-access/Show', [
            'invitation' => [
                ...$this->formatInvitation($visitorInvitation),
                'qr_svg' => $this->generateQrSvg($visitorInvitation->qr_code_token),
            ],
        ]);
    }

    /**
     * Cancel an active visitor invitation.
     */
    public function cancel(Request $request, VisitorInvitation $visitorInvitation): RedirectResponse
    {
        Gate::authorize('cancel', $visitorInvitation);

        $visitorInvitation->update(['status' => 'cancelled']);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('app.visitorAccess.myVisitors.cancelSuccess')]);

        return redirect()->route('visitor-access.invitations.index');
    }

    /**
     * Format a VisitorInvitation for the frontend.
     *
     * @return array<string, mixed>
     */
    private function formatInvitation(VisitorInvitation $invitation): array
    {
        return [
            'id' => $invitation->id,
            'visitor_name' => $invitation->visitor_name,
            'visitor_phone' => $invitation->visitor_phone,
            'visitor_purpose' => $invitation->visitor_purpose,
            'expected_at' => $invitation->expected_at?->toISOString(),
            'valid_until' => $invitation->valid_until?->toISOString(),
            'status' => $invitation->status,
            'qr_code_token' => $invitation->qr_code_token,
        ];
    }

    /**
     * Generate an SVG QR code for the given token and return it as a base64 data URL.
     */
    private function generateQrSvg(string $token): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd,
        );

        $writer = new Writer($renderer);
        $svg = $writer->writeString($token);

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    /**
     * Resolve the QR expiry duration (in minutes).
     *
     * Falls back to 1440 minutes (24 hours) if no community setting exists.
     */
    private function resolveQrExpiryMinutes(): int
    {
        return VisitorAccessSetting::query()->value('qr_expiry_minutes') ?? 1440;
    }
}
