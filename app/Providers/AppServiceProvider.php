<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Events\MigrationsStarted;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 限制 migration 只掃描 database/migrations 目錄，排除 vendor 目錄
        $this->app->resolving('migrator', function ($migrator) {
            $migrator->path(database_path('migrations'));
        });
        
        // 監聽 migration 事件，過濾掉不需要的 migration
        Event::listen(MigrationsStarted::class, function () {
            // 從資料庫中刪除 personal_access_tokens migration 記錄（如果存在）
            DB::table('migrations')
                ->where('migration', '2019_12_14_000001_create_personal_access_tokens_table')
                ->delete();
        });
    }
}
