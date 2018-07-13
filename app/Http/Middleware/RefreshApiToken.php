<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Tymon\JWTAuth\Facades\JWTAuth;

class RefreshApiToken
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
        $response = $next($request);

        $token = Cookie::get('aming_token');
        try {
            JWTAuth::toUser(Cookie::get('aming_token'));
        } catch (TokenExpiredException $e) {
            Config::package('tymon/jwt-auth', 'jwt');
            $ttl = Config::get('jwt::refresh_ttl');

            $iat = \Carbon\Carbon::createFromTimestamp($token->getPayload()->get('iat'));
            $now = \Carbon\Carbon::now();

            if ($iat->diffInMinutes($now) < $ttl) {
                // 刷新api token
                $token = JWTAuth::refresh(Cookie::get('aming_token'));
                $response->withCookie('aming_token', $token, config('jwt.ttl'), "/", null, false, true);
            }
        }
//        $response->headers->set('Authorization', 'Bearer '.$token);
        return $response;
    }
}
