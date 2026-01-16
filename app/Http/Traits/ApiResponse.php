<?php

namespace App\Http\Traits;

trait ApiResponse
{
    /**
     * 成功回應
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($data = [], string $message = '操作成功', int $code = 200)
    {
        return response()->json([
            'code' => $code,
            'is_success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * 錯誤回應
     *
     * @param string $message
     * @param int $code
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error(string $message = '操作失敗', int $code = 400, $data = [])
    {
        return response()->json([
            'code' => $code,
            'is_success' => false,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * 404 錯誤回應
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function notFound(string $message = '資源不存在')
    {
        return $this->error($message, 404);
    }

    /**
     * 422 驗證錯誤回應
     *
     * @param mixed $errors
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function validationError($errors, string $message = '驗證失敗')
    {
        return $this->error($message, 422, ['errors' => $errors]);
    }

    /**
     * 401 未授權回應
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function unauthorized(string $message = '未授權')
    {
        return $this->error($message, 401);
    }

    /**
     * 403 禁止訪問回應
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function forbidden(string $message = '禁止訪問')
    {
        return $this->error($message, 403);
    }

    /**
     * 500 伺服器錯誤回應
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function serverError(string $message = '伺服器錯誤')
    {
        return $this->error($message, 500);
    }
}

