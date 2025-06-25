<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Services\ExceptionHandlerService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Exceptions\ValidationApiException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Cache\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
Use Illuminate\Validation\ValidationException;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Exception Handler Service'i çözümle
        $exceptionHandler = app(ExceptionHandlerService::class);

        // Reporting - Hataları loglama
        $exceptions->reportable(function (Throwable $e) {
            // Kritik hataları özel bir şekilde raporla
            if ($e instanceof BaseApiException) {
                Log::channel('api')->error('Custom API Exception', [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'code' => $e->getErrorCode(),
                    'status' => $e->getStatusCode(),
                    'errors' => $e->getErrors(),
                    'user_id' => Auth::user()->id ?: null,
                    'timestamp' => now(),
                ]);
            }
        });

        // Rendering - Hataları API response'a dönüştürme
        $exceptions->renderable(function (Throwable $e, Request $request) use ($exceptionHandler) {
            // Sadece API istekleri için özel handling
            if ($request->expectsJson() || $request->is('api/*')) {
                return $exceptionHandler->handleApiException($e, $request);
            }

            // Web istekleri için default handling
            return null;
        });

        // Özel exception'lar için rate limiting
        $exceptions->throttle(function (Throwable $e) {
            if ($e instanceof ValidationApiException) {
                return Limit::perMinute(10)->by(request()->ip());
            }

            return Limit::none();
        });

        // Development ortamında daha detaylı hata gösterimi
        if (app()->environment('local', 'testing')) {
            $exceptions->dontReport([
                ValidationException::class,
                AuthenticationException::class,
                ValidationApiException::class,
            ]);
        }
    })->create();
