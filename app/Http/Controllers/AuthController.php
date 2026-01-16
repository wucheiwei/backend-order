<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * 會員註冊
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails()) {
            return $this->validationError($validator->errors(), '註冊驗證失敗');
        }

        try {
            // 前端傳送明碼密碼，後端用 base64 編碼後儲存到資料庫
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => base64_encode($request->password), // 用 base64 編碼後儲存
            ]);

            $token = JWTAuth::fromUser($user);

            return $this->success([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ], '註冊成功');
        } catch (\Exception $e) {
            return $this->serverError('註冊失敗：' . $e->getMessage());
        }
    }

    /**
     * 會員登入
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors(), '登入驗證失敗');
        }

        try {
            // 查詢用戶
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return $this->error('帳號或密碼錯誤', 401);
            }

            // 前端傳送明碼密碼，後端用 base64 編碼後與資料庫中的 base64 密碼比對
            $encodedPassword = base64_encode($request->password);
            if ($user->password !== $encodedPassword) {
                return $this->error('帳號或密碼錯誤', 401);
            }

            // 生成 JWT Token
            $token = JWTAuth::fromUser($user);

            return $this->success([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ], '登入成功');
        } catch (\Exception $e) {
            return $this->serverError('登入失敗：' . $e->getMessage());
        }
    }

    /**
     * 取得當前登入會員資訊
     */
    public function me()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return $this->unauthorized('未登入');
            }

            return $this->success([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ], '取得會員資訊成功');
        } catch (\Exception $e) {
            return $this->serverError('取得會員資訊失敗：' . $e->getMessage());
        }
    }

    /**
     * 登出
     */
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return $this->success([], '登出成功');
        } catch (\Exception $e) {
            return $this->error('登出失敗：' . $e->getMessage(), 500);
        }
    }

    /**
     * 刷新 Token
     */
    public function refresh()
    {
        try {
            $token = JWTAuth::refresh(JWTAuth::getToken());

            return $this->success([
                'token' => $token,
                'token_type' => 'Bearer',
            ], 'Token 刷新成功');
        } catch (\Exception $e) {
            return $this->unauthorized('Token 刷新失敗');
        }
    }
}

