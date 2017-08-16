<?php

namespace App\Http\Middleware;

use Closure;
use libs\accessToken;

class TokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 参数验证
        $token = null !== $request->input('token') ? $request->input('token') : '';

        if ($token === ''){
            return response(retJson(2, [], '登录失败!'), 200);
        }

        $user = accessToken::checkToken($token);
        if($user){
            $request->token_to_user = json_decode($user);
            return $next($request);
        }
        return response(retJson(2, [], '登录失败!'), 200);
    }
}
