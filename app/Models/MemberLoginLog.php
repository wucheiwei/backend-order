<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberLoginLog extends Model
{
    use HasFactory;

    protected $table = 'member_login_logs';

    protected $fillable = [
        'user_id',
        'login_time',
        'logout_time',
    ];

    protected $casts = [
        'login_time' => 'datetime',
        'logout_time' => 'datetime',
    ];

    /**
     * 禁用自動時間戳記（因為我們使用 login_time 和 logout_time）
     */
    public $timestamps = false;

    /**
     * 關聯到 User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
