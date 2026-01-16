<?php

namespace App\Http\Middleware;

use App\Http\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JwtAuthMiddleware
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // 從 Header 取得 Bearer Token
            $token = $request->bearerToken();

            if (!$token) {
                return $this->unauthorized('未提供 Token');
            }

            // 驗證 Token
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return $this->unauthorized('用戶不存在');
            }

            // 將用戶資訊附加到 request
            $request->merge(['user' => $user]);
            auth()->setUser($user);

        } catch (TokenExpiredException $e) {
            return $this->unauthorized('Token 已過期');
        } catch (TokenInvalidException $e) {
            return $this->unauthorized('Token 無效');
        } catch (JWTException $e) {
            return $this->unauthorized('Token 解析失敗');
        } catch (\Exception $e) {
            return $this->unauthorized('認證失敗');
        }

        return $next($request);
    }
}

