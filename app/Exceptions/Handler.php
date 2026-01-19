<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * 處理驗證異常，確保 API 請求返回 JSON 格式
     *
     * @param \Illuminate\Http\Request $request
     * @param ValidationException $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function invalid($request, ValidationException $exception)
    {
        // 如果是 API 請求（/api/* 路由），返回 JSON 格式
        if ($request->is('api/*') || $request->expectsJson()) {
            return $this->invalidJson($request, $exception);
        }

        return parent::invalid($request, $exception);
    }

    /**
     * 處理驗證異常的 JSON 回應
     *
     * @param \Illuminate\Http\Request $request
     * @param ValidationException $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json([
            'code' => 422,
            'is_success' => false,
            'message' => '驗證失敗',
            'data' => [
                'errors' => $exception->errors(),
            ],
        ], 422);
    }
}
