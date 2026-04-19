<?php

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->use([
            \App\Http\Middleware\TrustProxies::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            \App\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'localize' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localeCookieRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
            'localeViewPath' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'set.locale.from.header' => \App\Http\Middleware\SetLocaleFromHeader::class,
            'admin.not_blocked' => \App\Http\Middleware\EnsureAdminIsNotBlocked::class,
            'api.account.access' => \App\Http\Middleware\EnsureApiUserHasAccess::class,
        ]);

        $middleware->throttleApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return responseJson(
                    msg: __('apis.the_given_data_was_invalid'),
                    code: $e->status,
                    error: true,
                    errors: $e->errors(),
                );
            }

            return null;
        });

        $exceptions->render(function (QueryException $exception, Request $request) {
            if ($request->is('api/*')) {
                return responseJson(
                    msg: $exception->getMessage(),
                    code: ResponseAlias::HTTP_BAD_REQUEST,
                    error: true,
                    errors: ['line' => $exception->getLine(), 'file' => $exception->getFile()],
                );
            }

            return null;
        });

        $exceptions->render(function (\TypeError $exception, Request $request) {
            if ($request->is('api/*')) {
                return responseJson(
                    msg: $exception->getMessage(),
                    code: ResponseAlias::HTTP_BAD_REQUEST,
                    error: true,
                    errors: ['line' => $exception->getLine(), 'file' => $exception->getFile()],
                );
            }

            return null;
        });

        $exceptions->render(function (\ErrorException $exception, Request $request) {
            if ($request->is('api/*')) {
                return responseJson(
                    msg: $exception->getMessage(),
                    code: ResponseAlias::HTTP_BAD_REQUEST,
                    error: true,
                    errors: ['line' => $exception->getLine(), 'file' => $exception->getFile()],
                );
            }

            return null;
        });

        $exceptions->render(function (ModelNotFoundException $exception, Request $request) {
            if ($request->is('api/*')) {
                return responseJson(
                    msg: $exception->getMessage() ?: 'Model Not Found',
                    code: ResponseAlias::HTTP_NOT_FOUND,
                    error: true,
                    errors: ['line' => $exception->getLine(), 'file' => $exception->getFile()],
                );
            }

            return null;
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if ($request->is('api/*')) {
                return responseJson(
                    msg: $exception->getMessage() ?: 'Not Found',
                    code: ResponseAlias::HTTP_NOT_FOUND,
                    error: true,
                    errors: ['line' => $exception->getLine(), 'file' => $exception->getFile()],
                );
            }

            return null;
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return responseJson(
                    msg: trans('auth.unauthenticated'),
                    code: ResponseAlias::HTTP_UNAUTHORIZED,
                    error: true,
                    key: 'unauthenticated',
                );
            }

            return null;
        });

        $exceptions->render(function (\ParseError $exception, Request $request) {
            if ($request->is('api/*')) {
                return responseJson(
                    msg: $exception->getMessage(),
                    code: ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                    error: true,
                    errors: ['line' => $exception->getLine(), 'file' => $exception->getFile()],
                );
            }

            return null;
        });

        $exceptions->render(function (\Error $exception, Request $request) {
            if ($request->is('api/*')) {
                return responseJson(
                    msg: $exception->getMessage(),
                    code: ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                    error: true,
                    errors: ['line' => $exception->getLine(), 'file' => $exception->getFile()],
                );
            }

            return null;
        });

        $exceptions->render(function (AccessDeniedHttpException $exception, Request $request) {
            if ($request->is('api/*')) {
                return responseJson(
                    msg: __('apis.have_no_permission'),
                    code: ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                    error: true,
                    errors: ['line' => $exception->getLine(), 'file' => $exception->getFile()],
                );
            }

            return null;
        });

        $exceptions->render(function (UnauthorizedException $e, Request $request) {
            if ($request->has('draw')) {
                return response()->json([
                    'draw' => (int) $request->input('draw', 0),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'Unauthorized: '.$e->getMessage(),
                ], 403);
            }
            if ($request->is('api/*') || $request->expectsJson()) {
                return responseJson(
                    msg: __('apis.have_no_permission'),
                    code: ResponseAlias::HTTP_FORBIDDEN,
                    error: true,
                );
            }
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => $e->getMessage(),
                ], 403);
            }

            return null;
        });

        $exceptions->render(function (\Exception $exception, Request $request) {
            if ($request->is('api/*')) {
                return responseJson(
                    msg: $exception->getMessage(),
                    code: ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                    error: true,
                    errors: ['line' => $exception->getLine(), 'file' => $exception->getFile()],
                );
            }

            return null;
        });

        $exceptions->render(function (\Throwable $exception, Request $request) {
            if ($request->is('api/*')) {
                return responseJson(
                    msg: $exception->getMessage(),
                    code: ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                    error: true,
                    errors: ['line' => $exception->getLine(), 'file' => $exception->getFile()],
                );
            }

            return null;
        });
    })
    ->create();
