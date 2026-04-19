<?php

namespace App\Http\Middleware;

use App\Services\Auth\AccountAccessService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminIsNotBlocked
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return $next($request);
        }

        if (! $admin->is_blocked) {
            return $next($request);
        }

        app(AccountAccessService::class)->revokeAdminAccess($admin);

        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.login')
            ->with('error', __('auth.admin_blocked'));
    }
}
