<?php

use Carbon\Exceptions\InvalidCastException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->use([
            // \Illuminate\Http\Middleware\TrustHosts::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            \App\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ]);
        $middleware->group('api', [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\JsonMiddleware::class,
        ]);
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function(AuthenticationException $e, Request $request){
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 401);
        });

        $exceptions->renderable(function (NotFoundHttpException $e, Request $request) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ruta no encontrada'
                ], 404);
        });

        $exceptions->renderable(function (QueryException $e, Request $request) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 404);
        });

        $exceptions->renderable(function (BadMethodCallException $e, Request $request) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        });

        $exceptions->renderable(function (MethodNotAllowedHttpException $e, Request $request) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        });

        $exceptions->renderable(function (BindingResolutionException $e, Request $request) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        });

        $exceptions->renderable(function (PostTooLargeException $e, Request $request) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 413);
        });

        $exceptions->renderable(function (AccessDeniedHttpException $e, Request $request) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 401);
        });

        $exceptions->renderable(function (JWTException $e, Request $request) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 403);
        });

        $exceptions->renderable(function (TokenInvalidException $e, Request $request) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 401);
        });

        $exceptions->renderable(function (InvalidCastException $e, Request $request) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        });

        $exceptions->renderable(function (UnauthorizedException $e, Request $request) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 401);
        });

        $exceptions->renderable(function (TokenExpiredException $e, Request $request) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 401);
        });

        $exceptions->renderable(function (ValidationException $e, Request $request) {
            $collection = collect($e->errors());
            return response()->json([
                'errors' => $collection->keys()->first(),
                'message' => $collection->flatten()->first(),
            ], 422);
        });
    })->create();
