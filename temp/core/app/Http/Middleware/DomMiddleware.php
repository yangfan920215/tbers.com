<?php

namespace App\Http\Middleware;

use Closure;
use Response;

class DomMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure &$next)
    {
        $response = $next($request);
        // 可以指定域名允许跨域，例如http://laravel.test，如果要支持多个域名，可以先逻辑判断访问者是否在允许列表，然后动态加载
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, Accept');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS');
        $response->header('Access-Control-Allow-Credentials', 'true');
        return $response;
    }
}
