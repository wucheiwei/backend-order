<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\MemberLoginLogRepository;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    protected $userRepository;
    protected $loginLogRepository;

    public function __construct(
        UserRepository $userRepository,
        MemberLoginLogRepository $loginLogRepository
    ) {
        $this->userRepository = $userRepository;
        $this->loginLogRepository = $loginLogRepository;
    }

    /**
     * 註冊新用戶
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function register(array $data): array
    {
        // 檢查 Email 是否已存在
        if ($this->userRepository->emailExists($data['email'])) {
            throw new \Exception('此 Email 已被使用', 422);
        }

        // 將密碼用 base64 編碼
        $data['password'] = base64_encode($data['password']);

        // 創建用戶
        $user = $this->userRepository->create($data);

        // 生成 JWT Token
        $token = JWTAuth::fromUser($user);

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * 用戶登入
     *
     * @param string $email
     * @param string $password
     * @return array
     * @throws \Exception
     */
    public function login(string $email, string $password): array
    {
        // 查詢用戶
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new \Exception('帳號或密碼錯誤', 401);
        }

        // 驗證密碼（前端傳送明碼，後端用 base64 編碼後比對）
        $encodedPassword = base64_encode($password);
        if ($user->password !== $encodedPassword) {
            throw new \Exception('帳號或密碼錯誤', 401);
        }

        // 生成 JWT Token
        $token = JWTAuth::fromUser($user);

        // 記錄登入時間（如果該 user_id 沒有記錄就創建，有記錄就更新）
        $this->loginLogRepository->createOrUpdateLoginLog($user->id);

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * 取得當前登入用戶資訊
     *
     * @return array
     * @throws \Exception
     */
    public function getCurrentUser(): array
    {
        $user = auth()->user();

        if (!$user) {
            throw new \Exception('未登入', 401);
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }

    /**
     * 用戶登出
     *
     * @return void
     * @throws \Exception
     */
    public function logout(): void
    {
        $user = auth()->user();

        if ($user) {
            // 更新登出時間
            $this->loginLogRepository->updateLogoutTime($user->id);
        }

        // 使 Token 失效
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    /**
     * 刷新 Token
     *
     * @return array
     * @throws \Exception
     */
    public function refreshToken(): array
    {
        try {
            $token = JWTAuth::refresh(JWTAuth::getToken());

            return [
                'token' => $token,
                'token_type' => 'Bearer',
            ];
        } catch (\Exception $e) {
            throw new \Exception('Token 刷新失敗', 401);
        }
    }
}

