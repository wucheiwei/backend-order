<?php

namespace App\Repositories;

use App\Models\MemberLoginLog;

class MemberLoginLogRepository
{
    protected $model;

    public function __construct(MemberLoginLog $model)
    {
        $this->model = $model;
    }

    /**
     * 創建登入記錄
     *
     * @param int $userId
     * @return MemberLoginLog
     */
    public function createLoginLog(int $userId): MemberLoginLog
    {
        return $this->model->create([
            'user_id' => $userId,
            'login_time' => now(),
            'logout_time' => null,
        ]);
    }

    /**
     * 更新登出時間
     *
     * @param int $userId
     * @return bool
     */
    public function updateLogoutTime(int $userId): bool
    {
        $loginLog = $this->model
            ->where('user_id', $userId)
            ->whereNull('logout_time')
            ->latest('login_time')
            ->first();

        if ($loginLog) {
            return $loginLog->update([
                'logout_time' => now(),
            ]);
        }

        return false;
    }

    /**
     * 取得用戶的登入記錄
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserLoginLogs(int $userId, int $limit = 10)
    {
        return $this->model
            ->where('user_id', $userId)
            ->orderBy('login_time', 'desc')
            ->limit($limit)
            ->get();
    }
}

