<?php

namespace App\Services;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\ValidationApiException;
use BaseApiException;
class ExceptionHandlerService
{
    use ApiResponse;

    public function handleApiException(Throwable $exception, Request $request): JsonResponse
    {
        // Log kritik hataları
        $this->logException($exception, $request);

        // Özel exception'larımızı handle et
        if ($exception instanceof BaseApiException) {
            return $this->handleCustomException($exception);
        }

        // Laravel'in built-in exception'larını handle et
        return match (true) {
            $exception instanceof ValidationException => $this->handleValidationException($exception),
            $exception instanceof ModelNotFoundException => $this->handleModelNotFoundException($exception),
            $exception instanceof AuthenticationException => $this->handleAuthenticationException($exception),
            $exception instanceof AuthorizationException => $this->handleAuthorizationException($exception),
            $exception instanceof NotFoundHttpException => $this->handleNotFoundHttpException($exception),
            $exception instanceof MethodNotAllowedHttpException => $this->handleMethodNotAllowedHttpException($exception),
            $exception instanceof ThrottleRequestsException => $this->handleThrottleException($exception),
            $exception instanceof TokenMismatchException => $this->handleTokenMismatchException($exception),
            $exception instanceof QueryException => $this->handleQueryException($exception),
            default => $this->handleGenericException($exception)
        };
    }

    protected function handleCustomException(BaseApiException $exception): JsonResponse
    {
        return $this->error(
            message: $exception->getMessage() ?: 'An error occurred',
            statusCode: $exception->getStatusCode(),
            errors: $exception->getErrors(),
            errorCode: $exception->getErrorCode()
        );
    }

    protected function handleValidationException(ValidationException $exception): JsonResponse
    {
        return $this->error(
            message: 'Validation failed',
            statusCode: 422,
            errors: $exception->errors(),
            errorCode: 'VALIDATION_ERROR'
        );
    }

    protected function handleModelNotFoundException(ModelNotFoundException $exception): JsonResponse
    {
        $model = class_basename($exception->getModel());
        return $this->error(
            message: "{$model} not found",
            statusCode: 404,
            errorCode: 'RESOURCE_NOT_FOUND'
        );
    }

    protected function handleAuthenticationException(AuthenticationException $exception): JsonResponse
    {
        return $this->error(
            message: 'Authentication required',
            statusCode: 401,
            errorCode: 'UNAUTHENTICATED'
        );
    }

    protected function handleAuthorizationException(AuthorizationException $exception): JsonResponse
    {
        return $this->error(
            message: 'Access denied',
            statusCode: 403,
            errorCode: 'FORBIDDEN'
        );
    }

    protected function handleNotFoundHttpException(NotFoundHttpException $exception): JsonResponse
    {
        return $this->error(
            message: 'Endpoint not found',
            statusCode: 404,
            errorCode: 'ENDPOINT_NOT_FOUND'
        );
    }

    protected function handleMethodNotAllowedHttpException(MethodNotAllowedHttpException $exception): JsonResponse
    {
        return $this->error(
            message: 'Method not allowed',
            statusCode: 405,
            errorCode: 'METHOD_NOT_ALLOWED'
        );
    }

    protected function handleThrottleException(ThrottleRequestsException $exception): JsonResponse
    {
        return $this->error(
            message: 'Too many requests',
            statusCode: 429,
            errorCode: 'RATE_LIMIT_EXCEEDED'
        );
    }

    protected function handleTokenMismatchException(TokenMismatchException $exception): JsonResponse
    {
        return $this->error(
            message: 'CSRF token mismatch',
            statusCode: 419,
            errorCode: 'TOKEN_MISMATCH'
        );
    }

    protected function handleQueryException(QueryException $exception): JsonResponse
    {
        // Production'da database hatalarını gizle
        if (app()->environment('production')) {
            return $this->error(
                message: 'Database error occurred',
                statusCode: 500,
                errorCode: 'DATABASE_ERROR'
            );
        }

        return $this->error(
            message: 'Database error: ' . $exception->getMessage(),
            statusCode: 500,
            errorCode: 'DATABASE_ERROR'
        );
    }

    protected function handleGenericException(Throwable $exception): JsonResponse
    {
        // Production'da detaylı hata mesajlarını gizle
        if (app()->environment('production')) {
            return $this->error(
                message: 'Internal server error',
                statusCode: 500,
                errorCode: 'INTERNAL_ERROR'
            );
        }

        // Development'ta detaylı bilgi ver
        return $this->error(
            message: $exception->getMessage(),
            statusCode: 500,
            errors: [
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => collect($exception->getTrace())->take(10)->toArray()
            ],
            errorCode: 'INTERNAL_ERROR'
        );
    }

    protected function logException(Throwable $exception, Request $request): void
    {
        // Kritik hataları logla
        if ($this->shouldLog($exception)) {
            Log::error('API Exception occurred', [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_id' => Auth::user()->id ?: null,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toISOString(),
            ]);
        }
    }

    protected function shouldLog(Throwable $exception): bool
    {
        // Bu exception'ları loglama
        $dontLog = [
            ValidationException::class,
            AuthenticationException::class,
            NotFoundHttpException::class,
            ValidationApiException::class,
        ];

        return !in_array(get_class($exception), $dontLog);
    }
}
