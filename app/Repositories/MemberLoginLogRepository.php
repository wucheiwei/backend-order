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
     * 創建或更新登入記錄
     * 如果該 user_id 沒有記錄，就創建新的記錄
     * 如果該 user_id 已經存在，就更新現有記錄
     *
     * @param int $userId
     * @return MemberLoginLog
     */
    public function createOrUpdateLoginLog(int $userId): MemberLoginLog
    {
        return $this->model->updateOrCreate(
            ['user_id' => $userId],
            [
                'login_time' => now(),
                'logout_time' => null,
            ]
        );
    }

    /**
     * 更新登出時間
     * 更新該 user_id 的記錄的 logout_time
     *
     * @param int $userId
     * @return bool
     */
    public function updateLogoutTime(int $userId): bool
    {
        $loginLog = $this->model->where('user_id', $userId)->first();

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

