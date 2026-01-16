<?php

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

// 測試 API 連通性
Route::get('/test', function (Request $request) {
    return response()->json([
        'status' => 'success',
        'message' => 'API 連通測試成功',
        'timestamp' => now()->toDateTimeString(),
        'method' => $request->method(),
        'url' => $request->fullUrl(),
    ]);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
