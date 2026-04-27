<?php

namespace App\Http\Controllers\Settings;

use App\Actions\Settings\ResolveSessionInfo;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SessionsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
            new Middleware('verified'),
            new Middleware('password.confirm', only: ['destroy', 'destroyAll']),
        ];
    }

    public function index(Request $request, ResolveSessionInfo $resolver): JsonResponse
    {
        $currentSessionId = $request->session()->getId();

        $sessions = DB::table('sessions')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('last_activity')
            ->get()
            ->map(function ($session) use ($currentSessionId, $resolver) {
                $info = $resolver->resolve($session->user_agent, $session->ip_address);

                return [
                    'id' => $session->id,
                    'agent' => $info['agent'],
                    'ip_address' => $session->ip_address,
                    'location' => $info['location'],
                    'last_activity' => $session->last_activity,
                    'last_activity_diff' => $this->formatLastActivity($session->last_activity),
                    'is_current' => $session->id === $currentSessionId,
                ];
            });

        return response()->json($sessions);
    }

    public function destroy(Request $request, string $sessionId): JsonResponse
    {
        $userId = $request->user()->id;

        $session = DB::table('sessions')
            ->where('id', $sessionId)
            ->where('user_id', $userId)
            ->first();

        if (! $session) {
            return response()->json(['message' => 'Session not found.'], 404);
        }

        if ($session->id === $request->session()->getId()) {
            return response()->json(['message' => 'Cannot revoke the current session.'], 422);
        }

        DB::table('sessions')->where('id', $sessionId)->delete();

        return response()->json(['message' => 'Session revoked successfully.']);
    }

    public function destroyAll(Request $request): JsonResponse
    {
        $currentSessionId = $request->session()->getId();
        $userId = $request->user()->id;

        $deleted = DB::table('sessions')
            ->where('user_id', $userId)
            ->where('id', '!=', $currentSessionId)
            ->delete();

        return response()->json([
            'message' => 'All other sessions have been logged out.',
            'revoked_count' => $deleted,
        ]);
    }

    protected function formatLastActivity(int $timestamp): string
    {
        $lastActivity = Carbon::createFromTimestamp($timestamp);
        $diff = $lastActivity->diffInSeconds(now());

        if ($diff < 60) {
            return 'justNow';
        }

        if ($diff < 3600) {
            return trans_choice('{0} minutes ago|{1} :count minute ago|[2,*] :count minutes ago', (int) floor($diff / 60));
        }

        if ($diff < 86400) {
            return trans_choice('{0} hours ago|{1} :count hour ago|[2,*] :count hours ago', (int) floor($diff / 3600));
        }

        return trans_choice('{0} days ago|{1} :count day ago|[2,*] :count days ago', (int) floor($diff / 86400));
    }
}
