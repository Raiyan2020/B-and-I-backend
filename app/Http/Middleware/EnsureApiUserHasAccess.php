<?php

namespace App\Http\Middleware;

use App\Services\Auth\AccountAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiUserHasAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($user->is_blocked) {
            app(AccountAccessService::class)->revokeUserAccess($user);

            return responseJson(
                msg: __('apis.account_blocked'),
                code: Response::HTTP_FORBIDDEN,
                error: true,
                key: 'account_blocked',
            );
        }

        if (! $user->is_active) {
            app(AccountAccessService::class)->revokeUserAccess($user);

            return responseJson(
                msg: __('apis.account_inactive'),
                code: Response::HTTP_FORBIDDEN,
                error: true,
                key: 'account_inactive',
            );
        }

        return $next($request);
    }
}
