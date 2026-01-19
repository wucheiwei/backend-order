<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// 測試 API 連通性（不需要認證）
Route::get('/test', function (Request $request) {
    return response()->json([
        'code' => 200,
        'is_success' => true,
        'message' => 'API 連通測試成功',
        'data' => [
            'timestamp' => now()->toDateTimeString(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
        ],
    ]);
});

// 會員認證相關路由（不需要認證）
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']); // 註冊
    Route::post('/login', [AuthController::class, 'login']); // 登入
});

// 需要 JWT 認證的路由
Route::middleware('jwt.auth')->group(function () {
    // 會員相關路由
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']); // 取得當前會員資訊
        Route::post('/logout', [AuthController::class, 'logout']); // 登出
        Route::post('/refresh', [AuthController::class, 'refresh']); // 刷新 Token
    });

    // Store 相關路由
    Route::prefix('stores')->group(function () {
        Route::get('/', [StoreController::class, 'index']); // 取得所有類別（分頁）
        Route::get('/{id}', [StoreController::class, 'show']); // 取得單一類別
        Route::post('/', [StoreController::class, 'store']); // 批量創建類別
        Route::put('/', [StoreController::class, 'update']); // 批量更新類別
        Route::delete('/{id}', [StoreController::class, 'destroy']); // 刪除類別（軟刪除）
    });
});
