<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class authJWT
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
//        try {
//            //原来通过token
////            if (! $user = JWTAuth::parseToken()->authenticate()) {
////                return response()->json(['user_not_found'], 404);
////            }
//            // 如果用户登陆后的所有请求没有jwt的token抛出异常
//            if (!$user = JWTAuth::toUser(Cookie::get('aming_token'))) {
//                return response()->json(['user_not_found'], 404);
//            }
//            } catch (Exception $e) {
//            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
//                return response()->json(['error'=>'Token 无效', 'code'=>'400404'], 404, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
//            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
//                return response()->json(['error'=>'登录过期，请重新登录', 'code'=>'400404'], 404, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
//            }else{
//                return response()->json(['error'=>'账号未登录', 'code'=>'400404'], 404, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
//            }
//        }
        $accessToken = Cookie::get('accessToken');
        if ($accessToken && $accessToken !==''){
            return $next($request);
        } else{
            if (!Cookie::get('aming_token')){
                return response()->json(['msg'=>'token不存在,请重新登录', 'code'=>'404400'], 404, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }
            try {
                if (!$user = JWTAuth::toUser(Cookie::get('aming_token'))) {
                    return response()->json(['user_not_found'], 404);
                }
            } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                return response()->json(['token_expired'], $e->getStatusCode());
            } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                return response()->json(['token_invalid'], $e->getStatusCode());
            } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
                return response()->json(['token_absent'], $e->getStatusCode());
            }
            return $next($request);
        }
    }
}
