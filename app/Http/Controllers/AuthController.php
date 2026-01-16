<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * 會員註冊
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        try {
            $data = $this->authService->register($request->validated());

            return $this->success($data, '註冊成功');
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            if ($code === 401 || $code === 422) {
                return $this->error($e->getMessage(), $code);
            }
            return $this->serverError('註冊失敗：' . $e->getMessage());
        }
    }

    /**
     * 會員登入
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        try {
            $data = $this->authService->login(
                $request->input('email'),
                $request->input('password')
            );

            return $this->success($data, '登入成功');
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            if ($code === 401) {
                return $this->error($e->getMessage(), $code);
            }
            return $this->serverError('登入失敗：' . $e->getMessage());
        }
    }

    /**
     * 取得當前登入會員資訊
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        try {
            $user = $this->authService->getCurrentUser();

            return $this->success($user, '取得會員資訊成功');
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            if ($code === 401) {
                return $this->unauthorized($e->getMessage());
            }
            return $this->serverError('取得會員資訊失敗：' . $e->getMessage());
        }
    }

    /**
     * 登出
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            $this->authService->logout();

            return $this->success([], '登出成功');
        } catch (\Exception $e) {
            return $this->error('登出失敗：' . $e->getMessage(), 500);
        }
    }

    /**
     * 刷新 Token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            $data = $this->authService->refreshToken();

            return $this->success($data, 'Token 刷新成功');
        } catch (\Exception $e) {
            return $this->unauthorized('Token 刷新失敗');
        }
    }
}

