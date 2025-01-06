<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception): JsonResponse
    {
        Log::error('エラーが発生しました: ' . $exception->getMessage(), [
            'exception' => $exception,
        ]);

        // Validationエラーの場合
        if ($exception instanceof ValidationException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $exception->errors(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 認証エラーの場合
        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'status' => 'error',
                'message' => '認証に失敗しました。',
                'errors' => ['auth' => [$exception->getMessage()]],
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // HTTPエラーの場合
        if ($exception instanceof HttpException) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
                'errors' => ['http' => [$exception->getMessage()]],
            ], $exception->getStatusCode());
        }

        // NotFoundの場合
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
                'errors' => ['notFound' => [$exception->getMessage()]],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Internal Server Error',
            'errors' => ['server' => ['サーバーエラーが発生しました。']],
        ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}
